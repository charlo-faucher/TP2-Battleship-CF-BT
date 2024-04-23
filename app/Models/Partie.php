<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partie extends Model
{

    public function bateauxOrdinateur() : HasMany
    {
        return $this->hasMany(BateauOrdinateur::class);
    }
}
