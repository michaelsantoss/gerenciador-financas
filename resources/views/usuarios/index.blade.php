@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Usuários</h1>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">Novo Usuário</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td><span class="badge bg-info">{{ $usuario->role->nome ?? 'Sem Perfil' }}</span></td>
                    <td>
                        <div class="d-flex flex-column flex-sm-row gap-1">
                            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-outline-primary text-nowrap">Editar</a>
                            @if($usuario->id !== Auth::id())
                            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('Excluir este usuário?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Excluir</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
