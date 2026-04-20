<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\EmprestimoWebController;
use App\Http\Controllers\ClienteController;

Route::redirect('/', '/emprestimos');
Route::resource('emprestimos', EmprestimoWebController::class);
Route::resource('clientes', ClienteController::class);
Route::post('parcelas/{parcela}/quitar', [EmprestimoWebController::class, 'quitarParcela'])->name('parcelas.quitar');

