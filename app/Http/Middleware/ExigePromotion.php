<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExigePromotion
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = $request->user();

        if (! $utilisateur) {
            return $next($request);
        }

        // L'enseignant n'a pas de promotion_id : il ne doit jamais entrer
        // dans un fil de promotion, il passe par son propre module.
        if ($utilisateur->estEnseignant()) {
            return redirect()->route('enseignant.promotions.index');
        }

        if (! $utilisateur->promotion_id) {
            return redirect()
                ->route('promotion.rejoindre')
                ->with('erreur', 'Saisissez le code d\'invitation de votre promotion pour continuer.');
        }

        return $next($request);
    }
}
