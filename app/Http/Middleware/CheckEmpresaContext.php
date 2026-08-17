<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
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

        $empresa = Auth::user()->empresa;

        if (!$empresa || !$empresa->estaAtiva()) {
            Auth::logout();

            $mensagem = $empresa && $empresa->status === Empresa::STATUS_BLOQUEADO
                ? 'Empresa bloqueada. Contate o suporte.'
                : 'Empresa desativada. Contate o suporte.';

            return redirect()->route('login')->withErrors($mensagem);
        }

        return $next($request);
    }
}
