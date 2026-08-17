<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // O Trait BelongsToEmpresa já filtrará tudo automaticamente aqui
        $totalEmprestado = Emprestimo::sum('valor');
        $totalReceber = Emprestimo::where('status', '!=', 'pago')->get()->sum('saldo');
        $qtdClientes = Cliente::count();
        $qtdEmprestimosAtrasados = Emprestimo::where('status', 'atrasado')->count();

        // Rentabilidade: quanto de lucro (juros) já foi realizado vs. previsto no total dos contratos
        $totalRecebido = Pagamento::sum('valor_pago');
        $lucroPrevisto = Emprestimo::sum('valor_total') - $totalEmprestado;
        $lucroRecebido = $totalRecebido - $totalEmprestado;
        $margemPercentual = $totalEmprestado > 0 ? ($lucroPrevisto / $totalEmprestado) * 100 : 0;

        return view('dashboard', compact(
            'totalEmprestado',
            'totalReceber',
            'qtdClientes',
            'qtdEmprestimosAtrasados',
            'lucroRecebido',
            'lucroPrevisto',
            'margemPercentual'
        ));
    }
}
