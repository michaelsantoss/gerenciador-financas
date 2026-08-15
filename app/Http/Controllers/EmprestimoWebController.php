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

    public function destroy(Emprestimo $emprestimo)
    {
        $emprestimo->delete();
        return redirect()->route('emprestimos.index')->with('success', 'Empréstimo excluído com sucesso!');
    }
}
