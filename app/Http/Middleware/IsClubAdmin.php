<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsClubAdmin
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
        // Vérifié si l'utilisateur connecté est administrateur de son club
        // Si oui, on continue jusqu'à la prochaine requête
        // Sinon on renvoie une 403

        if ($request->user()->is_club_admin == true) {
            return $next($request);
        } else {
            return response()->json(['message' => 'Accès refusé à la ressource demandée'], 403);
        }
    }
}
