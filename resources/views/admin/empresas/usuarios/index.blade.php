@extends('layouts.admin')

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    @include('partials.back-button', ['href' => route('admin.empresas.index')])
    <h1 class="mb-0">Usuários — {{ $empresa->nome }}</h1>
</div>

<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.empresas.usuarios.create', $empresa->id) }}" class="btn btn-primary">Novo Usuário</a>
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
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td><span class="badge bg-info">{{ $usuario->role->nome ?? 'Sem Perfil' }}</span></td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('admin.empresas.usuarios.edit', [$empresa->id, $usuario->id]) }}" class="btn btn-sm btn-outline-primary text-nowrap">Editar</a>
                                <form action="{{ route('admin.empresas.usuarios.destroy', [$empresa->id, $usuario->id]) }}" method="POST" onsubmit="return confirm('Excluir este usuário?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Nenhum usuário cadastrado nessa empresa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
