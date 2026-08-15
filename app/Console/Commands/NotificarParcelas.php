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
                ->with('emprestimo.cliente')
                ->get();

            $atrasadas = Parcela::withoutGlobalScopes()
                ->where('empresa_id', $empresa->id)
                ->where('status', 'atrasado')
                ->with('emprestimo.cliente')
                ->get();

            if ($vencendoHoje->isEmpty() && $atrasadas->isEmpty()) {
                return;
            }

            $porCliente = [];

            foreach ($vencendoHoje as $parcela) {
                $cliente = $parcela->emprestimo?->cliente;
                if (! $cliente) {
                    continue;
                }
                $porCliente[$cliente->id]['cliente'] = $cliente;
                $porCliente[$cliente->id]['vencendoHoje'] = ($porCliente[$cliente->id]['vencendoHoje'] ?? 0) + 1;
                $porCliente[$cliente->id]['totalVencendoHoje'] = ($porCliente[$cliente->id]['totalVencendoHoje'] ?? 0) + (float) $parcela->valor;
            }

            foreach ($atrasadas as $parcela) {
                $cliente = $parcela->emprestimo?->cliente;
                if (! $cliente) {
                    continue;
                }
                $porCliente[$cliente->id]['cliente'] = $cliente;
                $porCliente[$cliente->id]['atrasadas'] = ($porCliente[$cliente->id]['atrasadas'] ?? 0) + 1;
                $porCliente[$cliente->id]['totalAtrasado'] = ($porCliente[$cliente->id]['totalAtrasado'] ?? 0) + (float) $parcela->valor;
            }

            $usuarios = $empresa->usuarios;

            foreach ($porCliente as $dados) {
                $notificacao = new ParcelaVencendoNotification(
                    $dados['cliente']->nome,
                    $dados['vencendoHoje'] ?? 0,
                    $dados['atrasadas'] ?? 0,
                    (float) ($dados['totalVencendoHoje'] ?? 0),
                    (float) ($dados['totalAtrasado'] ?? 0)
                );

                $usuarios->each(fn ($usuario) => $usuario->notify($notificacao));
            }
        });

        $this->info('Notificações enviadas.');
    }
}
