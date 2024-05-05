<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle d'un bateau appartenant à l'adversaire.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class BateauAdversaire extends Model
{
    /**
     * @var string Nom de la table.
     */
    protected $table = 'bateaux_adversaires';

    /**
     * @var string[] Champs qui sont modifiables.
     */
    protected $fillable = ['partie_id', 'type_id', 'est_coule'];
}
