<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BateauOrdinateur extends Model
{
    protected $table = 'bateaux_ordinateur';

    public function coordonnees() : HasMany
    {
        return  $this->hasMany(CoordonneeBateauOrdinateur::class);
    }
}
