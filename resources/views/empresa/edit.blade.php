@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('dashboard')])
                <h4 class="mb-0">Configurações da Empresa</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('empresa.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nome da Empresa</label>
                        <input type="text" name="nome" class="form-control" value="{{ $empresa->nome ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" class="form-control" value="{{ $empresa->cnpj ?? ''}}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h4 class="mb-0">Backup dos Dados</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Gera um arquivo .zip com um CSV de cada tabela (clientes, endereços, empréstimos, parcelas e pagamentos),
                    com os IDs preservados para permitir reimportação futura. Fotos e anexos não são incluídos — apenas a lista com os nomes dos arquivos.
                </p>
                <a href="{{ route('empresa.backup') }}" class="btn btn-outline-secondary">Exportar Backup (.zip / CSV)</a>
            </div>
        </div>
    </div>
</div>
@endsection
