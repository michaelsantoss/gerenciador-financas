@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Detalhes do Cliente: <span class="dado-sensivel">{{ $cliente->nome }}</span></h1>
    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Voltar</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Informações</div>
            <div class="card-body">
                <p><strong>Nome:</strong> <span class="dado-sensivel">{{ $cliente->nome }}</span></p>
                <p><strong>Telefone:</strong> <span class="dado-sensivel">{{ $cliente->telefone ?? 'Não informado' }}</span></p>
                <p><strong>Desde:</strong> {{ $cliente->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Histórico de Empréstimos</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Total Devido</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->emprestimos as $emprestimo)
                        <tr>
                            <td>{{ $emprestimo->created_at->format('d/m/Y') }}</td>
                            <td class="dado-sensivel">R$ {{ number_format($emprestimo->valor, 2, ',', '.') }}</td>
                            <td class="dado-sensivel">R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $emprestimo->status == 'pago' ? 'success' : 'primary' }}">
                                    {{ ucfirst($emprestimo->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('emprestimos.show', $emprestimo->id) }}" class="btn btn-sm btn-link">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhum empréstimo encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
