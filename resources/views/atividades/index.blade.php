@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Log de Atividades</h1>
</div>

<form action="{{ route('atividades.index') }}" method="GET" class="d-flex flex-wrap gap-2 mb-3">
    <select name="user_id" class="form-select form-select-sm" style="max-width: 260px;" onchange="this.form.submit()">
        <option value="">Todos os Admins</option>
        @foreach($usuarios as $usuario)
        <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
        @endforeach
    </select>

    <select name="cliente_id" class="form-select form-select-sm" style="max-width: 260px;" onchange="this.form.submit()">
        <option value="">Todos os Clientes</option>
        @foreach($clientes as $cliente)
        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
        @endforeach
    </select>

    @if(request('user_id') || request('cliente_id'))
    <a href="{{ route('atividades.index') }}" class="btn btn-sm btn-outline-secondary">Limpar filtros</a>
    @endif
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Admin</th>
                        <th>Ação</th>
                        <th>Cliente</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->acao }}</span></td>
                        <td>{{ $log->cliente->nome ?? '—' }}</td>
                        <td>{{ $log->detalhes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Nenhuma atividade registrada ainda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $logs->links() }}
</div>
@endsection
