<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CoordonneeBateauAdversaire extends Model
{
    protected $table = 'coordonnees_bateaux_adversaires';
    protected $fillable = ['coordonnee', 'partie_id', 'resultat', 'source_id'];

    protected function source() : ?BelongsTo
    {
        return $this->BelongsTo(CoordonneeBateauAdversaire::class, 'source_id');
    }
}
