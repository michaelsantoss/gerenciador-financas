<?php

namespace App\Http\Controllers;

use App\Models\AtividadeLog;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;

class AtividadeController extends Controller
{
    public function index(Request $request)
    {
        $query = AtividadeLog::with(['user', 'cliente']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        $usuarios = User::orderBy('name')->get();
        $clientes = Cliente::orderBy('nome')->get();

        return view('atividades.index', compact('logs', 'usuarios', 'clientes'));
    }
}
