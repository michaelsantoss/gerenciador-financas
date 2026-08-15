@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('emprestimos.index')])
                <h4 class="mb-0">Novo Empréstimo</h4>
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

                <!-- Formulário Principal de Empréstimo -->
                <form action="{{ route('emprestimos.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <div class="input-group">
                            <select name="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror">
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalCliente">
                                + Novo
                            </button>
                        </div>
                        @error('cliente_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Valor do Empréstimo (R$)</label>
                            <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" placeholder="0,00" value="{{ old('valor') }}">
                            @error('valor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Taxa de Juros (%)</label>
                            <input type="text" name="taxa_juros" class="form-control @error('taxa_juros') is-invalid @enderror" value="{{ old('taxa_juros', '30.00') }}">
                            @error('taxa_juros') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Frequência de Pagamento</label>
                            <select id="frequencia_pagamento" name="frequencia_pagamento" class="form-select">
                                <option value="mensal" {{ old('frequencia_pagamento') == 'mensal' ? 'selected' : '' }}>Mensal (1 Parcela)</option>
                                <option value="semanal" {{ old('frequencia_pagamento') == 'semanal' ? 'selected' : '' }}>Semanal (N Parcelas)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="grupo-numero-parcelas">
                            <label class="form-label">Número de Parcelas</label>
                            <input type="number" id="numero_parcelas" name="numero_parcelas" class="form-control" min="1" max="52" value="{{ old('numero_parcelas', 4) }}">
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="grupo-parcelas">
                        <label class="form-label">Parcelas</label>
                        <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Vencimento</th>
                                    <th>Valor (R$)</th>
                                    <th class="text-center">Já pago?</th>
                                    <th>Data do pagamento</th>
                                </tr>
                            </thead>
                            <tbody id="tabela-parcelas"></tbody>
                        </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-recalcular-parcelas">Recalcular parcelas</button>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">Gerar Empréstimo</button>
                        <a href="{{ route('emprestimos.index') }}" class="btn btn-link">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rápido Novo Cliente (MOVIDO PARA FORA DO FORM PRINCIPAL) -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Novo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('clientes.store') }}" method="POST">
                @csrf
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="telefone" class="form-control" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar e Continuar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const frequencia = document.getElementById('frequencia_pagamento');
    const grupoNumeroParcelas = document.getElementById('grupo-numero-parcelas');
    const grupoParcelas = document.getElementById('grupo-parcelas');
    const numeroParcelas = document.getElementById('numero_parcelas');
    const valorInput = document.querySelector('input[name="valor"]');
    const tabela = document.getElementById('tabela-parcelas');
    const btnRecalcular = document.getElementById('btn-recalcular-parcelas');

    function formatarData(data) {
        return data.toISOString().slice(0, 10);
    }

    function gerarLinhas() {
        const quantidade = Math.max(1, parseInt(numeroParcelas.value || '1', 10));
        const valorTotal = parseFloat((valorInput.value || '0').replace(',', '.')) || 0;
        const valorParcela = quantidade > 0 ? (valorTotal / quantidade) : 0;
        const hoje = new Date();

        tabela.innerHTML = '';

        for (let i = 1; i <= quantidade; i++) {
            const venc = new Date(hoje);
            venc.setDate(venc.getDate() + (7 * i));

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="date" class="form-control form-control-sm" name="parcelas[${i}][data_vencimento]" value="${formatarData(venc)}"></td>
                <td><input type="text" class="form-control form-control-sm" name="parcelas[${i}][valor]" value="${valorParcela.toFixed(2).replace('.', ',')}"></td>
                <td class="text-center"><input type="checkbox" class="form-check-input parcela-pago" name="parcelas[${i}][pago]" value="1"></td>
                <td><input type="date" class="form-control form-control-sm parcela-data-pagamento" name="parcelas[${i}][data_pagamento]" value="${formatarData(venc)}" disabled></td>
            `;
            tabela.appendChild(tr);
        }
    }

    tabela.addEventListener('change', function (event) {
        if (event.target.classList.contains('parcela-pago')) {
            const row = event.target.closest('tr');
            row.querySelector('.parcela-data-pagamento').disabled = !event.target.checked;
        }
    });

    frequencia.addEventListener('change', function () {
        const semanal = frequencia.value === 'semanal';
        grupoNumeroParcelas.classList.toggle('d-none', !semanal);
        grupoParcelas.classList.toggle('d-none', !semanal);
        if (semanal) {
            gerarLinhas();
        } else {
            tabela.innerHTML = '';
        }
    });

    numeroParcelas.addEventListener('change', gerarLinhas);
    btnRecalcular.addEventListener('click', gerarLinhas);

    if (frequencia.value === 'semanal') {
        grupoNumeroParcelas.classList.remove('d-none');
        grupoParcelas.classList.remove('d-none');
        gerarLinhas();
    }
})();
</script>
@endsection
