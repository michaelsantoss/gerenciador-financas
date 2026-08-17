@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
        @include('partials.back-button', ['href' => route('clientes.index')])
        <h1 class="mb-0">Detalhes do Cliente: <span class="dado-sensivel">{{ $cliente->nome }}</span></h1>
    </div>
    <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-outline-primary">Editar</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Informações</div>
            <div class="card-body">
                <p><strong>Nome:</strong> <span class="dado-sensivel">{{ $cliente->nome }}</span></p>
                @if($cliente->apelido)
                    <p><strong>Apelido:</strong> <span class="dado-sensivel">{{ $cliente->apelido }}</span></p>
                @endif
                <p><strong>Telefone:</strong> <span class="dado-sensivel">{{ $cliente->telefone ?? 'Não informado' }}</span></p>
                @if($cliente->email)
                    <p><strong>E-mail:</strong> <span class="dado-sensivel">{{ $cliente->email }}</span></p>
                @endif
                @if($cliente->cpf)
                    <p><strong>CPF:</strong> <span class="dado-sensivel">{{ $cliente->cpf }}</span></p>
                @endif
                @if($cliente->rg)
                    <p><strong>RG:</strong> <span class="dado-sensivel">{{ $cliente->rg }}</span></p>
                @endif
                @if($cliente->quem_indicou)
                    <p><strong>Quem indicou:</strong> {{ $cliente->quem_indicou }}</p>
                @endif
                <p><strong>Desde:</strong> {{ $cliente->created_at->format('d/m/Y') }}</p>
                @if($cliente->observacao)
                    <hr>
                    <p class="mb-0"><strong>Observação:</strong><br>{{ $cliente->observacao }}</p>
                @endif
            </div>
        </div>

        @if($cliente->endereco && array_filter($cliente->endereco->only(['cep','logradouro','numero','complemento','bairro','cidade','estado'])))
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Endereço</div>
            <div class="card-body dado-sensivel">
                @if($cliente->endereco->logradouro)
                    <p class="mb-1">{{ $cliente->endereco->logradouro }}{{ $cliente->endereco->numero ? ', ' . $cliente->endereco->numero : '' }}</p>
                @endif
                @if($cliente->endereco->complemento)
                    <p class="mb-1">{{ $cliente->endereco->complemento }}</p>
                @endif
                @if($cliente->endereco->bairro || $cliente->endereco->cidade)
                    <p class="mb-1">{{ $cliente->endereco->bairro }}{{ $cliente->endereco->bairro && $cliente->endereco->cidade ? ' - ' : '' }}{{ $cliente->endereco->cidade }} {{ $cliente->endereco->estado }}</p>
                @endif
                @if($cliente->endereco->cep)
                    <p class="mb-0">CEP: {{ $cliente->endereco->cep }}</p>
                @endif
            </div>
        </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Fotos <span class="text-muted small">({{ $cliente->fotos->count() }}/2)</span></div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @forelse($cliente->fotos as $foto)
                        <div class="text-center">
                            <a href="{{ route('clientes.arquivos.show', [$cliente->id, $foto->id]) }}" target="_blank">
                                <img src="{{ route('clientes.arquivos.show', [$cliente->id, $foto->id]) }}" alt="Foto do cliente" class="dado-sensivel rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            </a>
                            <form action="{{ route('clientes.arquivos.destroy', [$cliente->id, $foto->id]) }}" method="POST" onsubmit="return confirm('Excluir esta foto?')" class="mt-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhuma foto cadastrada.</p>
                    @endforelse
                </div>
                @if($cliente->fotos->count() < 2)
                <form action="{{ route('clientes.arquivos.store', $cliente->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="tipo" value="foto">
                    <input type="file" name="arquivo" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm" required>
                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Adicionar</button>
                </form>
                @endif
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">Anexos <span class="text-muted small">({{ $cliente->anexos->count() }}/2)</span></div>
            <div class="card-body">
                <ul class="list-unstyled mb-3">
                    @forelse($cliente->anexos as $anexo)
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <a href="{{ route('clientes.arquivos.show', [$cliente->id, $anexo->id]) }}" target="_blank" class="dado-sensivel text-truncate" style="max-width: 180px;">{{ $anexo->nome_original }}</a>
                            <form action="{{ route('clientes.arquivos.destroy', [$cliente->id, $anexo->id]) }}" method="POST" onsubmit="return confirm('Excluir este anexo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </li>
                    @empty
                        <li class="text-muted">Nenhum anexo cadastrado.</li>
                    @endforelse
                </ul>
                @if($cliente->anexos->count() < 2)
                <form action="{{ route('clientes.arquivos.store', $cliente->id) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                    @csrf
                    <input type="hidden" name="tipo" value="anexo">
                    <input type="file" name="arquivo" accept=".pdf,.jpg,.jpeg,.png" class="form-control form-control-sm" required>
                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">Adicionar</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Histórico de Empréstimos</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Saldo Devedor</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cliente->emprestimos as $emprestimo)
                        <tr>
                            <td class="text-nowrap">{{ $emprestimo->created_at->format('d/m/Y') }}</td>
                            <td class="dado-sensivel text-nowrap">R$ {{ number_format($emprestimo->valor, 2, ',', '.') }}</td>
                            <td class="dado-sensivel text-nowrap">R$ {{ number_format($emprestimo->saldo, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $emprestimo->status == 'pago' ? 'success' : 'primary' }}">
                                    {{ ucfirst($emprestimo->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('emprestimos.show', $emprestimo->id) }}" class="btn btn-sm btn-link">Ver</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Nenhum empréstimo encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
