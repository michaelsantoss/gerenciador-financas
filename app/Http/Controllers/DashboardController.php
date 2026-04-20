<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Cliente;
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

        return view('dashboard', compact(
            'totalEmprestado', 
            'totalReceber', 
            'qtdClientes', 
            'qtdEmprestimosAtrasados'
        ));
    }
}
