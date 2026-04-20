<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Cliente extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'clientes';
    protected $fillable = ['nome', 'telefone', 'empresa_id'];

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class, 'cliente_id');
    }
}
