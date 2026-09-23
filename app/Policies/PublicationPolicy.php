<?php

namespace App\Policies;

use App\Models\Publication;
use App\Models\User;

class PublicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->promotion_id !== null || $user->estEnseignant();
    }

    public function view(User $user, Publication $publication): bool
    {
        if ($user->estEnseignant()) {
            return true;
        }

        // Une publication masquée reste visible pour son auteur
        if ($publication->statut !== 'publie' && $publication->user_id !== $user->id) {
            return $user->estDelegue() && $user->promotion_id === $publication->promotion_id;
        }

        return $user->promotion_id === $publication->promotion_id;
    }

    public function create(User $user): bool
    {
        return $user->promotion_id !== null && ! $user->estEnseignant();
    }

    public function delete(User $user, Publication $publication): bool
    {
        return $user->id === $publication->user_id
            || ($user->estDelegue() && $user->promotion_id === $publication->promotion_id);
    }

    public function signaler(User $user, Publication $publication): bool
    {
        return $user->promotion_id === $publication->promotion_id
            && $user->id !== $publication->user_id;
    }

    public function designerReponse(User $user, Publication $publication): bool
    {
        return $user->id === $publication->user_id && $publication->type === 'question';
    }
}
