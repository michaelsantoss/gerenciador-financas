@extends('layouts.app')

@section('content')
@php $filtroAtivo = request('inicio') || request('fim'); @endphp
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
    <h1 class="mb-0">Dashboard: {{ Auth::user()->empresa->nome }}</h1>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filtroPeriodo">
        Filtrar por período {{ $filtroAtivo ? '(ativo)' : '' }}
    </button>
</div>

<div class="collapse {{ $filtroAtivo ? 'show' : '' }} mb-4" id="filtroPeriodo">
    <form action="{{ route('dashboard') }}" method="GET" class="d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small mb-0">De</label>
            <input type="date" name="inicio" value="{{ request('inicio') }}" class="form-control form-control-sm">
        </div>
        <div>
            <label class="form-label small mb-0">Até</label>
            <input type="date" name="fim" value="{{ request('fim') }}" class="form-control form-control-sm">
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
        @if($filtroAtivo)
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">Limpar</a>
        @endif
    </form>
</div>

@if($inicio || $fim)
    <p class="text-muted small">
        Empréstimos concedidos e Lucro Previsto considerando o período selecionado
        ({{ $inicio ? $inicio->format('d/m/Y') : 'início' }} até {{ $fim ? $fim->format('d/m/Y') : 'hoje' }}).
        Total a Receber, Clientes Ativos e Empréstimos em Atraso continuam mostrando a situação atual (não são afetados pelo período).
    </p>
@endif

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Total Emprestado</h6>
                <h3 class="dado-sensivel">R$ {{ number_format($totalEmprestado, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Total a Receber</h6>
                <h3 class="dado-sensivel">R$ {{ number_format($totalReceber, 2, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Clientes Ativos</h6>
                <h3>{{ $qtdClientes }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="{{ route('parcelas.hoje') }}" class="text-decoration-none">
            <div class="card bg-danger text-white shadow-sm mb-4">
                <div class="card-body">
                    <h6>Empréstimos em Atraso</h6>
                    <h3>{{ $qtdEmprestimosAtrasados }}</h3>
                </div>
            </div>
        </a>
    </div>
</div>

<h5 class="mb-3">Rentabilidade</h5>
<div class="row">
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Lucro Recebido</h6>
                <h3 class="dado-sensivel">R$ {{ number_format($lucroRecebido, 2, ',', '.') }}</h3>
                <small>Parte de lucro (juros) já embutida no que foi efetivamente pago (R$ {{ number_format($totalRecebido, 2, ',', '.') }} recebidos)</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Lucro Previsto</h6>
                <h3 class="dado-sensivel">R$ {{ number_format($lucroPrevisto, 2, ',', '.') }}</h3>
                <small>Juros embutidos nos empréstimos do período, se tudo for pago</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Margem de Lucro</h6>
                <h3>{{ number_format($margemPercentual, 1, ',', '.') }}%</h3>
                <small>Lucro previsto sobre o total emprestado no período</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5>Ações Rápidas</h5>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('parcelas.hoje') }}" class="btn btn-warning">Cobranças de Hoje</a>
                    <a href="{{ route('emprestimos.create') }}" class="btn btn-primary">Novo Empréstimo</a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-primary">Gerenciar Clientes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
