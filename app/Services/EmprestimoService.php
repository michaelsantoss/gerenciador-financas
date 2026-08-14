<?php

namespace App\Services;

use App\Models\Emprestimo;
use App\Models\Pagamento;
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

            $emprestimo = Emprestimo::create([
                'cliente_id' => $dados['cliente_id'],
                'valor' => $valorPrincipal,
                'taxa_juros' => $taxa,
                'valor_total' => 0,
                'frequencia_pagamento' => $dados['frequencia_pagamento'],
                'data_vencimento' => Carbon::now(),
                'status' => 'ativo'
            ]);

            $this->gerarParcelas($emprestimo, $dados);

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
     * Gera as parcelas: usa a lista vinda do formulário quando informada
     * (permite prazo/valores livres e marcar parcelas já pagas), ou cai no
     * comportamento padrão (1 parcela mensal / N parcelas semanais iguais).
     */
    private function gerarParcelas(Emprestimo $emprestimo, array $dados): void
    {
        $parcelasInformadas = $dados['parcelas'] ?? [];

        if (!empty($parcelasInformadas)) {
            $especificacoes = collect($parcelasInformadas)->map(fn ($parcela) => [
                'valor' => round((float) $parcela['valor'], 2),
                'data_vencimento' => Carbon::parse($parcela['data_vencimento']),
                'pago' => (bool) ($parcela['pago'] ?? false),
                'data_pagamento' => !empty($parcela['data_pagamento']) ? Carbon::parse($parcela['data_pagamento']) : null,
            ])->all();
        } else {
            $quantidade = $emprestimo->frequencia_pagamento === 'semanal' ? (int) ($dados['numero_parcelas'] ?? 4) : 1;
            $valorTotal = round($this->calcularJurosSimples($emprestimo->valor, $emprestimo->taxa_juros), 2);
            $valorParcela = round($valorTotal / $quantidade, 2);
            $hoje = Carbon::now();

            $especificacoes = collect(range(1, $quantidade))->map(fn ($i) => [
                'valor' => $valorParcela,
                'data_vencimento' => $emprestimo->frequencia_pagamento === 'semanal'
                    ? $hoje->copy()->addWeeks($i)
                    : $hoje->copy()->addMonth(),
                'pago' => false,
                'data_pagamento' => null,
            ])->all();
        }

        foreach ($especificacoes as $especificacao) {
            $parcela = Parcela::create([
                'emprestimo_id' => $emprestimo->id,
                'valor' => $especificacao['valor'],
                'data_vencimento' => $especificacao['data_vencimento'],
                'status' => $especificacao['pago'] ? 'pago' : 'pendente',
            ]);

            if ($especificacao['pago']) {
                Pagamento::create([
                    'emprestimo_id' => $emprestimo->id,
                    'valor_pago' => $parcela->valor,
                    'data_pagamento' => $especificacao['data_pagamento'] ?? $especificacao['data_vencimento'],
                    'observacoes' => "Pagamento da parcela ID: {$parcela->id}",
                ]);
            }
        }

        $ultimaParcela = collect($especificacoes)->sortBy('data_vencimento')->last();

        $emprestimo->update([
            'valor_total' => collect($especificacoes)->sum('valor'),
            'data_vencimento' => $ultimaParcela['data_vencimento'],
        ]);
    }
}
