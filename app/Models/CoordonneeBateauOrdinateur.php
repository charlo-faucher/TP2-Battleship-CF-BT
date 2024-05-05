<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoordonneeBateauOrdinateur extends Model
{
    protected $table = 'coordonnees_bateaux_ordinateur';
    protected $fillable = ['coordonnee', 'bateau_id'];

    public function bateauOrdinateur() : BelongsTo
    {
        return $this->belongsTo(BateauOrdinateur::class, 'bateau_id');
    }
}
