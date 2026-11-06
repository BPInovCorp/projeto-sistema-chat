<?php

use App\Http\Controllers\AuthController;
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

    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});
