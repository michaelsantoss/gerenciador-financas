<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Models\Cliente;
use App\Models\Parcela;
use App\Http\Requests\EmprestimoRequest;
use App\Services\EmprestimoService;
use Illuminate\Http\Request;

class EmprestimoWebController extends Controller
{
    public function index(Request $request)
    {
        $query = Emprestimo::with('cliente');

        if ($request->has('status') && $request->status !== null) {
            $query->where('status', $request->status);
        }

        $emprestimos = $query->orderBy('data_vencimento', 'asc')->get();
        return view('emprestimos.index', compact('emprestimos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('emprestimos.create', compact('clientes'));
    }

    public function store(EmprestimoRequest $request, EmprestimoService $service)
    {
        try {
            $service->criar($request->validated());
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
        $clientes = Cliente::all();
        return view('emprestimos.edit', compact('emprestimo', 'clientes'));
    }

    public function update(Request $request, Emprestimo $emprestimo)
    {
        $request->merge([
            'valor' => str_replace(',', '.', (string) $request->input('valor')),
            'taxa_juros' => str_replace(',', '.', (string) $request->input('taxa_juros')),
        ]);

        $dados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0.01',
            'taxa_juros' => 'nullable|numeric|min:0',
        ]);

        $emprestimo->update($dados);

        return redirect()->route('emprestimos.show', $emprestimo->id)->with('success', 'Empréstimo atualizado com sucesso!');
    }

    public function quitarParcela(Request $request, Parcela $parcela)
    {
        $resultado = app(ParcelaController::class)->quitar($request, $parcela);

        if ($resultado instanceof \Illuminate\Http\JsonResponse) {
            return back()->withErrors(['error' => $resultado->getData()->message]);
        }

        return back()->with('success', $resultado->status === 'pago' ? 'Parcela quitada com sucesso!' : 'Pagamento parcial registrado com sucesso!');
    }

    public function desfazerParcela(Parcela $parcela)
    {
        $resultado = app(ParcelaController::class)->desfazer($parcela);

        if ($resultado instanceof \Illuminate\Http\JsonResponse) {
            return back()->withErrors(['error' => $resultado->getData()->message]);
        }

        return back()->with('success', 'Pagamento desfeito com sucesso!');
    }

    public function renovar(Emprestimo $emprestimo, EmprestimoService $service)
    {
        try {
            $service->renovar($emprestimo);
            return back()->with('success', 'Empréstimo renovado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Emprestimo $emprestimo)
    {
        $emprestimo->delete();
        return redirect()->route('emprestimos.index')->with('success', 'Empréstimo excluído com sucesso!');
    }
}
