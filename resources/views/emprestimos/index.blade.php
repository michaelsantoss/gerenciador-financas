@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Empréstimos</h1>
    <div class="d-flex gap-2">
        <form action="{{ route('emprestimos.index') }}" method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Todos os Status</option>
                <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativos</option>
                <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pagos</option>
                <option value="atrasado" {{ request('status') == 'atrasado' ? 'selected' : '' }}>Atrasados</option>
            </select>
        </form>
        <a href="{{ route('emprestimos.create') }}" class="btn btn-primary">Novo Empréstimo</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Valor Principal</th>
                        <th>Total Devido</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emprestimos as $emprestimo)
                    <tr class="{{ $emprestimo->status == 'atrasado' ? 'table-danger' : '' }}">
                        <td>{{ $emprestimo->cliente->nome }}</td>
                        <td>R$ {{ number_format($emprestimo->valor, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}</td>
                        <td class="{{ $emprestimo->data_vencimento->isPast() && $emprestimo->status != 'pago' ? 'text-danger fw-bold' : '' }}">
                            {{ $emprestimo->data_vencimento->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $emprestimo->status == 'pago' ? 'success' : ($emprestimo->status == 'atrasado' ? 'danger' : 'primary') }}">
                                {{ ucfirst($emprestimo->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('emprestimos.show', $emprestimo->id) }}" class="btn btn-sm btn-outline-secondary">Ver Detalhes</a>
                                <form action="{{ route('emprestimos.destroy', $emprestimo->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este empréstimo? Todos os pagamentos e parcelas vinculados serão removidos.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
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
