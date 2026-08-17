<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AdminActivityLog extends Model
{
    protected $table = 'admin_activity_logs';
    protected $fillable = ['user_id', 'empresa_id', 'acao', 'detalhes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public static function registrar(string $acao, ?Empresa $empresa = null, ?string $detalhes = null)
    {
        return static::create([
            'user_id' => Auth::id(),
            'empresa_id' => $empresa?->id,
            'acao' => $acao,
            'detalhes' => $detalhes,
        ]);
    }
}
