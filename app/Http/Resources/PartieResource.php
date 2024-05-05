<?php

namespace App\Http\Resources;

use App\Models\BateauOrdinateur;
use App\Models\TypeBateau;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource d'une partie.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class PartieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bateauxPositions = [];

        foreach ($this->bateauxOrdinateur as $bateau) {
            $bateauxPositions[$bateau->type->nom] = $bateau->coordonnees->pluck('coordonnee')->toArray();
        }

        if ($this->est_finie == 1)
        {
            $this->delete();
        }

        return [
            'id' => $this->id,
            'adversaire' => $this->adversaire,
            'bateaux' => $bateauxPositions,
            'created_at' => $this->created_at
        ];
    }
}
