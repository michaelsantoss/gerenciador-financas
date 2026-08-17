@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('clientes.show', $cliente->id)])
                <h4 class="mb-0">Editar Cliente</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="text-muted text-uppercase small">Dados Pessoais</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $cliente->nome) }}" required>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Apelido</label>
                            <input type="text" name="apelido" class="form-control @error('apelido') is-invalid @enderror" value="{{ old('apelido', $cliente->apelido) }}">
                            @error('apelido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $cliente->telefone) }}" placeholder="(00) 00000-0000">
                            @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $cliente->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Quem indicou</label>
                            <input type="text" name="quem_indicou" class="form-control @error('quem_indicou') is-invalid @enderror" value="{{ old('quem_indicou', $cliente->quem_indicou) }}">
                            @error('quem_indicou') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf', $cliente->cpf) }}" placeholder="000.000.000-00">
                            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">RG</label>
                            <input type="text" name="rg" class="form-control @error('rg') is-invalid @enderror" value="{{ old('rg', $cliente->rg) }}">
                            @error('rg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control @error('observacao') is-invalid @enderror" rows="3">{{ old('observacao', $cliente->observacao) }}</textarea>
                        @error('observacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    <h6 class="text-muted text-uppercase small">Endereço</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">CEP</label>
                            <input type="text" name="endereco[cep]" class="form-control @error('endereco.cep') is-invalid @enderror" value="{{ old('endereco.cep', $cliente->endereco->cep ?? '') }}">
                            @error('endereco.cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-7 mb-3">
                            <label class="form-label">Logradouro</label>
                            <input type="text" name="endereco[logradouro]" class="form-control @error('endereco.logradouro') is-invalid @enderror" value="{{ old('endereco.logradouro', $cliente->endereco->logradouro ?? '') }}">
                            @error('endereco.logradouro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="endereco[numero]" class="form-control @error('endereco.numero') is-invalid @enderror" value="{{ old('endereco.numero', $cliente->endereco->numero ?? '') }}">
                            @error('endereco.numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Complemento</label>
                            <input type="text" name="endereco[complemento]" class="form-control @error('endereco.complemento') is-invalid @enderror" value="{{ old('endereco.complemento', $cliente->endereco->complemento ?? '') }}">
                            @error('endereco.complemento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="endereco[bairro]" class="form-control @error('endereco.bairro') is-invalid @enderror" value="{{ old('endereco.bairro', $cliente->endereco->bairro ?? '') }}">
                            @error('endereco.bairro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Cidade</label>
                            <input type="text" name="endereco[cidade]" class="form-control @error('endereco.cidade') is-invalid @enderror" value="{{ old('endereco.cidade', $cliente->endereco->cidade ?? '') }}">
                            @error('endereco.cidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="form-label">UF</label>
                            <input type="text" name="endereco[estado]" maxlength="2" class="form-control @error('endereco.estado') is-invalid @enderror" value="{{ old('endereco.estado', $cliente->endereco->estado ?? '') }}">
                            @error('endereco.estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-success btn-lg">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
