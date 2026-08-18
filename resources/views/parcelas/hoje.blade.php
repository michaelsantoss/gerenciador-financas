@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    @include('partials.back-button', ['href' => route('dashboard')])
    <h1 class="mb-0">Cobranças de Hoje</h1>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span>Vencendo Hoje</span>
        <span class="badge bg-warning text-dark">{{ $vencendoHoje->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vencendoHoje as $parcela)
                    <tr class="linha-clicavel" data-href="{{ route('emprestimos.show', $parcela->emprestimo_id) }}">
                        <td class="dado-sensivel">{{ $parcela->emprestimo->cliente->nome }}</td>
                        <td class="dado-sensivel text-nowrap">
                            R$ {{ number_format($parcela->valor_pendente, 2, ',', '.') }}
                            @if($parcela->valor_pago > 0)
                                <br><small class="text-muted">de R$ {{ number_format($parcela->valor, 2, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $parcela->status == 'parcial' ? 'info' : 'warning' }}">
                                {{ ucfirst($parcela->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('emprestimos.show', $parcela->emprestimo_id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">Ver / Pagar</a>
                                @include('partials.whatsapp-button', ['link' => $parcela->emprestimo->cliente->linkWhatsapp(\App\Support\MensagemCobranca::gerar('hoje', $parcela->valor_pendente))])
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Nenhuma parcela vencendo hoje.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span>Atrasadas</span>
        <span class="badge bg-danger">{{ $atrasadas->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cliente</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($atrasadas as $parcela)
                    <tr class="linha-clicavel table-danger" data-href="{{ route('emprestimos.show', $parcela->emprestimo_id) }}">
                        <td class="dado-sensivel">{{ $parcela->emprestimo->cliente->nome }}</td>
                        <td class="text-nowrap fw-bold">{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                        <td class="dado-sensivel text-nowrap">
                            R$ {{ number_format($parcela->valor_pendente, 2, ',', '.') }}
                            @if($parcela->valor_pago > 0)
                                <br><small class="text-muted">de R$ {{ number_format($parcela->valor, 2, ',', '.') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $parcela->status == 'parcial' ? 'info' : 'danger' }}">
                                {{ ucfirst($parcela->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row gap-1">
                                <a href="{{ route('emprestimos.show', $parcela->emprestimo_id) }}" class="btn btn-sm btn-outline-secondary text-nowrap">Ver / Pagar</a>
                                @include('partials.whatsapp-button', ['link' => $parcela->emprestimo->cliente->linkWhatsapp(\App\Support\MensagemCobranca::gerar('atrasado', $parcela->valor_pendente))])
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Nenhuma parcela atrasada. 🎉</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
