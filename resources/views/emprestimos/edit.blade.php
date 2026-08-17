@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('emprestimos.show', $emprestimo->id)])
                <h4 class="mb-0">Editar Empréstimo</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    Aqui dá pra corrigir o cliente, o valor emprestado (principal) e a taxa de
                    juros — útil pra acertar o registro de rentabilidade. As parcelas em si
                    (valores, datas, pagamentos) continuam sendo ajustadas na
                    <a href="{{ route('emprestimos.show', $emprestimo->id) }}">tela de detalhes do empréstimo</a>.
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

                    <p class="text-muted small">
                        Valor Total (R$ {{ number_format($emprestimo->valor_total, 2, ',', '.') }}) não é editado
                        aqui — ele sempre reflete a soma das parcelas do empréstimo.
                    </p>

                    <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
