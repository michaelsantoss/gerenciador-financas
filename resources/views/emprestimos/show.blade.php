@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Detalhes do Empréstimo</h1>
    <form action="{{ route('emprestimos.destroy', $emprestimo->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este empréstimo?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Excluir Empréstimo</button>
    </form>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Resumo do Empréstimo</div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <span class="dado-sensivel">{{ $emprestimo->cliente->nome }}</span></p>
                <p><strong>Total Devido:</strong> <span class="dado-sensivel">R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}</span></p>
                <p><strong>Saldo Restante:</strong> <span class="dado-sensivel">R$ {{ number_format($emprestimo->saldo, 2, ',', '.') }}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-primary">{{ ucfirst($emprestimo->status) }}</span></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Parcelas</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Parcela</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emprestimo->parcelas as $parcela)
                        <tr class="{{ $parcela->status == 'pendente' && $parcela->data_vencimento->isPast() ? 'table-warning' : '' }}">
                            <td>#{{ $loop->iteration }}</td>
                            <td class="text-nowrap {{ $parcela->status == 'pendente' && $parcela->data_vencimento->isPast() ? 'text-danger fw-bold' : '' }}">
                                {{ $parcela->data_vencimento->format('d/m/Y') }}
                            </td>
                            <td class="dado-sensivel text-nowrap">
                                R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                                @if($parcela->valor_pago > 0 && $parcela->status != 'pago')
                                    <br><small class="text-muted">Pago: R$ {{ number_format($parcela->valor_pago, 2, ',', '.') }} · Falta: R$ {{ number_format($parcela->valor_pendente, 2, ',', '.') }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $parcela->status == 'pago' ? 'success' : ($parcela->status == 'parcial' ? 'info' : 'warning') }}">
                                    {{ ucfirst($parcela->status) }}
                                </span>
                            </td>
                            <td style="min-width: 200px">
                                @if($parcela->status != 'pago')
                                <form action="{{ route('parcelas.quitar', $parcela->id) }}" method="POST" class="d-flex gap-1 parcela-acao-form">
                                    @csrf
                                    <input type="number" name="valor" step="0.01" min="0.01" max="{{ $parcela->valor_pendente }}" value="{{ $parcela->valor_pendente }}" class="form-control form-control-sm parcela-acao-valor" title="Valor a pagar">
                                    <button type="submit" class="btn btn-sm btn-success text-nowrap">Registrar pagamento</button>
                                </form>
                                @else
                                <form action="{{ route('parcelas.desfazer', $parcela->id) }}" method="POST" onsubmit="return confirm('Desfazer o último pagamento desta parcela?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Desfazer</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @if($parcela->status == 'parcial')
                        <tr>
                            <td colspan="5" class="text-end pt-0 pb-2">
                                <form action="{{ route('parcelas.desfazer', $parcela->id) }}" method="POST" onsubmit="return confirm('Desfazer o último pagamento desta parcela?')" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Desfazer último pagamento</button>
                                </form>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
