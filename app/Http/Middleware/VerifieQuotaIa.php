<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifieQuotaIa
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->peutAppelerIa()) {
            return back()
                ->withInput()
                ->with('erreur', 'Vous avez épuisé votre quota d’assistance IA pour aujourd’hui. Réessayez demain.');
        }

        return $next($request);
    }
}
