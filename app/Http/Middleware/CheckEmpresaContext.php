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

        if (!Auth::user()->empresa || !Auth::user()->empresa->ativo) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Empresa desativada. Contate o suporte.');
        }

        return $next($request);
    }
}
