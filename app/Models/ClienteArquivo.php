<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use App\Traits\BelongsToEmpresa;

class ClienteArquivo extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'cliente_arquivos';
    protected $fillable = [
        'cliente_id', 'tipo', 'nome_original', 'caminho', 'mime', 'tamanho', 'empresa_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    protected static function booted()
    {
        static::deleting(function (ClienteArquivo $arquivo) {
            Storage::disk('local')->delete($arquivo->caminho);
        });
    }
}
