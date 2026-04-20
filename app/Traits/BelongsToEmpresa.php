<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToEmpresa
{
    protected static function bootBelongsToEmpresa()
    {
        // Filtra consultas pelo id da empresa do usuário logado
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (Auth::hasUser() && Auth::user()->empresa_id) {
                $builder->where($builder->getQuery()->from . '.empresa_id', Auth::user()->empresa_id);
            }
        });

        // Define empresa_id automaticamente ao criar um registro
        static::creating(function ($model) {
            if (Auth::hasUser() && Auth::user()->empresa_id && !$model->empresa_id) {
                $model->empresa_id = Auth::user()->empresa_id;
            }
        });
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }
}
