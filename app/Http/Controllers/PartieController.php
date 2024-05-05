<?php

namespace App\Http\Controllers;

use App\Algorithmes\DefensiveBattleship;
use App\Algorithmes\OffenseBattleship;
use App\Http\Requests\PartieRequest;
use App\Http\Requests\ResultatRequest;
use App\Http\Resources\MissileResource;
use App\Http\Resources\PartieResource;
use App\Models\BateauAdversaire;
use App\Models\BateauOrdinateur;
use App\Models\CoordonneeBateauAdversaire;
use App\Models\CoordonneeBateauOrdinateur;
use App\Models\Partie;
use App\Models\TypeBateau;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PartieController extends Controller
{
    public function store(PartieRequest $request) : PartieResource
    {
        $attributes = $request->validated();
        $partie = Auth::user()->parties()->create($attributes);

        DefensiveBattleship::creerBateaux($partie);

        return new PartieResource($partie);
    }


    public function fire($idPartie) : MissileResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('update', $partie);

        $idSource = null;
        $coordonnee = OffenseBattleship::calculerMeilleurCoup($idPartie, $idSource);
        //dd($coordonnee);

        $tir = CoordonneeBateauAdversaire::create([
            'coordonnee' => $coordonnee,
            'partie_id' => $idPartie,
            'source_id' => $idSource
        ]);

        return new MissileResource($tir);
    }

    public function resultat(ResultatRequest $request, $idPartie, String $missile) : MissileResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('update', $partie);

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

    public function destroy($idPartie) : PartieResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('update', $partie);

        $partie = Partie::findOrFail($idPartie);
        $partie->update(['est_finie' => true]);

        return new PartieResource($partie);
    }
}
