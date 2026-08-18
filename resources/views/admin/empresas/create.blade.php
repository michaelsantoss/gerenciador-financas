@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('admin.empresas.index')])
                <h4 class="mb-0">Nova Empresa</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.empresas.store') }}" method="POST">
                    @csrf

                    <h6 class="text-muted text-uppercase small">Dados da Empresa</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required>
                            @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj') }}">
                            @error('cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plano</label>
                        <select name="plano" class="form-select @error('plano') is-invalid @enderror" required>
                            @foreach(\App\Models\Empresa::PLANOS as $slug => $info)
                                <option value="{{ $slug }}" {{ old('plano', 'basico') == $slug ? 'selected' : '' }}>
                                    {{ $info['label'] }} — R$ {{ number_format($info['preco'], 0, ',', '.') }}/mês
                                    ({{ $info['max_clientes'] ?? 'ilimitado' }} clientes, {{ $info['max_admins'] ?? 'ilimitado' }} usuários)
                                </option>
                            @endforeach
                        </select>
                        @error('plano') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-4">

                    <h6 class="text-muted text-uppercase small">Primeiro Usuário (admin da empresa)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="usuario_nome" class="form-control @error('usuario_nome') is-invalid @enderror" value="{{ old('usuario_nome') }}" required>
                            @error('usuario_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="usuario_email" class="form-control @error('usuario_email') is-invalid @enderror" value="{{ old('usuario_email') }}" required>
                            @error('usuario_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Perfil</label>
                            <select name="usuario_role_id" class="form-select @error('usuario_role_id') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('usuario_role_id') == $role->id ? 'selected' : '' }}>{{ $role->nome }}</option>
                                @endforeach
                            </select>
                            @error('usuario_role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="usuario_senha" class="form-control @error('usuario_senha') is-invalid @enderror" required>
                            @error('usuario_senha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Confirmar Senha</label>
                            <input type="password" name="usuario_senha_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-success btn-lg">Criar Empresa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
