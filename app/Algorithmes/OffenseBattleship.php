<?php

namespace App\Algorithmes;

use App\Models\CoordonneeBateauAdversaire;
use App\Models\Partie;
use App\Models\TypeBateau;

class OffenseBattleship
{
    public static function calculerMeilleurCoup(int $partieId): string
    {
        // Get les tailles et les noms des bateaux qu'il reste à attaquer.
        $partie = Partie::findOrFail($partieId);
        $queryTypes = TypeBateau::query()
            ->select('nom', 'taille')
            ->join('bateaux_adversaires', 'bateaux_adversaires.type_id', '=', 'types_bateaux.id')
            ->where('partie_id', $partie->id);

        // Dictionnaire nom/taille des bateaux.
        // $bateaux = $queryTypes->get()->pluck('taille','nom')->toArray();

        // Get les tailles des bateaux qu'il reste à attaquer.
        $bateauxTypesPasCoules = $queryTypes->where('est_coule', false)->get();
        $bateauxTailles = $bateauxTypesPasCoules->pluck('taille')->toArray();

        // Get les coordonnees où l'ordinateur a déjà envoyé des missiles
        $queryMissiles = CoordonneeBateauAdversaire::query()->where('partie_id', $partieId);
        $missiles = $queryMissiles->get();
        $nbMissiles = count($queryMissiles->where('resultat', '!=', 0)->get());

        $bateauxTypesCoules = $queryTypes->where('est_coule', true)->get();
        $nbCasesBateauxTouchees = array_sum($bateauxTypesCoules->pluck('taille')->toArray());
        dd($bateauxTypesCoules->toArray());

        // TODO : est_coule = 1 lorsque coule est donné
        if ($nbCasesBateauxTouchees < $nbMissiles)
        {
            // Logique du Target mode

           dd($nbBateauxTouchees." - ".$nbMissiles);
            return "A-1";
        }
        else
        {
            // Logique du Hunt mode
            $missilesDict = $missiles->pluck('resultat', 'coordonnee')->toArray();
            return OffenseBattleship::huntMode($bateauxTailles, $missilesDict);
        }
    }

    private static function debugMap(array $map): void
    {
        $affichage = " ";

        for ($i = 1; $i <= 10; $i++) {
            $affichage .= ' ' . str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        $coord = 0;

        foreach ($map as $pos) {
            if ($coord % 10 == 0) {
                $affichage .= "\n";
                $affichage .= chr(65 + $coord / 10);
            }

            $affichage .= ' ' . str_pad($pos, 2, '0', STR_PAD_LEFT);

            ++$coord;
        }
        dd($affichage);
    }

    private static function huntMode(array $bateaux_tailles, array $missiles): string
    {
        $map = OffenseBattleship::initialiserMap();

        foreach ($bateaux_tailles as $taille) {
            foreach (array_keys($map) as $coord) {
                $posArr = explode('-', $coord);
                $col = ord($posArr[0]);
                $row = intval($posArr[1]);

                // Vérification des positions de bateaux **HORIZONTALEMENT**
                if (OffenseBattleship::verifierPositionBateauPossible($taille, $coord, $missiles, true)) {
                    for ($k = 0; $k < $taille; $k++) {
                        $map[chr($col) . '-' . ($row + $k)]++;
                    }
                }

                // Vérification des positions de bateaux **VERTICALEMENT**
                if (OffenseBattleship::verifierPositionBateauPossible($taille, $coord, $missiles, false)) {
                    for ($k = 0; $k < $taille; $k++) {
                        $map[chr($col + $k) . '-' . $row]++;
                    }
                }
            }
        }

        // OffenseBattleship::debugMap($map);

        $maxs = array_keys($map, max($map));
        return $maxs[0];
    }

    private static function initialiserMap() : array
    {
        $map = [];
        $tailleJeu = 10;

        for ($i = 0; $i < $tailleJeu; $i++) {
            for ($j = 0; $j < $tailleJeu; $j++) {
                $map[chr(65 + $i).'-'.($j + 1)] = 0;
            }
        }

        return $map;
    }

    private static function verifierPositionBateauPossible(int $taille, string $position, array $missiles, bool $estHorizontal) : bool
    {
        if (array_key_exists($position, $missiles)) {
            return false;
        }

        $posArr = explode('-', $position);
        $col = ord($posArr[0]);
        $row = intval($posArr[1]);

        if (($estHorizontal ? $row : ($col - 64)) + $taille - 1 > 10) {
            // dd('no');
            return false;
        }

        for ($i = 1; $i < $taille; $i++) {
            if (array_key_exists(chr(($estHorizontal ? 0 : $i) + $col) . '-' . ($row + ($estHorizontal ? $i : 0)), $missiles))
            {
                // dd('no');
                return false;
            }
        }

        return true;
    }
}
