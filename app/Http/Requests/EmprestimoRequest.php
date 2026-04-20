<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmprestimoRequest extends FormRequest
{
    public function authorize() { return true; }

    protected function prepareForValidation()
    {
        $this->merge([
            'valor' => str_replace(',', '.', $this->valor),
            'taxa_juros' => str_replace(',', '.', $this->taxa_juros),
        ]);
    }

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0.01',
            'taxa_juros' => 'nullable|numeric|min:0',
            'frequencia_pagamento' => 'required|in:semanal,mensal',
        ];
    }
}
