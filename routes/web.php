<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmprestimoWebController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PushSubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas Públicas / Visitantes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rotas Protegidas (Autenticação + Contexto de Empresa)
use App\Http\Controllers\DashboardController;

// ...
Route::middleware(['auth', 'empresa'])->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Empréstimos
    Route::resource('emprestimos', EmprestimoWebController::class)->except(['destroy']);
    Route::delete('emprestimos/{emprestimo}', [EmprestimoWebController::class, 'destroy'])
        ->name('emprestimos.destroy')
        ->middleware('permission:emprestimos.excluir');
    
    // Clientes
    Route::resource('clientes', ClienteController::class);
    
    // Configurações e Usuários
    Route::get('empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('empresa', [EmpresaController::class, 'update'])->name('empresa.update');
    Route::resource('usuarios', UsuarioController::class);
    
    // Ações de Parcelas
    Route::post('parcelas/{parcela}/quitar', [EmprestimoWebController::class, 'quitarParcela'])->name('parcelas.quitar');

    // Notificações Push
    Route::post('push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
});
