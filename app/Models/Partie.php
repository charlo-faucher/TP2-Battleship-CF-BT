<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;

class Partie extends Model
{
    protected $fillable = ['adversaire', 'est_finie', 'user_id'];

    public function bateauxOrdinateur() : HasMany
    {
        return $this->HasMany(BateauOrdinateur::class);
    }

    public function bateauxAdversaires() : HasMany
    {
        return $this->HasMany(BateauAdversaire::class);
    }

    public function coordonneesBateauxOrdinateur() : HasManyThrough
    {
        return $this->HasManyThrough(CoordonneeBateauOrdinateur::class, BateauOrdinateur::class, 'partie_id', 'bateau_id');
    }

    public function coordonneesBateauxAdversaire() : HasMany
    {
        return $this->HasMany(CoordonneeBateauAdversaire::class);
    }
}
