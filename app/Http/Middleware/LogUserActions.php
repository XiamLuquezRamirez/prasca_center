<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\log;
use Illuminate\Support\Facades\Auth;

class LogUserActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        $response = $next($request);

        // Obtener usuario autenticado
        $user = Auth::user();

        // Registrar acción solo si es un método POST, PUT, DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE','GET'])) {
            log::create([
                'user_id' => $user ? $user->id : null,
                'accion'  => $request->path(),
                'ip'      => $request->ip(),
                'detalles' => json_encode($request->all())
            ]);
        }

        return $response;
    }
}
