<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToEmpresa;

class Parcela extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'parcelas';
    protected $fillable = ['emprestimo_id', 'valor', 'data_vencimento', 'status', 'empresa_id'];
    protected $casts = ['data_vencimento' => 'date'];

    public function emprestimo()
    {
        return $this->belongsTo(Emprestimo::class, 'emprestimo_id');
    }
}
