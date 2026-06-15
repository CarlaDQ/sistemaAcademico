<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $userRol = session('user_rol');

        if (!$userRol) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($userRol === $role) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
