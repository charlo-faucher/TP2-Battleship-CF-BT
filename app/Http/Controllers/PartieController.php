<?php

namespace App\Http\Controllers;

use App\Algorithmes\DefensiveBattleship;
use App\Algorithmes\OffenseBattleship;
use App\Http\Requests\PartieRequest;
use App\Http\Requests\ResultatRequest;
use App\Http\Resources\MissileResource;
use App\Http\Resources\PartieResource;
use App\Models\BateauAdversaire;
use App\Models\CoordonneeBateauAdversaire;
use App\Models\Partie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controlleur d'une partie.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class PartieController extends Controller
{
    /**
     * Crée une nouvelle partie de Battleship.
     *
     * @param PartieRequest $request Requête avec le nom de l'adversaire.
     * @return PartieResource Ressource avec la position des bateaux de l'ordinateur.
     */
    public function store(PartieRequest $request) : PartieResource
    {
        $attributes = $request->validated();
        $partie = Auth::user()->parties()->create($attributes);

        DefensiveBattleship::creerBateaux($partie);

        return new PartieResource($partie);
    }

    /**
     * Calcule le meilleur tir possible selon les tirs précédents.
     *
     * @param int $idPartie Id de la partie.
     * @return MissileResource Ressource avec la coordonnée à attaquer.
     */
    public function fire(int $idPartie) : MissileResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('verifierConnexion', $partie);

        $idSource = null;
        $coordonnee = OffenseBattleship::calculerMeilleurCoup($idPartie, $idSource);

        $tir = CoordonneeBateauAdversaire::create([
            'coordonnee' => $coordonnee,
            'partie_id' => $idPartie,
            'source_id' => $idSource
        ]);

        return new MissileResource($tir);
    }

    /**
     * Conserve le résultat du tir précédent pour une future utilisation.
     *
     * @param ResultatRequest $request Requête avec le résultat.
     * @param int $idPartie Id de la partie.
     * @param String $missile Coordonnée du missile.
     * @return MissileResource Ressource avec la coordonnée et le résultat du missile.
     */
    public function resultat(ResultatRequest $request, int $idPartie, String $missile) : MissileResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('verifierConnexion', $partie);

        $attributes = $request->validated();

        $query = CoordonneeBateauAdversaire::query()->where('partie_id',  $idPartie)->where('coordonnee', $missile);
        $coordonnee = $query->get()->first();

        if ($coordonnee == null)
        {
            abort(404);
        }

        $coordonnee->update($attributes);
        $resultat = $attributes['resultat'];

        if ($resultat > 1)
        {
            $bateau = BateauAdversaire::query()->where('partie_id',  $idPartie)->where('type_id', $resultat - 1)->firstOrFail();
            $bateau->update(['est_coule' => true]);
        }

        return new MissileResource($coordonnee);
    }

    /**
     * Détruit une partie.
     *
     * @param int $idPartie Id de la partie.
     * @return PartieResource Ressource qui renvoit les informations de la partie.
     */
    public function destroy(int $idPartie) : PartieResource
    {
        $partie = Partie::findOrFail($idPartie);
        Gate::authorize('verifierConnexion', $partie);

        $partie = Partie::findOrFail($idPartie);
        $partie->update(['est_finie' => true]);

        return new PartieResource($partie);
    }
}
