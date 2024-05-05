<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle d'un type de bateau.
 */
class TypeBateau extends Model
{
    /**
     * @var string Nom de la table.
     */
    protected $table = 'types_bateaux';

    /**
     * @return HasMany Parties qui utilise les types de bateaux.
     */
    public function parties(): HasMany
    {
        return $this->HasMany(BateauOrdinateur::class);
    }
}
