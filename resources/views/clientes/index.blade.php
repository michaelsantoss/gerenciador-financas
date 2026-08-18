@extends('layouts.app')

@section('content')
@php $empresaAtual = Auth::user()->empresa; @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h1 class="mb-0">Clientes</h1>
        @if($empresaAtual->limiteClientes())
            <small class="text-muted">{{ $clientes->count() }} de {{ $empresaAtual->limiteClientes() }} do plano {{ $empresaAtual->nomePlano() }}</small>
        @endif
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCliente">
        Novo Cliente
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Empréstimos Ativos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr class="linha-clicavel" data-href="{{ route('clientes.show', $cliente->id) }}">
                        <td class="dado-sensivel">{{ $cliente->nome }}</td>
                        <td class="dado-sensivel">{{ $cliente->telefone ?? 'Não informado' }}</td>
                        <td>{{ $cliente->emprestimos->where('status', 'ativo')->count() }}</td>
                        <td>
                            <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-outline-secondary">Ver Histórico</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Novo Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Cliente</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
