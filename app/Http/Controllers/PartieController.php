<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartieController extends Controller
{
    public function store() : JsonResponse
    {
        return response()->json([]);
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
