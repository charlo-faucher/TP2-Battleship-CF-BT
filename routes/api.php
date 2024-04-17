<?php

use App\Http\Controllers\PartieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('partie')
    ->controller(PartieController::class)
    ->group(function () {
        Route::get('/{id}', 'spect');
        Route::post('/', 'index');
        Route::post('/{id}/missile', 'fire');
        Route::post('/{id}/missile/{coordonée}', 'resultat');
        Route::delete('/{id}', 'destroy');
    });
