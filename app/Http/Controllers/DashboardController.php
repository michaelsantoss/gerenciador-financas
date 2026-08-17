<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $inicio = $request->filled('inicio') ? Carbon::parse($request->input('inicio'))->startOfDay() : null;
        $fim = $request->filled('fim') ? Carbon::parse($request->input('fim'))->endOfDay() : null;

        // O Trait BelongsToEmpresa já filtrará tudo automaticamente aqui

        // Empréstimos concedidos no período (por data de criação)
        $emprestimosQuery = Emprestimo::query();
        if ($inicio) {
            $emprestimosQuery->where('created_at', '>=', $inicio);
        }
        if ($fim) {
            $emprestimosQuery->where('created_at', '<=', $fim);
        }
        $emprestimosPeriodo = $emprestimosQuery->get();

        $totalEmprestado = $emprestimosPeriodo->sum('valor');
        $totalReceber = Emprestimo::where('status', '!=', 'pago')->get()->sum('saldo');
        $qtdClientes = Cliente::count();
        $qtdEmprestimosAtrasados = Emprestimo::where('status', 'atrasado')->count();

        // Lucro Previsto: juros embutidos nos empréstimos concedidos no período
        $lucroPrevisto = $emprestimosPeriodo->sum(fn ($e) => max(0, $e->valor_total - $e->valor));

        // Pagamentos recebidos no período (por data de pagamento) — independente de quando o empréstimo foi concedido
        $pagamentosQuery = Pagamento::query();
        if ($inicio) {
            $pagamentosQuery->where('data_pagamento', '>=', $inicio);
        }
        if ($fim) {
            $pagamentosQuery->where('data_pagamento', '<=', $fim);
        }
        $pagamentosPeriodo = $pagamentosQuery->with('emprestimo')->get();

        // Lucro Recebido: proporção de juros de cada empréstimo aplicada sobre o que foi
        // efetivamente pago dele. Nunca fica negativo (nem quando o capital ainda não voltou).
        $totalRecebido = $pagamentosPeriodo->sum('valor_pago');
        $lucroRecebido = $pagamentosPeriodo->sum(function ($pagamento) {
            $emprestimo = $pagamento->emprestimo;
            if (!$emprestimo || $emprestimo->valor_total <= 0) {
                return 0;
            }
            $proporcaoLucro = max(0, $emprestimo->valor_total - $emprestimo->valor) / $emprestimo->valor_total;
            return $pagamento->valor_pago * $proporcaoLucro;
        });

        $margemPercentual = $totalEmprestado > 0 ? ($lucroPrevisto / $totalEmprestado) * 100 : 0;

        return view('dashboard', compact(
            'totalEmprestado',
            'totalReceber',
            'qtdClientes',
            'qtdEmprestimosAtrasados',
            'totalRecebido',
            'lucroRecebido',
            'lucroPrevisto',
            'margemPercentual',
            'inicio',
            'fim'
        ));
    }
}
