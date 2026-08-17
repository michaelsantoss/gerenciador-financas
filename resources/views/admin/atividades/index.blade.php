@extends('layouts.admin')

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    @include('partials.back-button', ['href' => route('admin.empresas.index')])
    <h1 class="mb-0">Logs de Atividade</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>Empresa</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $log->acao }}</span></td>
                        <td>{{ $log->empresa->nome ?? '—' }}</td>
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
