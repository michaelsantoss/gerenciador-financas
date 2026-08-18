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

    const PLANOS = [
        'basico' => ['label' => 'Básico', 'preco' => 20, 'max_clientes' => 20, 'max_admins' => 1],
        'intermediario' => ['label' => 'Intermediário', 'preco' => 50, 'max_clientes' => 50, 'max_admins' => 2],
        'avancado' => ['label' => 'Avançado', 'preco' => 100, 'max_clientes' => 150, 'max_admins' => 5],
        'full' => ['label' => 'Full', 'preco' => 300, 'max_clientes' => null, 'max_admins' => null],
    ];

    protected $fillable = ['nome', 'cnpj', 'status', 'observacao', 'plano'];

    public function estaAtiva()
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function nomePlano()
    {
        return self::PLANOS[$this->plano]['label'] ?? ucfirst($this->plano);
    }

    public function limiteClientes()
    {
        return self::PLANOS[$this->plano]['max_clientes'] ?? null;
    }

    public function limiteAdmins()
    {
        return self::PLANOS[$this->plano]['max_admins'] ?? null;
    }

    public function atingiuLimiteClientes()
    {
        $limite = $this->limiteClientes();
        return $limite !== null && $this->clientes()->count() >= $limite;
    }

    public function atingiuLimiteAdmins()
    {
        $limite = $this->limiteAdmins();
        return $limite !== null && $this->usuarios()->count() >= $limite;
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
