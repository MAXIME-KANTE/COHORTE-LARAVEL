<?php

namespace App\Policies;

use App\Models\Reponse;
use App\Models\User;

class ReponsePolicy
{
    public function delete(User $user, Reponse $reponse): bool
    {
        return $user->id === $reponse->user_id
            || ($user->estDelegue() && $user->promotion_id === $reponse->publication->promotion_id);
    }
}
