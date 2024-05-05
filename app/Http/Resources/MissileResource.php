<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Ressource d'un missile.
 *
 * @author Charles-Olivier Faucher et Benjamin Theriault
 */
class MissileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'coordonnee' => $this->coordonnee,
            'resultat' => $this->resultat,
            'created_at' => $this->created_at
        ];
    }
}
