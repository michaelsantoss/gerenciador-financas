<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Cliente extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'clientes';
    protected $fillable = [
        'nome', 'apelido', 'telefone', 'cpf', 'rg', 'email',
        'quem_indicou', 'observacao', 'empresa_id',
    ];

    public function emprestimos()
    {
        return $this->hasMany(Emprestimo::class, 'cliente_id');
    }

    public function endereco()
    {
        return $this->hasOne(Endereco::class);
    }

    public function arquivos()
    {
        return $this->hasMany(ClienteArquivo::class);
    }

    public function fotos()
    {
        return $this->arquivos()->where('tipo', 'foto');
    }

    public function anexos()
    {
        return $this->arquivos()->where('tipo', 'anexo');
    }
}
