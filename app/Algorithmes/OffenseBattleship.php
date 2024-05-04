<?php

namespace App\Algorithmes;

use App\Models\BateauAdversaire;
use App\Models\CoordonneeBateauAdversaire;
use App\Models\Partie;
use App\Models\TypeBateau;

class OffenseBattleship
{
    public static function calculerMeilleurCoup(int $partieId, ?int &$sourceId): string
    {
        // Get les tailles et les noms des bateaux qu'il reste à attaquer.
        $partie = Partie::findOrFail($partieId);
        $queryTypes = TypeBateau::query()
            ->select('nom', 'taille', 'types_bateaux.id')
            ->join('bateaux_adversaires', 'bateaux_adversaires.type_id', '=', 'types_bateaux.id')
            ->where('partie_id', $partie->id);

        // Get les tailles des bateaux qu'il reste à attaquer.
        $bateauxTypesPasCoules = (clone $queryTypes)->where('est_coule', false)->get();

        $bateauxTailles = $bateauxTypesPasCoules->pluck('taille')->toArray();

        // Get les coordonnees où l'ordinateur a déjà envoyé des missiles
        $queryMissiles = CoordonneeBateauAdversaire::query()->where('partie_id', $partieId);
        $missiles = (clone $queryMissiles)->get();

        // Get les missiles qui ont touchés ou coulés un bateau + le nombre
        $missilesTouches = (clone $queryMissiles)->where('resultat', '!=', 0)->get();
        $nbMissiles = count($missilesTouches);

        // Get les types des bateaux déjà coulés
        $bateauxTypesCoules = (clone $queryTypes)->where('est_coule',true)->get();

        if (array_sum($bateauxTypesCoules->pluck('taille')->toArray()) < $nbMissiles) {
            $dernierMissileTouche = new CoordonneeBateauAdversaire($missilesTouches->last()->toArray());
            $missilesCoord = $missiles->pluck('coordonnee')->toArray();

            $missileDeRepere = $dernierMissileTouche;

            if ($dernierMissileTouche->resultat > 1)
            {
                $bateauxTypesTaillesAvecID = (clone $queryTypes)->get()->pluck('taille', 'id')->toArray();
                $sources = $missilesTouches->toQuery()->has('casesEnfants')->get();

                foreach ($sources as $source) {
                    $sourceBateau = $source->casesEnfants->where('resultat', '>' , 1)->firstOrFail();

                    if ($source->casesEnfants->count() >= $bateauxTypesTaillesAvecID[$sourceBateau->resultat - 1]) {
                        $premierMissile = $source->casesEnfants->firstOrFail();
                        $dernierMissile = $source->casesEnfants->last();
                        $sourceMissileCoord = explode('-', $source);
                        $premierMissileCoord = explode('-', $premierMissile);
                        $dernierMissileCoord = explode('-', $dernierMissile);

                        [$sourceMissileCol, $sourceMissileRow] = [ord($sourceMissileCoord[0]), intval($sourceMissileCoord[1])];
                        [$premierMissileCol, $premierMissileRow] = [ord($premierMissileCoord[0]), intval($premierMissileCoord[1])];
                        [$dernierMissileCol, $dernierMissileRow] = [ord($dernierMissileCoord[0]), intval($dernierMissileCoord[1])];

                        $directionPremier = [$premierMissileCol <=> $sourceMissileCol, $premierMissileRow <=> $sourceMissileRow];
                        $directionDernier = [$dernierMissileCol <=> $sourceMissileCol, $dernierMissileRow <=> $sourceMissileRow];

                        if ($directionPremier === $directionDernier)
                        {
                            $missileDeRepere = $source;
                        }
                        else
                        {
                            $missileDeRepere = $source->casesEnfants->firstOrFail();
                        }

                        $missileDeRepere->update(['source_id' => null]);

                        break;
                    }
                }
            }

            $dernierMissileCoord = explode('-', $missileDeRepere->coordonnee);
            $dernierMissileCoordCol = ord($dernierMissileCoord[0]);
            $dernierMissileCoordRow = intval($dernierMissileCoord[1]);

            if ($missilesTouches->last()->source_id != null && $missilesTouches->last()->resultat <= 1)
            {
                $missileSource = $missileDeRepere->source;
                $missileDeRepere = $missileSource;
                $missileSourceCoord = explode('-',$missileSource->coordonnee);
                $missileSourceCoordCol = ord($missileSourceCoord[0]);
                $missileSourceCoordRow = intval($missileSourceCoord[1]);

                $vertical = $dernierMissileCoordCol <=> $missileSourceCoordCol;
                $horizontal = $dernierMissileCoordRow <=> $missileSourceCoordRow;

                $nouveauVertical = $dernierMissileCoordCol + $vertical;
                $nouveauHorizontal = $dernierMissileCoordRow + $horizontal;
                $nouvelleCoord =  chr($nouveauVertical).'-'.($nouveauHorizontal);


                if (!in_array($nouvelleCoord, $missilesCoord) && (65 <= $nouveauVertical && $nouveauVertical < 65 + 10) && (1 <= $nouveauHorizontal && $nouveauHorizontal <= 10))
                {
                    $sourceId = $missileDeRepere->source_id ?? $missiles->where('coordonnee', $missileDeRepere->coordonnee)->last()->id;

                    return $nouvelleCoord;
                }
            }

            //$missile = empty($missileDeRepere->casesEnfants->toArray()) ? $missileDeRepere->source : $missileDeRepere;

            $missile = $missileDeRepere;
            $missileCoord = explode('-', $missile->coordonnee);
            $missileCoordCol = ord($missileCoord[0]);
            $missileCoordRow = intval($missileCoord[1]);

            $ordre = [[-1, 0], [1, 0], [0, -1], [0, 1]];
            foreach ($ordre as $sens)
            {
                $nouveauVertical = $missileCoordCol + $sens[0];
                $nouveauHorizontal = $missileCoordRow + $sens[1];
                $nouvelleCoord = chr($nouveauVertical).'-'.($nouveauHorizontal);

                // TODO : Regarder meilleur sens possible selon bateaux restants
                if (!in_array($nouvelleCoord, $missilesCoord) && (65 <= $nouveauVertical && $nouveauVertical < 65 + 10) && (1 <= $nouveauHorizontal && $nouveauHorizontal <= 10)) {
                    $sourceId = $missiles->where('coordonnee', $missile->coordonnee)->last()->id;

                    return $nouvelleCoord;
                }
            }

            // Ne devrait jamais arriver ici, mais retarget une cible déjà touchée pour ne pas crash

            //dd("fail");
            return $missilesTouches->first();
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

        //OffenseBattleship::debugMap($map);

        $maxs = array_keys($map, max($map));
        return $maxs[array_rand($maxs)];
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
            return false;
        }

        for ($i = 1; $i < $taille; $i++) {
            if (array_key_exists(chr(($estHorizontal ? 0 : $i) + $col) . '-' . ($row + ($estHorizontal ? $i : 0)), $missiles))
            {
                return false;
            }
        }

        return true;
    }
}
