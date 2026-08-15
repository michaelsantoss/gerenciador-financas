<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\ClienteRequest;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('emprestimos')->latest()->get();
        return view('clientes.index', compact('clientes'));
    }

    public function store(ClienteRequest $request)
    {
        Cliente::create($request->validated());
        
        // Se a requisição vier da tela de empréstimo, volta para lá
        if (str_contains(url()->previous(), 'emprestimos/create')) {
            return back()->with('success', 'Cliente cadastrado com sucesso!');
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('emprestimos.pagamentos');
        // Você pode criar uma view específica depois, por enquanto redireciona ou mostra JSON
        return view('clientes.show', compact('cliente'));
    }
}
