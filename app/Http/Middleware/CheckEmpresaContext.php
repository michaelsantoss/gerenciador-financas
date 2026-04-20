<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckEmpresaContext
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->empresa_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Acesso negado: contexto de empresa inválido.');
        }

        return $next($request);
    }
}
