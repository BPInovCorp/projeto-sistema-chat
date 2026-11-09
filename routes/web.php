<?php

use App\Http\Controllers\AdminUtilizadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversaDiretaController;
use App\Http\Controllers\MensagemController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\SalaMembroController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('salas.index');
    });

    Route::get('/salas', [SalaController::class, 'index'])->name('salas.index');
    Route::get('/salas/criar', [SalaController::class, 'create'])->name('salas.create');
    Route::post('/salas', [SalaController::class, 'store'])->name('salas.store');
    Route::get('/salas/{sala}', [SalaController::class, 'show'])->name('salas.show');

    Route::post('/salas/{sala}/membros', [SalaMembroController::class, 'store'])->name('salas.membros.store');
    Route::delete('/salas/{sala}/membros/{utilizador}', [SalaMembroController::class, 'destroy'])->name('salas.membros.destroy');
    Route::post('/salas/{sala}/mensagens', [MensagemController::class, 'storeSala'])->name('salas.mensagens.store');

    Route::get('/conversas-diretas', [ConversaDiretaController::class, 'index'])->name('conversas-diretas.index');
    Route::get('/conversas-diretas/{utilizador}', [ConversaDiretaController::class, 'show'])->name('conversas-diretas.show');
    Route::post('/conversas-diretas/{conversa}/mensagens', [MensagemController::class, 'storeConversaDireta'])->name('conversas-diretas.mensagens.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/utilizadores', [AdminUtilizadorController::class, 'index'])->name('utilizadores.index');
        Route::get('/utilizadores/criar', [AdminUtilizadorController::class, 'create'])->name('utilizadores.create');
        Route::post('/utilizadores', [AdminUtilizadorController::class, 'store'])->name('utilizadores.store');
        Route::get('/utilizadores/{utilizador}/editar', [AdminUtilizadorController::class, 'edit'])->name('utilizadores.edit');
        Route::put('/utilizadores/{utilizador}', [AdminUtilizadorController::class, 'update'])->name('utilizadores.update');
        Route::delete('/utilizadores/{utilizador}', [AdminUtilizadorController::class, 'destroy'])->name('utilizadores.destroy');
    });

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
