<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $fillable = ['nome', 'telefone'];

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class, 'cliente_id');
    }
}
