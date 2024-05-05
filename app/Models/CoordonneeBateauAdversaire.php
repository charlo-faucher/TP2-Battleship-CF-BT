<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle d'une coordonnée d'un missile tiré par l'IA.
 *
 */
class CoordonneeBateauAdversaire extends Model
{
    /**
     * @var string Nom de la table.
     */
    protected $table = 'coordonnees_bateaux_adversaires';

    /**
     * @var string[] Champs qui sont modifiables.
     */
    protected $fillable = ['coordonnee', 'partie_id', 'resultat', 'source_id'];

    /**
     * @return BelongsTo|null Coordonnée d'où provient le tir.
     */
    public function source() : ?BelongsTo
    {
        return $this->BelongsTo(CoordonneeBateauAdversaire::class, 'source_id');
    }

    /**
     * @return HasMany|null Cases dont ce missile est la source.
     */
    public function casesEnfants() : ?HasMany
    {
        return $this->HasMany(CoordonneeBateauAdversaire::class, 'source_id');
    }
}
