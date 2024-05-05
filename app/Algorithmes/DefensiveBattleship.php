<?php

namespace App\Algorithmes;

use App\Models\BateauAdversaire;
use App\Models\BateauOrdinateur;
use App\Models\CoordonneeBateauOrdinateur;
use App\Models\Partie;
use App\Models\TypeBateau;

class DefensiveBattleship
{
    public static function creerBateaux(Partie $partie) : void
    {
        $lettres = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $lettreVal = 0;
        foreach (TypeBateau::all() as $typeBateau) {
            BateauOrdinateur::create([
                'partie_id' => $partie->id,
                'type_id' => $typeBateau->id
            ]);

            BateauAdversaire::create([
                'partie_id' => $partie->id,
                'type_id' => $typeBateau->id
            ]);

            $query = BateauOrdinateur::query()->where('partie_id',  $partie->id)->where('type_id',  $typeBateau->id);
            $bateau = $query->get()[0];

            for ($i = 0; $i < $typeBateau->taille; $i++) {
                CoordonneeBateauOrdinateur::create([
                    'coordonnee' => $lettres[$lettreVal].'-'.($i + 1),
                    'bateau_id' => $bateau->id
                ]);
            }
            ++$lettreVal;
        }
    }
}
