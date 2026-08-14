<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Parcela;
use App\Notifications\ParcelaVencendoNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NotificarParcelas extends Command
{
    protected $signature = 'parcelas:notificar';

    protected $description = 'Marca parcelas vencidas como atrasadas e notifica os usuários sobre parcelas vencendo hoje ou atrasadas';

    public function handle()
    {
        $hoje = Carbon::today();

        $parcelasVencidas = Parcela::withoutGlobalScopes()
            ->where('status', 'pendente')
            ->whereDate('data_vencimento', '<', $hoje)
            ->get();

        foreach ($parcelasVencidas as $parcela) {
            $parcela->update(['status' => 'atrasado']);
            $parcela->emprestimo?->atualizarStatus();
        }

        $this->info("{$parcelasVencidas->count()} parcela(s) marcada(s) como atrasada(s).");

        Empresa::query()->each(function (Empresa $empresa) use ($hoje) {
            $vencendoHoje = Parcela::withoutGlobalScopes()
                ->where('empresa_id', $empresa->id)
                ->where('status', 'pendente')
                ->whereDate('data_vencimento', $hoje)
                ->get();

            $atrasadas = Parcela::withoutGlobalScopes()
                ->where('empresa_id', $empresa->id)
                ->where('status', 'atrasado')
                ->get();

            if ($vencendoHoje->isEmpty() && $atrasadas->isEmpty()) {
                return;
            }

            $notificacao = new ParcelaVencendoNotification(
                $vencendoHoje->count(),
                $atrasadas->count(),
                (float) $vencendoHoje->sum('valor'),
                (float) $atrasadas->sum('valor')
            );

            $empresa->usuarios()->each(fn ($usuario) => $usuario->notify($notificacao));
        });

        $this->info('Notificações enviadas.');
    }
}
