<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Cliente;
use App\Models\Parcela;
use App\Models\AtividadeLog;
use App\Http\Requests\EmprestimoRequest;
use App\Services\EmprestimoService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmprestimoWebController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->has('status') ? $request->status : 'ativo';

        $query = Emprestimo::with('cliente');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $emprestimos = $query->orderBy('data_vencimento', 'asc')->get();
        return view('emprestimos.index', compact('emprestimos', 'status'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('emprestimos.create', compact('clientes'));
    }

    public function store(EmprestimoRequest $request, EmprestimoService $service)
    {
        try {
            $emprestimo = $service->criar($request->validated());
            AtividadeLog::registrar('emprestimo.criado', $emprestimo->cliente, "Empréstimo #{$emprestimo->id} criado para \"{$emprestimo->cliente->nome}\"");
            return redirect()->route('emprestimos.index')->with('success', 'Empréstimo criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Erro ao criar: ' . $e->getMessage()]);
        }
    }

    public function show(Emprestimo $emprestimo)
    {
        $emprestimo->load(['cliente', 'parcelas', 'pagamentos']);
        return view('emprestimos.show', compact('emprestimo'));
    }

    public function edit(Emprestimo $emprestimo)
    {
        $emprestimo->load('parcelas');
        $clientes = Cliente::all();
        return view('emprestimos.edit', compact('emprestimo', 'clientes'));
    }

    public function update(Request $request, Emprestimo $emprestimo, EmprestimoService $service)
    {
        $parcelas = collect($request->input('parcelas', []))->map(function ($parcela) {
            $parcela['valor'] = str_replace(',', '.', $parcela['valor'] ?? '');
            return $parcela;
        })->all();

        $request->merge([
            'valor' => str_replace(',', '.', (string) $request->input('valor')),
            'taxa_juros' => str_replace(',', '.', (string) $request->input('taxa_juros')),
            'parcelas' => $parcelas,
        ]);

        $dados = $request->validate([
            'cliente_id' => [
                'required',
                Rule::exists('clientes', 'id')->where('empresa_id', $request->user()->empresa_id),
            ],
            'valor' => 'required|numeric|min:0.01',
            'taxa_juros' => 'nullable|numeric|min:0',
            'parcelas' => 'required|array|min:1',
            'parcelas.*.id' => 'nullable|integer',
            'parcelas.*.valor' => 'required|numeric|min:0.01',
            'parcelas.*.data_vencimento' => 'required|date',
            'parcelas.*.remover' => 'nullable|boolean',
        ]);

        $emprestimo->update([
            'cliente_id' => $dados['cliente_id'],
            'valor' => $dados['valor'],
            'taxa_juros' => $dados['taxa_juros'] ?? $emprestimo->taxa_juros,
        ]);

        try {
            $service->atualizarParcelas($emprestimo, $dados['parcelas']);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }

        AtividadeLog::registrar('emprestimo.editado', $emprestimo->cliente, "Empréstimo #{$emprestimo->id} atualizado");

        return redirect()->route('emprestimos.show', $emprestimo->id)->with('success', 'Empréstimo atualizado com sucesso!');
    }

    public function quitarParcela(Request $request, Parcela $parcela)
    {
        $resultado = app(ParcelaController::class)->quitar($request, $parcela);

        if ($resultado instanceof \Illuminate\Http\JsonResponse) {
            return back()->withErrors(['error' => $resultado->getData()->message]);
        }

        $parcela->loadMissing('emprestimo.cliente');
        AtividadeLog::registrar(
            'parcela.quitada',
            $parcela->emprestimo->cliente,
            "Parcela #{$parcela->id} do empréstimo #{$parcela->emprestimo_id} " . ($resultado->status === 'pago' ? 'quitada' : 'paga parcialmente')
        );

        return back()->with('success', $resultado->status === 'pago' ? 'Parcela quitada com sucesso!' : 'Pagamento parcial registrado com sucesso!');
    }

    public function desfazerParcela(Parcela $parcela)
    {
        $resultado = app(ParcelaController::class)->desfazer($parcela);

        if ($resultado instanceof \Illuminate\Http\JsonResponse) {
            return back()->withErrors(['error' => $resultado->getData()->message]);
        }

        $parcela->loadMissing('emprestimo.cliente');
        AtividadeLog::registrar('parcela.pagamento_desfeito', $parcela->emprestimo->cliente, "Pagamento da parcela #{$parcela->id} do empréstimo #{$parcela->emprestimo_id} desfeito");

        return back()->with('success', 'Pagamento desfeito com sucesso!');
    }

    public function renovar(Emprestimo $emprestimo, EmprestimoService $service)
    {
        try {
            $service->renovar($emprestimo);
            AtividadeLog::registrar('emprestimo.renovado', $emprestimo->cliente, "Empréstimo #{$emprestimo->id} renovado");
            return back()->with('success', 'Empréstimo renovado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Emprestimo $emprestimo)
    {
        AtividadeLog::registrar('emprestimo.excluido', $emprestimo->cliente, "Empréstimo #{$emprestimo->id} excluído");
        $emprestimo->delete();
        return redirect()->route('emprestimos.index')->with('success', 'Empréstimo excluído com sucesso!');
    }
}
