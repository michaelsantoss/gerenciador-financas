<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function edit()
    {
        $empresa = Auth::user()->empresa;
        return view('empresa.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
        ]);

        $empresa = Auth::user()->empresa;

        if (!$empresa) {
            return back()->withErrors('Empresa não encontrada para este usuário.');
        }

        $empresa->update($dados);
        return back()->with('success', 'Dados da empresa atualizados!');
    }
}
