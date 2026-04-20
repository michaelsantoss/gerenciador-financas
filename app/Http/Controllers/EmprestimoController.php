<?php

namespace App\Http\Controllers;

use App\Models\Emprestimo;
use App\Http\Requests\EmprestimoRequest;
use App\Services\EmprestimoService;

class EmprestimoController extends Controller
{
    public function index()
    {
        return Emprestimo::with('cliente')->get();
    }

    public function store(EmprestimoRequest $request, EmprestimoService $service)
    {
        return $service->criar($request->validated());
    }

    public function show(Emprestimo $emprestimo)
    {
        return $emprestimo->load(['cliente', 'parcelas', 'pagamentos']);
    }
}
