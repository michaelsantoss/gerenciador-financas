@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Empréstimos</h1>
    <div class="d-flex flex-wrap gap-2">
        <form action="{{ route('emprestimos.index') }}" method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="" {{ $status === '' ? 'selected' : '' }}>Todos os Status</option>
                <option value="ativo" {{ $status === 'ativo' ? 'selected' : '' }}>Ativos</option>
                <option value="pago" {{ $status === 'pago' ? 'selected' : '' }}>Pagos</option>
                <option value="atrasado" {{ $status === 'atrasado' ? 'selected' : '' }}>Atrasados</option>
            </select>
        </form>
        <a href="{{ route('parcelas.hoje') }}" class="btn btn-warning">Cobranças de Hoje</a>
        <a href="{{ route('emprestimos.create') }}" class="btn btn-primary">Novo Empréstimo</a>
    </div>
</div>

<div class="mb-3">
    <input type="text" id="filtro-nome-emprestimo" class="form-control" placeholder="Filtrar por nome do cliente...">
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
                <tbody id="tabela-emprestimos">
                    @foreach($emprestimos as $emprestimo)
                    <tr class="linha-clicavel {{ $emprestimo->status == 'atrasado' ? 'table-danger' : '' }}" data-href="{{ route('emprestimos.show', $emprestimo->id) }}" data-nome="{{ strtolower($emprestimo->cliente->nome) }}">
                        <td class="dado-sensivel">{{ $emprestimo->cliente->nome }}</td>
                        <td class="dado-sensivel">R$ {{ number_format($emprestimo->valor, 2, ',', '.') }}</td>
                        <td class="dado-sensivel">R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}</td>
                        <td class="{{ $emprestimo->data_vencimento->isPast() && $emprestimo->status != 'pago' ? 'text-danger fw-bold' : '' }}">
                            {{ $emprestimo->data_vencimento->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $emprestimo->status == 'pago' ? 'success' : ($emprestimo->status == 'atrasado' ? 'danger' : 'primary') }}">
                                {{ ucfirst($emprestimo->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('emprestimos.show', $emprestimo->id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">Ver Detalhes</a>
                                <form action="{{ route('emprestimos.destroy', $emprestimo->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este empréstimo? Todos os pagamentos e parcelas vinculados serão removidos.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Excluir</button>
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

<script>
document.getElementById('filtro-nome-emprestimo').addEventListener('input', function () {
    const termo = this.value.trim().toLowerCase();
    document.querySelectorAll('#tabela-emprestimos tr[data-nome]').forEach(function (linha) {
        linha.classList.toggle('d-none', !linha.dataset.nome.includes(termo));
    });
});
</script>
@endsection
