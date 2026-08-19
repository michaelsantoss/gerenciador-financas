<?php

namespace App\Console\Commands;

use App\Models\AdminActivityLog;
use App\Models\Empresa;
use Illuminate\Console\Command;

class VerificarVencimentoPlanos extends Command
{
    protected $signature = 'empresas:verificar-vencimento';

    protected $description = 'Bloqueia automaticamente empresas cujo plano venceu';

    public function handle()
    {
        $empresasVencidas = Empresa::whereDate('data_vencimento', '<', today())
            ->where('status', '!=', Empresa::STATUS_BLOQUEADO)
            ->get();

        foreach ($empresasVencidas as $empresa) {
            $motivo = "Bloqueio automático. Motivo: Vencimento do plano (venceu em {$empresa->data_vencimento->format('d/m/Y')}).";

            $empresa->update([
                'status' => Empresa::STATUS_BLOQUEADO,
                'observacao' => trim(($empresa->observacao ? $empresa->observacao . "\n" : '') . $motivo),
            ]);

            AdminActivityLog::registrar('empresa.bloqueio_automatico', $empresa, "Empresa: {$empresa->nome} | {$motivo}");
        }

        $this->info("{$empresasVencidas->count()} empresa(s) bloqueada(s) por vencimento do plano.");
    }
}
