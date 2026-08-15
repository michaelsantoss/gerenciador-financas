<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class ParcelaController extends Controller
{
    /**
     * Quita uma parcela total ou parcialmente.
     */
    public function quitar(Request $request, Parcela $parcela)
    {
        $valorPendente = $parcela->valor_pendente;

        if ($valorPendente <= 0) {
            return response()->json(['message' => 'Parcela já está paga.'], 400);
        }

        $request->validate([
            'valor' => 'nullable|numeric|min:0.01|max:' . $valorPendente,
        ]);

        $valorAplicado = $request->filled('valor') ? (float) $request->input('valor') : $valorPendente;
        $novoValorPago = $parcela->valor_pago + $valorAplicado;
        $quitadaTotalmente = $novoValorPago >= $parcela->valor;

        $parcela->update([
            'valor_pago' => $quitadaTotalmente ? $parcela->valor : $novoValorPago,
            'status' => $quitadaTotalmente ? 'pago' : 'parcial',
            'data_pagamento' => now(),
        ]);

        Pagamento::create([
            'emprestimo_id' => $parcela->emprestimo_id,
            'parcela_id' => $parcela->id,
            'valor_pago' => $valorAplicado,
            'data_pagamento' => now(),
            'observacoes' => ($quitadaTotalmente ? 'Quitação' : 'Pagamento parcial') . " da parcela ID: {$parcela->id}",
        ]);

        return $parcela;
    }

    /**
     * Desfaz o último pagamento registrado para a parcela.
     */
    public function desfazer(Parcela $parcela)
    {
        $ultimoPagamento = $parcela->pagamentos()->latest('data_pagamento')->latest('id')->first();

        if (! $ultimoPagamento) {
            return response()->json(['message' => 'Nenhum pagamento para desfazer nesta parcela.'], 400);
        }

        $ultimoPagamento->delete();

        $valorPago = $parcela->pagamentos()->sum('valor_pago');

        $parcela->update([
            'valor_pago' => $valorPago,
            'status' => $valorPago <= 0 ? 'pendente' : 'parcial',
            'data_pagamento' => $valorPago <= 0 ? null : $parcela->pagamentos()->latest('data_pagamento')->value('data_pagamento'),
        ]);

        return $parcela;
    }
}
