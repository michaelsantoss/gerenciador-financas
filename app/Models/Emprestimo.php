<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Emprestimo extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'emprestimos';
    protected $fillable = [
        'cliente_id', 'valor', 'taxa_juros', 'valor_total',
        'frequencia_pagamento', 'data_vencimento', 'status', 'empresa_id'
    ];

    protected $casts = [
        'data_vencimento' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'emprestimo_id');
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class, 'emprestimo_id');
    }

    public function getSaldoAttribute()
    {
        return $this->valor_total - $this->pagamentos()->sum('valor_pago');
    }

    public function atualizarStatus()
    {
        if ($this->saldo <= 0) {
            $this->update(['status' => 'pago']);
        } elseif ($this->data_vencimento->isPast()) {
            $this->update(['status' => 'atrasado']);
        } else {
            $this->update(['status' => 'ativo']);
        }
    }
}
