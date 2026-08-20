<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AtividadeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('role')->get(); // O Trait já filtra por empresa_id
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $empresa = Auth::user()->empresa;

        if ($empresa->atingiuLimiteAdmins()) {
            return back()->withInput()->withErrors(
                "Limite de {$empresa->limiteAdmins()} usuário(s) do plano {$empresa->nomePlano()} atingido. Fale com o suporte para fazer upgrade do plano."
            );
        }

        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'role_id' => $dados['role_id'],
            'empresa_id' => Auth::user()->empresa_id,
        ]);

        AtividadeLog::registrar('usuario.criado', null, "Usuário \"{$usuario->name}\" cadastrado");

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado!');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $dados['password'] = Hash::make($request->password);
        }

        $usuario->update($dados);

        AtividadeLog::registrar('usuario.editado', null, "Usuário \"{$usuario->name}\" atualizado");

        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado!');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return back()->withErrors('Você não pode excluir a si mesmo.');
        }

        AtividadeLog::registrar('usuario.excluido', null, "Usuário \"{$usuario->name}\" removido");

        $usuario->delete();
        return back()->with('success', 'Usuário removido!');
    }
}
