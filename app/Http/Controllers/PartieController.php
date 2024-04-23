<?php

namespace App\Http\Controllers;

use App\Http\Resources\PartieResource;
use App\Models\Partie;
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


        return new PartieResource($partie);
    }


    public function fire(Request $request) : JsonResponse
    {
        return response()->json([]);
    }

    public function resultat(Request $request) : JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(Request $request) : JsonResponse
    {
        return response()->json([]);
    }
}
