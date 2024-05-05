<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modèle d'un bateau appartenant à l'ordinateur.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class BateauOrdinateur extends Pivot
{
    /**
     * @var string Nom de la table.
     */
    protected $table = 'bateaux_ordinateur';

    /**
     * @var bool Si l'identifiant est incrémentable.
     */
    public $incrementing = true;

    /**
     * @var string[] Champs qui sont modifiables.
     */
    protected $fillable = ['partie_id', 'type_id'];

    /**
     * Partie qui possède le bateau.
     *
     * @return BelongsTo Partie.
     */
    public function partie() : BelongsTo
    {
        return $this->belongsTo(Partie::class);
    }

    /**
     * Type du bateau.
     *
     * @return BelongsTo Type de bateau.
     */
    public function type() : BelongsTo
    {
        return $this->belongsTo(TypeBateau::class);
    }

    /**
     * Coordonnées où se trouve le bateau.
     *
     * @return HasMany Coordonnées du bateau.
     */
    public function coordonnees() : HasMany
    {
        return $this->hasMany(CoordonneeBateauOrdinateur::class, 'bateau_id', 'id');
    }
}
