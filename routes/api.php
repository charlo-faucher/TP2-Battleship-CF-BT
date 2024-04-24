<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('parties')
    ->controller(PartieController::class)
    ->group(function () {
        Route::post('/', 'store');
        Route::post('/{id}/missiles', 'fire');
        Route::post('/{id}/missiles/{coordonnee}', 'resultat');
        Route::delete('/{id}', 'destroy');
    });
