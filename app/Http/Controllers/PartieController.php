<?php

namespace App\Http\Controllers;

use App\Algorithmes\OffenseBattleship;
use App\Http\Resources\MissileResource;
use App\Http\Resources\PartieResource;
use App\Models\BateauAdversaire;
use App\Models\BateauOrdinateur;
use App\Models\CoordonneeBateauAdversaire;
use App\Models\CoordonneeBateauOrdinateur;
use App\Models\Partie;
use App\Models\TypeBateau;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartieController extends Controller
{
    public function store(Request $request) : PartieResource
    {
        $partie = Partie::create([
                'adversaire' => $request->adversaire,
            ]
        );

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


    public function fire($id) : MissileResource
    {
        $targetMode = false;
        $coordonnee = OffenseBattleship::calculerMeilleurCoup($id);

        $tir = CoordonneeBateauAdversaire::create([
            'coordonnee' => $coordonnee,
            'partie_id' => $id,
        ]);

        return new MissileResource($tir);
    }

    public function resultat(Request $request, $id, $coordonnee) : MissileResource
    {
        $partie = Partie::findOrFail($id);
        $query = CoordonneeBateauAdversaire::query()->where('partie_id',  $partie->id)->where('coordonnee', $coordonnee);
        $coordonnee = $query->get()[0];
        $coordonnee->update(['resultat' => $request->input('resultat')]);

        return new MissileResource($coordonnee);
    }

    public function destroy($id) : PartieResource
    {
        $partie = Partie::findOrFail($id);
        $partie->update(['est_finie' => true]);
        return new PartieResource($partie);
    }
}
