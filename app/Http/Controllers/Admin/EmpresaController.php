<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::withCount(['usuarios', 'clientes', 'emprestimos'])
            ->orderBy('nome')
            ->get();

        return view('admin.empresas.index', compact('empresas'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.empresas.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'plano' => 'required|in:' . implode(',', array_keys(Empresa::PLANOS)),
            'usuario_nome' => 'required|string|max:255',
            'usuario_email' => 'required|email|unique:users,email',
            'usuario_senha' => 'required|min:6|confirmed',
            'usuario_role_id' => 'required|exists:roles,id',
        ]);

        DB::transaction(function () use ($dados) {
            $empresa = Empresa::create([
                'nome' => $dados['nome'],
                'cnpj' => $dados['cnpj'] ?? null,
                'plano' => $dados['plano'],
            ]);

            User::create([
                'name' => $dados['usuario_nome'],
                'email' => $dados['usuario_email'],
                'password' => Hash::make($dados['usuario_senha']),
                'role_id' => $dados['usuario_role_id'],
                'empresa_id' => $empresa->id,
            ]);

            AdminActivityLog::registrar('empresa.criada', $empresa, "Empresa: {$empresa->nome} | Usuário inicial: {$dados['usuario_email']}");
        });

        return redirect()->route('admin.empresas.index')->with('success', 'Empresa criada com sucesso!');
    }

    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:20',
            'status' => 'required|in:ativo,inativo,bloqueado',
            'plano' => 'required|in:' . implode(',', array_keys(Empresa::PLANOS)),
            'observacao' => 'nullable|string',
        ]);

        $statusMudou = $empresa->status !== $dados['status'];
        $planoAntigo = $empresa->plano;
        $planoMudou = $planoAntigo !== $dados['plano'];

        $empresa->update($dados);

        $detalhes = "Empresa: {$empresa->nome}";
        if ($statusMudou) {
            $detalhes .= " | Novo status: {$dados['status']}";
        }
        if ($planoMudou) {
            $detalhes .= " | Plano: {$planoAntigo} -> {$dados['plano']}";
        }

        AdminActivityLog::registrar('empresa.editada', $empresa, $detalhes);

        return redirect()->route('admin.empresas.index')->with('success', 'Empresa atualizada com sucesso!');
    }

    public function ativar(Empresa $empresa)
    {
        $empresa->update(['status' => Empresa::STATUS_ATIVO]);
        AdminActivityLog::registrar('empresa.ativada', $empresa, "Empresa: {$empresa->nome}");

        return back()->with('success', 'Empresa ativada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->clientes()->count() > 0 || $empresa->emprestimos()->count() > 0) {
            return back()->withErrors('Não é possível excluir: essa empresa já tem clientes ou empréstimos cadastrados. Desative-a em vez de excluir.');
        }

        $nome = $empresa->nome;
        $empresa->delete();
        AdminActivityLog::registrar('empresa.excluida', null, "Empresa excluída: {$nome} (ID {$empresa->id})");

        return redirect()->route('admin.empresas.index')->with('success', 'Empresa excluída com sucesso!');
    }
}
