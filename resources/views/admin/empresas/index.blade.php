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
                        <th>Plano</th>
                        <th>Usuários</th>
                        <th>Clientes</th>
                        <th>Empréstimos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $statusCor = ['ativo' => 'success', 'inativo' => 'secondary', 'bloqueado' => 'danger'];
                        $statusLabel = ['ativo' => 'Ativa', 'inativo' => 'Inativa', 'bloqueado' => 'Bloqueada'];
                    @endphp
                    @foreach($empresas as $empresa)
                    <tr>
                        <td>{{ $empresa->nome }}</td>
                        <td>{{ $empresa->cnpj ?? '—' }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $empresa->nomePlano() }}</span>
                            @if($empresa->atingiuLimiteClientes() || $empresa->atingiuLimiteAdmins())
                                <br><small class="text-danger">Limite atingido</small>
                            @endif
                        </td>
                        <td>{{ $empresa->usuarios_count }}{{ $empresa->limiteAdmins() ? '/' . $empresa->limiteAdmins() : '' }}</td>
                        <td>{{ $empresa->clientes_count }}{{ $empresa->limiteClientes() ? '/' . $empresa->limiteClientes() : '' }}</td>
                        <td>{{ $empresa->emprestimos_count }}</td>
                        <td>
                            <span class="badge bg-{{ $statusCor[$empresa->status] ?? 'secondary' }}">
                                {{ $statusLabel[$empresa->status] ?? ucfirst($empresa->status) }}
                            </span>
                            @if($empresa->observacao)
                                <br><small class="text-muted">{{ $empresa->observacao }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('admin.empresas.edit', $empresa->id) }}" class="btn btn-sm btn-outline-primary text-nowrap">Editar</a>
                                <a href="{{ route('admin.empresas.usuarios.index', $empresa->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">Usuários</a>

                                @if(!$empresa->estaAtiva())
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
