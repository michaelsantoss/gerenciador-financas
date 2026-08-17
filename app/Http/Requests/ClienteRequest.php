<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'rg' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'quem_indicou' => 'nullable|string|max:255',
            'observacao' => 'nullable|string',
            'endereco.cep' => 'nullable|string|max:10',
            'endereco.logradouro' => 'nullable|string|max:255',
            'endereco.numero' => 'nullable|string|max:20',
            'endereco.complemento' => 'nullable|string|max:255',
            'endereco.bairro' => 'nullable|string|max:255',
            'endereco.cidade' => 'nullable|string|max:255',
            'endereco.estado' => 'nullable|string|max:2',
        ];
    }
}
