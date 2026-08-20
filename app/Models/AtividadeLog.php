<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AtividadeLog extends Model
{
    use BelongsToEmpresa;

    protected $table = 'atividade_logs';
    protected $fillable = ['empresa_id', 'user_id', 'cliente_id', 'acao', 'detalhes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public static function registrar(string $acao, ?Cliente $cliente = null, ?string $detalhes = null)
    {
        return static::create([
            'user_id' => Auth::id(),
            'cliente_id' => $cliente?->id,
            'acao' => $acao,
            'detalhes' => $detalhes,
        ]);
    }
}
