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

    public function telefoneWhatsapp(): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $this->telefone);

        if (strlen($digitos) < 10) {
            return null;
        }

        if (!str_starts_with($digitos, '55')) {
            $digitos = '55' . $digitos;
        }

        return $digitos;
    }

    public function linkWhatsapp(string $mensagem): ?string
    {
        $numero = $this->telefoneWhatsapp();

        return $numero ? 'https://wa.me/' . $numero . '?text=' . urlencode($mensagem) : null;
    }
}
