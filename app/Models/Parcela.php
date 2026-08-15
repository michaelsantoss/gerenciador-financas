<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Parcela extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'parcelas';
    protected $fillable = ['emprestimo_id', 'valor', 'valor_pago', 'data_vencimento', 'data_pagamento', 'status', 'empresa_id'];
    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'datetime',
        'valor' => 'decimal:2',
        'valor_pago' => 'decimal:2',
    ];

    public function emprestimo()
    {
        return $this->belongsTo(Emprestimo::class, 'emprestimo_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'parcela_id');
    }

    public function getValorPendenteAttribute()
    {
        return $this->valor - $this->valor_pago;
    }
}
