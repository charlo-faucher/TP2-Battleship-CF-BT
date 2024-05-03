<?php

namespace App\Http\Controllers;

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
        //$partie = Partie::create($attributes);

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

        return new PartieResource($partie);
    }


    public function fire($idPartie) : MissileResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('update', $partie);

        $coordonnee = OffenseBattleship::calculerMeilleurCoup($idPartie);

        $tir = CoordonneeBateauAdversaire::create([
            'coordonnee' => $coordonnee,
            'partie_id' => $idPartie,
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
            switch ($resultat) {
                case 2:
                    $nomBateau = "porte-avions";
                    break;
                case 3:
                    $nomBateau = "cuirasse";
                    break;
                case 4:
                    $nomBateau = "destroyer";
                    break;
                case 5:
                    $nomBateau = "sous-marin";
                    break;
                case 6:
                    $nomBateau = "patrouilleur";
                    break;
            }

            $bateau = BateauAdversaire::query()->where('partie_id',  $idPartie)->join('types_bateaux', 'type_id', '=', 'types_bateaux.id')->where('types_bateaux.nom', $nomBateau);
            //dd($bateau->get()->first());
            $bateau->update(['est_coule' => true]);
        }

        return new MissileResource($coordonnee);
    }

    // TODO : Faire bien le delete
    public function destroy($idPartie) : PartieResource
    {
        $partie = Partie::findOrFail($idPartie);

        Gate::authorize('update', $partie);

        $partie = Partie::findOrFail($idPartie);
        $partie->update(['est_finie' => true]);
        return new PartieResource($partie);
    }
}
