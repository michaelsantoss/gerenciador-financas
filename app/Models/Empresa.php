<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_BLOQUEADO = 'bloqueado';

    protected $fillable = ['nome', 'cnpj', 'status', 'observacao'];

    public function estaAtiva()
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'empresa_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'empresa_id');
    }

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class, 'empresa_id');
    }
}
