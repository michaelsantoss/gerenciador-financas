<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmprestimoRequest extends FormRequest
{
    public function authorize() { return true; }

    protected function prepareForValidation()
    {
        $parcelas = collect($this->parcelas ?? [])->map(function ($parcela) {
            $parcela['valor'] = str_replace(',', '.', $parcela['valor'] ?? '');
            $parcela['pago'] = filter_var($parcela['pago'] ?? false, FILTER_VALIDATE_BOOLEAN);
            return $parcela;
        })->all();

        $this->merge([
            'valor' => str_replace(',', '.', $this->valor),
            'taxa_juros' => str_replace(',', '.', $this->taxa_juros),
            'parcelas' => $parcelas,
        ]);
    }

    public function rules()
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'valor' => 'required|numeric|min:0.01',
            'taxa_juros' => 'nullable|numeric|min:0',
            'frequencia_pagamento' => 'required|in:semanal,mensal',
            'numero_parcelas' => 'required_if:frequencia_pagamento,semanal|integer|min:1|max:52',
            'parcelas' => 'nullable|array',
            'parcelas.*.valor' => 'required_with:parcelas|numeric|min:0.01',
            'parcelas.*.data_vencimento' => 'required_with:parcelas|date',
            'parcelas.*.pago' => 'nullable|boolean',
            'parcelas.*.data_pagamento' => 'nullable|date',
        ];
    }
}
