@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
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

                    <div class="mb-3">
                        <label class="form-label">Frequência de Pagamento</label>
                        <select name="frequencia_pagamento" class="form-select">
                            <option value="mensal" {{ old('frequencia_pagamento') == 'mensal' ? 'selected' : '' }}>Mensal (1 Parcela)</option>
                            <option value="semanal" {{ old('frequencia_pagamento') == 'semanal' ? 'selected' : '' }}>Semanal (4 Parcelas)</option>
                        </select>
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
@endsection
