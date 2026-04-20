<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!Auth::check() || !Auth::user()->temPermissao($permission)) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }

        return $next($request);
    }
}
