<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Modèle d'une partie de Battleship.
 */
class Partie extends Model
{
    /**
     * @var string[] Champs qui sont modifiables.
     */
    protected $fillable = ['adversaire', 'est_finie', 'user_id'];

    /**
     * @return HasMany Bateaux de l'ordinateur.
     */
    public function bateauxOrdinateur() : HasMany
    {
        return $this->HasMany(BateauOrdinateur::class);
    }

    /**
     * @return HasMany Bateau de l'adversaire.
     */
    public function bateauxAdversaires() : HasMany
    {
        return $this->HasMany(BateauAdversaire::class);
    }

    /**
     * @return HasManyThrough Coordonnées des bateaux de l'ordinateur.
     */
    public function coordonneesBateauxOrdinateur() : HasManyThrough
    {
        return $this->HasManyThrough(CoordonneeBateauOrdinateur::class, BateauOrdinateur::class, 'partie_id', 'bateau_id');
    }

    /**
     * @return HasMany Coordonnées des bateaux/missiles sur la grille de l'adversaire.
     */
    public function coordonneesBateauxAdversaire() : HasMany
    {
        return $this->HasMany(CoordonneeBateauAdversaire::class);
    }
}
