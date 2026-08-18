<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\ClienteRequest;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('emprestimos')->latest()->get();
        return view('clientes.index', compact('clientes'));
    }

    public function store(ClienteRequest $request)
    {
        $empresa = Auth::user()->empresa;

        if ($empresa->atingiuLimiteClientes()) {
            return back()->withInput()->withErrors(
                "Limite de {$empresa->limiteClientes()} clientes do plano {$empresa->nomePlano()} atingido. Fale com o suporte para fazer upgrade do plano."
            );
        }

        $dados = $request->validated();
        $enderecoDados = $dados['endereco'] ?? null;
        unset($dados['endereco']);

        $cliente = Cliente::create($dados);

        if ($enderecoDados && array_filter($enderecoDados)) {
            $cliente->endereco()->create($enderecoDados);
        }

        // Se a requisição vier da tela de empréstimo, volta para lá
        if (str_contains(url()->previous(), 'emprestimos/create')) {
            return back()->with('success', 'Cliente cadastrado com sucesso!');
        }

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('emprestimos.pagamentos', 'endereco', 'arquivos');
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load('endereco');
        return view('clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $dados = $request->validated();
        $enderecoDados = $dados['endereco'] ?? null;
        unset($dados['endereco']);

        $cliente->update($dados);

        if ($enderecoDados && array_filter($enderecoDados)) {
            $cliente->endereco()->updateOrCreate([], $enderecoDados);
        }

        return redirect()->route('clientes.show', $cliente->id)->with('success', 'Cliente atualizado com sucesso!');
    }
}
