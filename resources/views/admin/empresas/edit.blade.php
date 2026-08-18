@extends('layouts.admin')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex align-items-center gap-2">
                @include('partials.back-button', ['href' => route('admin.empresas.index')])
                <h4 class="mb-0">Editar Empresa</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.empresas.update', $empresa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $empresa->nome) }}" required>
                        @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" class="form-control @error('cnpj') is-invalid @enderror" value="{{ old('cnpj', $empresa->cnpj) }}">
                        @error('cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plano</label>
                        <select name="plano" class="form-select @error('plano') is-invalid @enderror" required>
                            @foreach(\App\Models\Empresa::PLANOS as $slug => $info)
                                <option value="{{ $slug }}" {{ old('plano', $empresa->plano) == $slug ? 'selected' : '' }}>
                                    {{ $info['label'] }} — R$ {{ number_format($info['preco'], 0, ',', '.') }}/mês
                                    ({{ $info['max_clientes'] ?? 'ilimitado' }} clientes, {{ $info['max_admins'] ?? 'ilimitado' }} usuários)
                                </option>
                            @endforeach
                        </select>
                        @error('plano') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">
                            Uso atual: {{ $empresa->clientes()->count() }} cliente(s), {{ $empresa->usuarios()->count() }} usuário(s).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach(['ativo' => 'Ativa', 'inativo' => 'Inativa', 'bloqueado' => 'Bloqueada'] as $valor => $label)
                                <option value="{{ $valor }}" {{ old('status', $empresa->status) == $valor ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Inativa/Bloqueada impedem login dos usuários dessa empresa.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea name="observacao" class="form-control @error('observacao') is-invalid @enderror" rows="3" placeholder="Motivo da inativação/bloqueio, ou qualquer anotação relevante">{{ old('observacao', $empresa->observacao) }}</textarea>
                        @error('observacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
