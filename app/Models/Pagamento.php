<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Pagamento extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'pagamentos';
    protected $fillable = ['emprestimo_id', 'valor_pago', 'data_pagamento', 'observacoes', 'empresa_id'];

    protected $casts = [
        'data_pagamento' => 'date',
    ];

    public function emprestimo()
    {
        return $this->belongsTo(Emprestimo::class, 'emprestimo_id');
    }

    protected static function booted()
    {
        static::saved(fn ($pagamento) => $pagamento->emprestimo->atualizarStatus());
        static::deleted(fn ($pagamento) => $pagamento->emprestimo->atualizarStatus());
    }
}
