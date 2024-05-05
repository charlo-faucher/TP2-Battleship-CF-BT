<?php

namespace App\Policies;

use App\Models\Partie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Classe pour les policies d'une partie.
 */
class PartiePolicy
{
    /**
     * Vérifie si la partie appartient à un utilisateur.
     */
    public function verifierConnexion(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }
}
