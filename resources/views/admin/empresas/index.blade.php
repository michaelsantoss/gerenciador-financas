@extends('layouts.admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Empresas</h1>
    <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">Nova Empresa</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Usuários</th>
                        <th>Clientes</th>
                        <th>Empréstimos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($empresas as $empresa)
                    <tr>
                        <td>{{ $empresa->nome }}</td>
                        <td>{{ $empresa->cnpj ?? '—' }}</td>
                        <td>{{ $empresa->usuarios_count }}</td>
                        <td>{{ $empresa->clientes_count }}</td>
                        <td>{{ $empresa->emprestimos_count }}</td>
                        <td>
                            <span class="badge bg-{{ $empresa->ativo ? 'success' : 'secondary' }}">
                                {{ $empresa->ativo ? 'Ativa' : 'Desativada' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn btn-sm btn-outline-primary text-nowrap">Editar</a>

                                @if($empresa->ativo)
                                <form action="{{ route('admin.empresas.desativar', $empresa->id) }}" method="POST" onsubmit="return confirm('Desativar esta empresa? Os usuários dela não vão conseguir mais logar.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Desativar</button>
                                </form>
                                @else
                                <form action="{{ route('admin.empresas.ativar', $empresa->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success w-100">Ativar</button>
                                </form>
                                @endif

                                @if($empresa->clientes_count == 0 && $empresa->emprestimos_count == 0)
                                <form action="{{ route('admin.empresas.destroy', $empresa->id) }}" method="POST" onsubmit="return confirm('Excluir esta empresa e seu(s) usuário(s)? Essa ação não pode ser desfeita.')">
                                    @csrf
                                    @method('DELETE')
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
