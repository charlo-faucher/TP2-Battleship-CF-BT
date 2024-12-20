<?php

namespace App\Algorithmes;

use App\Models\CoordonneeBateauAdversaire;
use App\Models\Partie;
use App\Models\TypeBateau;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Classe pour l'offense contre les bateaux adversaires.
 *
 * @author Charles-Olivier Faucher et Benjamin Thériault
 */
class OffenseBattleship
{
    /**
     * Calcule la meilleur coordonnée à attaquer selon les informations actuelles.
     *
     * @param int $partieId Id de la partie.
     * @param int|null $sourceId Source de la coordonnée calculée.
     * @return string Coordonnée à attaquer.
     */
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

        // Get les coordonnees où l'ordinateur a déjà envoyé des missiles
        $queryMissiles = CoordonneeBateauAdversaire::query()->where('partie_id', $partieId);
        $missiles = (clone $queryMissiles)->get();

        // Get les missiles qui ont touchés ou coulés un bateau + le nombre
        $missilesTouches = (clone $queryMissiles)->where('resultat', '!=', 0)->get();
        $nbMissiles = count($missilesTouches);

        // Get les types des bateaux déjà coulés
        $bateauxTypesCoules = (clone $queryTypes)->where('est_coule', true)->get();

        if (array_sum($bateauxTypesCoules->pluck('taille')->toArray()) < $nbMissiles) {
            return OffenseBattleship::targetMode($missilesTouches, $missiles, $queryTypes, $sourceId);
        } else {
            // Logique du Hunt mode
            $missilesDict = $missiles->pluck('resultat', 'coordonnee')->toArray();
            $bateauxTailles = $bateauxTypesPasCoules->pluck('taille')->toArray();
            return OffenseBattleship::huntMode($bateauxTailles, $missilesDict);
        }
    }

    /**
     * Mode de ciblage pour détruire le(s) bateau(x) qu'il reste à couler.
     *
     * @param Builder|Collection $missilesTouches Missile qui ont touché un bateau.
     * @param Builder|Collection $missiles Missiles précédents.
     * @param Builder $queryTypes Types de bateaux.
     * @return string Coordonnée à attaquer.
     */
    public static function targetMode(
        Builder|Collection $missilesTouches,
        Builder|Collection $missiles,
        Builder            $queryTypes,
        ?int               &$sourceId
    ): string {
        $dernierMissileTouche = new CoordonneeBateauAdversaire($missilesTouches->last()->toArray());
        $missilesCoord = $missiles->pluck('coordonnee')->toArray();

        $missileDeRepere = $dernierMissileTouche;

        if ($dernierMissileTouche->resultat > 1) {
            $bateauxTypesTaillesAvecID = (clone $queryTypes)->get()->pluck('taille', 'id')->toArray();
            $sources = $missilesTouches->toQuery()->has('casesEnfants')->where('resultat', '!=', '0')->get();

            foreach ($sources as $source) {
                $sourceBateau = $source->casesEnfants->where('resultat', '>', 1)->first();

                if ($sourceBateau == null) {
                    $missileDeRepere = $source->casesEnfants->last()->source;
                    break;
                }

                if ($source->casesEnfants->toQuery()->where('resultat', '!=', '0')->count()
                    >= $bateauxTypesTaillesAvecID[$sourceBateau->resultat - 1]) {
                    $premierMissile = $source->casesEnfants->where('resultat', '!=', '0')->firstOrFail();
                    $dernierMissile = $source->casesEnfants->where('resultat', '!=', '0')->last();

                    $sourceMissileCoord = explode('-', $source);
                    $premierMissileCoord = explode('-', $premierMissile);
                    $dernierMissileCoord = explode('-', $dernierMissile);
                    [$sourceMissileCol, $sourceMissileRow] = [
                        ord($sourceMissileCoord[0]),
                        intval($sourceMissileCoord[1])
                    ];
                    [$premierMissileCol, $premierMissileRow] = [
                        ord($premierMissileCoord[0]),
                        intval($premierMissileCoord[1])
                    ];
                    [$dernierMissileCol, $dernierMissileRow] = [
                        ord($dernierMissileCoord[0]),
                        intval($dernierMissileCoord[1])
                    ];

                    $directionPremier = [
                        $premierMissileCol <=> $sourceMissileCol,
                        $premierMissileRow <=> $sourceMissileRow
                    ];
                    $directionDernier = [
                        $dernierMissileCol <=> $sourceMissileCol,
                        $dernierMissileRow <=> $sourceMissileRow
                    ];

                    $missileDeRepere = $directionPremier === $directionDernier ? $source : $premierMissile;

                    $missileDeRepere->update(['source_id' => null]);
                    break;
                }
            }
        }

        $dernierMissileCoord = explode('-', $missileDeRepere->coordonnee);
        $dernierMissileCoordCol = ord($dernierMissileCoord[0]);
        $dernierMissileCoordRow = intval($dernierMissileCoord[1]);

        if ($missilesTouches->last()->source_id != null && $missilesTouches->last()->resultat <= 1) {
            $missileSource = $missileDeRepere->source;
            $missileDeRepere = $missileSource;

            $missileSourceCoord = explode('-', $missileSource->coordonnee);
            [$missileSourceCoordCol, $missileSourceCoordRow] = [
                ord($missileSourceCoord[0]),
                intval($missileSourceCoord[1])
            ];

            $vertical = $dernierMissileCoordCol <=> $missileSourceCoordCol;
            $horizontal = $dernierMissileCoordRow <=> $missileSourceCoordRow;

            $nouveauVertical = $dernierMissileCoordCol + $vertical;
            $nouveauHorizontal = $dernierMissileCoordRow + $horizontal;
            $nouvelleCoord = chr($nouveauVertical) . '-' . ($nouveauHorizontal);

            if (!in_array($nouvelleCoord, $missilesCoord)
                && (65 <= $nouveauVertical && $nouveauVertical < 65 + 10)
                && (1 <= $nouveauHorizontal && $nouveauHorizontal <= 10)) {
                $sourceId = $missileDeRepere->source_id
                    ?? $missiles->where('coordonnee', $missileDeRepere->coordonnee)->last()->id;

                return $nouvelleCoord;
            }
        }

        $missile = $missileDeRepere;
        $missileCoord = explode('-', $missile->coordonnee);
        $missileCoordCol = ord($missileCoord[0]);
        $missileCoordRow = intval($missileCoord[1]);

        $ordre = [[-1, 0], [1, 0], [0, -1], [0, 1]];
        foreach ($ordre as $sens) {
            $nouveauVertical = $missileCoordCol + $sens[0];
            $nouveauHorizontal = $missileCoordRow + $sens[1];
            $nouvelleCoord = chr($nouveauVertical) . '-' . ($nouveauHorizontal);

            // TODO si possible : Regarder meilleur sens possible selon bateaux restants
            if (!in_array($nouvelleCoord, $missilesCoord)
                && (65 <= $nouveauVertical && $nouveauVertical < 65 + 10)
                && (1 <= $nouveauHorizontal && $nouveauHorizontal <= 10)) {
                $sourceId = $missiles->where('coordonnee', $missile->coordonnee)->last()->id;

                return $nouvelleCoord;
            }
        }

        // Ne devrait jamais arriver ici, mais retarget une cible déjà touchée pour ne pas crash
        return $missilesTouches->first()->coordonnee;
    }

    /**
     * Méthode de debug qui imprime la grille de jeu avec les chances de chaque case.
     *
     * @param array $map
     * @return void
     */
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

    /**
     * Mode de chasse pour trouver une coordonnée à cibler.
     *
     * @param array $bateauxTailles Tailles des bateaux restants.
     * @param array $missiles Missiles précédents.
     * @return string Coordonnée à attaquer.
     */
    private static function huntMode(array $bateauxTailles, array $missiles): string
    {
        $map = OffenseBattleship::initialiserMap();

        foreach ($bateauxTailles as $taille) {
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

        // Décommenter pour afficher la map des probabilités.
        // OffenseBattleship::debugMap($map);

        $maxs = array_keys($map, max($map));
        return $maxs[array_rand($maxs)];
    }

    /**
     * Crée un dictionnaire avec la position des cases de Battleship ainsi que leur probabilité (zéro par défaut).
     *
     * @return array Map des coordonnées.
     */
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

    /**
     * Vérifie si un bateau peut être placé sur une position donnée.
     *
     * @param int $taille Taille du bateau.
     * @param string $position Coordonnée à analyser.
     * @param array $missiles Missiles précédents.
     * @param bool $estHorizontal Si la direction est horizontale.
     * @return bool Si le bateau peut être placé.
     */
    private static function verifierPositionBateauPossible(
        int $taille,
        string $position,
        array $missiles,
        bool $estHorizontal
    ) : bool {
        if (array_key_exists($position, $missiles)) {
            return false;
        }

        $posArr = explode('-', $position);
        [$col, $row] = [ord($posArr[0]), intval($posArr[1])];

        if (($estHorizontal ? $row : ($col - 64)) + $taille - 1 > 10) {
            return false;
        }

        for ($i = 1; $i < $taille; $i++) {
            if (array_key_exists(
                chr(
                    ($estHorizontal ? 0 : $i) + $col
                ) . '-' . ($row + ($estHorizontal ? $i : 0)),
                $missiles
            )
            ) {
                return false;
            }
        }

        return true;
    }
}
