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

            $especificacoes = $this->calcularEspecificacoes($dados, $valorPrincipal, $taxa);
            $ultimaParcela = collect($especificacoes)->sortBy('data_vencimento')->last();

            $emprestimo = Emprestimo::create([
                'cliente_id' => $dados['cliente_id'],
                'valor' => $valorPrincipal,
                'taxa_juros' => $taxa,
                'valor_total' => collect($especificacoes)->sum('valor'),
                'frequencia_pagamento' => $dados['frequencia_pagamento'],
                'data_vencimento' => $ultimaParcela['data_vencimento'],
                'status' => 'ativo'
            ]);

            $this->gerarParcelas($emprestimo, $especificacoes);

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
     * Calcula as especificações de parcela: usa a lista vinda do formulário
     * quando informada (permite prazo/valores livres e marcar parcelas já
     * pagas), ou cai no comportamento padrão (1 parcela mensal / N parcelas
     * semanais iguais).
     */
    private function calcularEspecificacoes(array $dados, float $valorPrincipal, float $taxa): array
    {
        $parcelasInformadas = $dados['parcelas'] ?? [];

        if (!empty($parcelasInformadas)) {
            return collect($parcelasInformadas)->map(fn ($parcela) => [
                'valor' => round((float) $parcela['valor'], 2),
                'data_vencimento' => Carbon::parse($parcela['data_vencimento']),
                'pago' => (bool) ($parcela['pago'] ?? false),
                'data_pagamento' => !empty($parcela['data_pagamento']) ? Carbon::parse($parcela['data_pagamento']) : null,
            ])->all();
        }

        $frequencia = $dados['frequencia_pagamento'];
        $quantidade = $frequencia === 'semanal' ? (int) ($dados['numero_parcelas'] ?? 4) : 1;
        $valorTotal = round($this->calcularJurosSimples($valorPrincipal, $taxa), 2);
        $valorParcela = round($valorTotal / $quantidade, 2);
        $hoje = Carbon::now();

        return collect(range(1, $quantidade))->map(fn ($i) => [
            'valor' => $valorParcela,
            'data_vencimento' => $frequencia === 'semanal'
                ? $hoje->copy()->addWeeks($i)
                : $hoje->copy()->addMonth(),
            'pago' => false,
            'data_pagamento' => null,
        ])->all();
    }

    /**
     * Cria as parcelas (e os pagamentos das que já nascem quitadas) para um
     * empréstimo cujo valor_total/data_vencimento já foram definidos.
     */
    private function gerarParcelas(Emprestimo $emprestimo, array $especificacoes): void
    {
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
    }
}
