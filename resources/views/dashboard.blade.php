@extends('layouts.app')

@section('content')
<h1 class="mb-4">Dashboard: {{ Auth::user()->empresa->nome }}</h1>

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
        <div class="card bg-danger text-white shadow-sm mb-4">
            <div class="card-body">
                <h6>Empréstimos em Atraso</h6>
                <h3>{{ $qtdEmprestimosAtrasados }}</h3>
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
                    <a href="{{ route('emprestimos.create') }}" class="btn btn-primary">Novo Empréstimo</a>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-primary">Gerenciar Clientes</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
