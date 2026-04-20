<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class ParcelaController extends Controller
{
    /**
     * Quita uma parcela individualmente.
     */
    public function quitar(Parcela $parcela)
    {
        if ($parcela->status === 'pago') {
            return response()->json(['message' => 'Parcela já está paga.'], 400);
        }

        $parcela->update(['status' => 'pago']);
        
        Pagamento::create([
            'emprestimo_id' => $parcela->emprestimo_id,
            'valor_pago' => $parcela->valor,
            'data_pagamento' => now(),
            'observacoes' => "Pagamento da parcela ID: {$parcela->id}"
        ]);

        return $parcela;
    }
}
