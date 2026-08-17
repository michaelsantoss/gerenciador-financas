<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmpresaUsuarioController extends Controller
{
    public function index(Empresa $empresa)
    {
        $usuarios = $empresa->usuarios()->with('role')->get();

        return view('admin.empresas.usuarios.index', compact('empresa', 'usuarios'));
    }

    public function create(Empresa $empresa)
    {
        $roles = Role::all();

        return view('admin.empresas.usuarios.create', compact('empresa', 'roles'));
    }

    public function store(Request $request, Empresa $empresa)
    {
        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->where('empresa_id', $empresa->id)],
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $usuario = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'role_id' => $dados['role_id'],
            'empresa_id' => $empresa->id,
        ]);

        AdminActivityLog::registrar('empresa.usuario.criado', $empresa, "Empresa: {$empresa->nome} | Usuário: {$usuario->email}");

        return redirect()->route('admin.empresas.usuarios.index', $empresa->id)->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(Empresa $empresa, User $usuario)
    {
        $this->garantirMesmaEmpresa($empresa, $usuario);

        $roles = Role::all();

        return view('admin.empresas.usuarios.edit', compact('empresa', 'usuario', 'roles'));
    }

    public function update(Request $request, Empresa $empresa, User $usuario)
    {
        $this->garantirMesmaEmpresa($empresa, $usuario);

        $dados = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->where('empresa_id', $empresa->id)->ignore($usuario->id)],
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $dados['password'] = Hash::make($request->password);
        }

        $usuario->update($dados);

        AdminActivityLog::registrar('empresa.usuario.editado', $empresa, "Empresa: {$empresa->nome} | Usuário: {$usuario->email}");

        return redirect()->route('admin.empresas.usuarios.index', $empresa->id)->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(Empresa $empresa, User $usuario)
    {
        $this->garantirMesmaEmpresa($empresa, $usuario);

        $email = $usuario->email;
        $usuario->delete();

        AdminActivityLog::registrar('empresa.usuario.excluido', $empresa, "Empresa: {$empresa->nome} | Usuário excluído: {$email}");

        return back()->with('success', 'Usuário removido com sucesso!');
    }

    private function garantirMesmaEmpresa(Empresa $empresa, User $usuario)
    {
        abort_if($usuario->empresa_id !== $empresa->id, 404);
    }
}
