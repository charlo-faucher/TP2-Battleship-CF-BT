<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('parties')
    ->controller(PartieController::class)
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/', 'store');
        Route::post('/{idPartie}/missiles', 'fire');
        Route::put('/{idPartie}/missiles/{missile}', 'resultat')->where('missile', '^[A-J]-([1-9]|10)$');;
        Route::delete('/{idPartie}', 'destroy');
    });
