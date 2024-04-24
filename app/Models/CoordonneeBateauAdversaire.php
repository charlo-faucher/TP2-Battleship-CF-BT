<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoordonneeBateauAdversaire extends Model
{
    protected $table = 'coordonnees_bateaux_adversaires';
    protected $fillable = ['coordonnee', 'bateau_id', 'partie_id', 'resultat'];
}
