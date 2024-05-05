<?php

namespace App\Algorithmes;

use App\Models\BateauAdversaire;
use App\Models\BateauOrdinateur;
use App\Models\CoordonneeBateauOrdinateur;
use App\Models\Partie;
use App\Models\TypeBateau;

class DefensiveBattleship
{
    /**
     * Création des bateaux appartenant à l'ordinateur pour une partie.
     *
     * @param Partie $partie - Partie à débuter
     * @return void
     */
    public static function creerBateaux(Partie $partie) : void
    {
        $bateaux = [];
        foreach (TypeBateau::all() as $typeBateau) {
            $bateau = BateauOrdinateur::create([
                'partie_id' => $partie->id,
                'type_id' => $typeBateau->id
            ]);

            BateauAdversaire::create([
                'partie_id' => $partie->id,
                'type_id' => $typeBateau->id
            ]);

            $bateauModele = new BateauOrdinateur($bateau->toArray());
            $bateaux[$bateauModele->type->nom] = ['id' => $bateau->id, 'taille' => $bateauModele->type->taille, 'bateau' => $bateauModele];
        }

        DefensiveBattleship::strategieEcart($bateaux);
    }

    /**
     * Stratégie de placement des bateaux qui place les bateaux dans leurs "rangée" respective.
     *
     *    1 2 3 4 5 6 7 8 9 10
     *  A X X X X X - - - - -
     *  B - - X - - - - - - -
     *  C - - X - - - - - - -
     *  D - - X - - - - - - -
     *  E X - - - - - - - - -
     *  F X - - - X - - - - -
     *  G X - - - X - - - - -
     *  H x - - - X - - - - -
     *  I - - - - - - - - - -
     *  J - - - - - - - X X -
     *
     * @param array $bateaux - Bateaux de la partie
     * @return void
     */
    private static function strategieEcart(array $bateaux) : void
    {
        $bateauxHorizontals = [$bateaux['patrouilleur'], $bateaux['porte-avions']];

        $idHorizontal = array_rand($bateauxHorizontals);

        $positionH1 = rand(1, 10 - ($bateauxHorizontals[$idHorizontal]['taille']) + 1);
        for ($i = 0; $i < $bateauxHorizontals[$idHorizontal]['taille']; ++$i) {
            CoordonneeBateauOrdinateur::create([
                'coordonnee' => 'A-'.($positionH1 + $i),
                'bateau_id'=> $bateauxHorizontals[$idHorizontal]['id']
            ]);
        }

        $positionH2 = rand(1, 10 - ($bateauxHorizontals[!$idHorizontal]['taille']) + 1);
        for ($i = 0; $i < $bateauxHorizontals[!$idHorizontal]['taille']; ++$i) {
            CoordonneeBateauOrdinateur::create([
                'coordonnee' => 'J-'.($positionH2 + $i),
                'bateau_id' => $bateauxHorizontals[!$idHorizontal]['id']
            ]);
        }

        $positionSousMarin = rand(1, 10);
        for ($i = 0; $i < $bateaux['sous-marin']['taille']; ++$i) {
            CoordonneeBateauOrdinateur::create([
                'coordonnee' => chr(66 + $i).'-'.$positionSousMarin,
                'bateau_id' => $bateaux['sous-marin']['id']
            ]);
        }

        $positionCuirasse = rand(1, 10);
        $positionDestroyer = ($positionCuirasse + rand(1, 9) + 1) % 10 + 1;

        for ($i = 0; $i < $bateaux['cuirasse']['taille']; ++$i) {
            CoordonneeBateauOrdinateur::create([
                'coordonnee' => chr(69 + $i).'-'.$positionCuirasse,
                'bateau_id' => $bateaux['cuirasse']['id']
            ]);
        }

        for ($i = 0; $i < $bateaux['destroyer']['taille']; ++$i) {
            CoordonneeBateauOrdinateur::create([
                'coordonnee' => chr(70 + $i).'-'.$positionDestroyer,
                'bateau_id' => $bateaux['destroyer']['id']
            ]);
        }
    }
}
