<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Endereco extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'enderecos';
    protected $fillable = [
        'cliente_id', 'cep', 'logradouro', 'numero', 'complemento',
        'bairro', 'cidade', 'estado', 'empresa_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
