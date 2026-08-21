@extends('layouts.app')

@section('content')
@php
    $linhasAntigas = old('parcelas');
    $parcelasPorId = $emprestimo->parcelas->keyBy('id');

    if ($linhasAntigas) {
        $linhas = collect($linhasAntigas)->map(function ($linha) use ($parcelasPorId) {
            $original = !empty($linha['id']) ? $parcelasPorId->get((int) $linha['id']) : null;
            return [
                'id' => $linha['id'] ?? null,
                'data_vencimento' => $linha['data_vencimento'] ?? '',
                'valor' => $linha['valor'] ?? '',
                'status' => $original->status ?? 'pendente',
                'editavel' => !$original || in_array($original->status, ['pendente', 'atrasado']),
            ];
        })->values();
    } else {
        $linhas = $emprestimo->parcelas->map(function ($parcela) {
            return [
                'id' => $parcela->id,
                'data_vencimento' => $parcela->data_vencimento->format('Y-m-d'),
                'valor' => number_format($parcela->valor, 2, ',', ''),
                'status' => $parcela->status,
                'editavel' => in_array($parcela->status, ['pendente', 'atrasado']),
            ];
        })->values();
    }
@endphp
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('emprestimos.show', $emprestimo->id)])
                <h4 class="mb-0">Editar Empréstimo</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info small">
                    Parcelas já pagas ou pagas parcialmente não podem ser alteradas ou removidas —
                    o histórico de pagamento é sempre preservado.
                </div>

                <form action="{{ route('emprestimos.update', $emprestimo->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $emprestimo->cliente_id) == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor Emprestado / Principal (R$)</label>
                            <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', number_format($emprestimo->valor, 2, ',', '')) }}" required>
                            @error('valor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Taxa de Juros (%)</label>
                            <input type="text" name="taxa_juros" class="form-control @error('taxa_juros') is-invalid @enderror" value="{{ old('taxa_juros', number_format($emprestimo->taxa_juros, 2, ',', '')) }}">
                            @error('taxa_juros') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parcelas</label>
                        <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Valor (R$)</th>
                                    <th>Status</th>
                                    <th class="text-center">Remover</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-parcelas">
                                @foreach($linhas as $i => $linha)
                                <tr>
                                    @if($linha['editavel'])
                                        <td>
                                            <input type="hidden" name="parcelas[{{ $i }}][id]" value="{{ $linha['id'] }}">
                                            <input type="date" class="form-control form-control-sm" name="parcelas[{{ $i }}][data_vencimento]" value="{{ $linha['data_vencimento'] }}">
                                        </td>
                                        <td><input type="text" class="form-control form-control-sm" name="parcelas[{{ $i }}][valor]" value="{{ $linha['valor'] }}"></td>
                                        <td><span class="badge bg-{{ $linha['status'] == 'atrasado' ? 'danger' : 'warning' }}">{{ ucfirst($linha['status']) }}</span></td>
                                        <td class="text-center"><input type="checkbox" class="form-check-input" name="parcelas[{{ $i }}][remover]" value="1"></td>
                                    @else
                                        <td>
                                            <input type="hidden" name="parcelas[{{ $i }}][id]" value="{{ $linha['id'] }}">
                                            {{ \Illuminate\Support\Carbon::parse($linha['data_vencimento'])->format('d/m/Y') }}
                                        </td>
                                        <td>R$ {{ $linha['valor'] }}</td>
                                        <td><span class="badge bg-success">{{ ucfirst($linha['status']) }}</span></td>
                                        <td class="text-center text-muted">—</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-adicionar-parcela">+ Adicionar parcela</button>
                    </div>

                    <p class="text-muted small">
                        Valor Total: <strong>R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}</strong>
                        — recalculado automaticamente a partir da soma das parcelas ao salvar.
                    </p>

                    <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const tabela = document.getElementById('tabela-parcelas');
    const btnAdicionar = document.getElementById('btn-adicionar-parcela');
    let indice = {{ $linhas->count() }};

    function formatarData(data) {
        return data.toISOString().slice(0, 10);
    }

    btnAdicionar.addEventListener('click', function () {
        const hoje = new Date();
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="date" class="form-control form-control-sm" name="parcelas[${indice}][data_vencimento]" value="${formatarData(hoje)}"></td>
            <td><input type="text" class="form-control form-control-sm" name="parcelas[${indice}][valor]" placeholder="0,00"></td>
            <td><span class="badge bg-secondary">Nova</span></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remover-linha">&times;</button>
            </td>
        `;
        tabela.appendChild(tr);
        indice++;
    });

    tabela.addEventListener('click', function (event) {
        if (event.target.classList.contains('btn-remover-linha')) {
            event.target.closest('tr').remove();
        }
    });
})();
</script>
@endsection
