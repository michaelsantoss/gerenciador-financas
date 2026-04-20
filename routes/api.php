<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmprestimoController;
use App\Http\Controllers\ParcelaController;

Route::name('api.')->group(function () {
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('emprestimos', EmprestimoController::class)->only(['index', 'store', 'show']);
    Route::post('parcelas/{parcela}/quitar', [ParcelaController::class, 'quitar']);
});
