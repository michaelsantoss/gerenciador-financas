<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmprestimoWebController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteArquivoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\Admin\EmpresaController as AdminEmpresaController;
use App\Http\Controllers\Admin\AtividadeController as AdminAtividadeController;
use App\Http\Controllers\Admin\EmpresaUsuarioController as AdminEmpresaUsuarioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas Públicas / Visitantes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Rotas Protegidas (Autenticação + Contexto de Empresa)
use App\Http\Controllers\DashboardController;

// ...
Route::middleware(['auth', 'empresa'])->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Tutorial (guia de uso do sistema)
    Route::get('/tutorial', function () {
        return response()->file(resource_path('tutorial/guia-de-uso.html'), [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    })->name('tutorial');

    // Empréstimos
    Route::resource('emprestimos', EmprestimoWebController::class)->except(['destroy']);
    Route::delete('emprestimos/{emprestimo}', [EmprestimoWebController::class, 'destroy'])
        ->name('emprestimos.destroy')
        ->middleware('permission:emprestimos.excluir');
    Route::post('emprestimos/{emprestimo}/renovar', [EmprestimoWebController::class, 'renovar'])->name('emprestimos.renovar');
    
    // Clientes
    Route::resource('clientes', ClienteController::class);
    Route::post('clientes/{cliente}/arquivos', [ClienteArquivoController::class, 'store'])->name('clientes.arquivos.store');
    Route::get('clientes/{cliente}/arquivos/{arquivo}', [ClienteArquivoController::class, 'show'])->name('clientes.arquivos.show');
    Route::delete('clientes/{cliente}/arquivos/{arquivo}', [ClienteArquivoController::class, 'destroy'])->name('clientes.arquivos.destroy');
    
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
    Route::delete('push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
});

// Painel do Dono do Software (super-admin, fora do contexto de empresa/tenant)
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('empresas', AdminEmpresaController::class)->except(['show']);
    Route::post('empresas/{empresa}/ativar', [AdminEmpresaController::class, 'ativar'])->name('empresas.ativar');
    Route::resource('empresas.usuarios', AdminEmpresaUsuarioController::class)->except(['show']);
    Route::get('logs', [AdminAtividadeController::class, 'index'])->name('logs.index');
});
