<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmprestimoWebController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\Admin\EmpresaController as AdminEmpresaController;
use App\Http\Controllers\Admin\AtividadeController as AdminAtividadeController;

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
    Route::get('parcelas/hoje', [ParcelaController::class, 'hoje'])->name('parcelas.hoje');
    Route::post('parcelas/{parcela}/quitar', [EmprestimoWebController::class, 'quitarParcela'])->name('parcelas.quitar');
    Route::post('parcelas/{parcela}/desfazer', [EmprestimoWebController::class, 'desfazerParcela'])->name('parcelas.desfazer');

    // Notificações Push
    Route::post('push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
});

// Painel do Dono do Software (super-admin, fora do contexto de empresa/tenant)
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('empresas', AdminEmpresaController::class)->except(['show']);
    Route::post('empresas/{empresa}/desativar', [AdminEmpresaController::class, 'desativar'])->name('empresas.desativar');
    Route::post('empresas/{empresa}/ativar', [AdminEmpresaController::class, 'ativar'])->name('empresas.ativar');
    Route::get('logs', [AdminAtividadeController::class, 'index'])->name('logs.index');
});
