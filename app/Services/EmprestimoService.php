<?php

namespace App\Services;

use App\Models\Emprestimo;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmprestimoService
{
    /**
     * Cria um novo empréstimo e gera as parcelas.
     */
    public function criar(array $dados)
    {
        return DB::transaction(function () use ($dados) {
            $valorPrincipal = round((float) $dados['valor'], 2);
            $taxa = round((float) ($dados['taxa_juros'] ?? 30.00), 2);
            
            $valorTotal = round($this->calcularJurosSimples($valorPrincipal, $taxa), 2);
            
            $emprestimo = Emprestimo::create([
                'cliente_id' => $dados['cliente_id'],
                'valor' => $valorPrincipal,
                'taxa_juros' => $taxa,
                'valor_total' => $valorTotal,
                'frequencia_pagamento' => $dados['frequencia_pagamento'],
                'data_vencimento' => Carbon::now()->addDays(30),
                'status' => 'ativo'
            ]);

            $this->gerarParcelas($emprestimo);

            return $emprestimo;
        });
    }

    /**
     * Calcula o valor total com juros simples.
     */
    private function calcularJurosSimples(float $valor, float $taxa): float
    {
        return $valor * (1 + ($taxa / 100));
    }

    /**
     * Gera as parcelas baseadas na frequência.
     */
    private function gerarParcelas(Emprestimo $emprestimo): void
    {
        $quantidade = $emprestimo->frequencia_pagamento === 'semanal' ? 4 : 1;
        $valorParcela = round($emprestimo->valor_total / $quantidade, 2);
        $hoje = Carbon::now();

        for ($i = 1; $i <= $quantidade; $i++) {
            Parcela::create([
                'emprestimo_id' => $emprestimo->id,
                'valor' => $valorParcela,
                'data_vencimento' => $emprestimo->frequencia_pagamento === 'semanal' 
                    ? $hoje->copy()->addWeeks($i) 
                    : $hoje->copy()->addMonth(),
                'status' => 'pendente'
            ]);
        }
    }
}
