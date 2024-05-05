<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordonneeBateauOrdinateur extends Model
{
    /**
     * @var string Nom de la table.
     */
    protected $table = 'coordonnees_bateaux_ordinateur';

    /**
     * @var string[] Champs qui sont modifiables.
     */
    protected $fillable = ['coordonnee', 'bateau_id'];

    /**
     * @return BelongsTo Bateau de la coordonnée,
     */
    public function bateauOrdinateur() : BelongsTo
    {
        return $this->belongsTo(BateauOrdinateur::class, 'bateau_id');
    }
}
