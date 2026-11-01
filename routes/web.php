<?php

use App\Http\Controllers\Admin\UsuarioController as AdminUsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DisenoController;
use App\Http\Controllers\FlujoDisenoController;
use Illuminate\Support\Facades\Route;

// --- Acceso ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- Aplicación (requiere sesión) ---
Route::middleware('auth')->group(function () {

    // Ver: Fábrica y EU
    Route::get('/', [DisenoController::class, 'index'])->name('disenos.index');

    // Crear / editar / eliminar datos del diseño: solo Fábrica
    Route::middleware('can:fabrica')->group(function () {
        Route::post('/disenos', [DisenoController::class, 'store'])->name('disenos.store');
        Route::put('/disenos/{diseno}', [DisenoController::class, 'update'])->name('disenos.update');
        Route::delete('/disenos/{diseno}', [DisenoController::class, 'destroy'])->name('disenos.destroy');
        Route::post('/disenos/{diseno}/codigos-fabrica', [FlujoDisenoController::class, 'storeFactoryBarcode'])->name('disenos.codigos-fabrica.store');
        Route::delete('/disenos/{diseno}/codigos-fabrica', [FlujoDisenoController::class, 'destroyFactoryBarcode'])->name('disenos.codigos-fabrica.destroy');
    });

    // Observaciones: Fábrica (INT) y EU
    Route::post('/disenos/{diseno}/observaciones', [FlujoDisenoController::class, 'storeObservation'])
        ->name('disenos.observaciones.store');

    // Vista previa de cualquier código (la usan los paneles de EU y Fábrica)
    Route::get('/disenos/{diseno}/codigo-preview', [FlujoDisenoController::class, 'previewBarcode'])
        ->name('disenos.codigos.preview');

    // Decisión, revisiones y códigos de barras: solo EU
    Route::middleware('can:eu')->group(function () {
        Route::patch('/disenos/{diseno}/aprobacion', [FlujoDisenoController::class, 'updateApproval'])->name('disenos.aprobacion.update');
        Route::post('/disenos/{diseno}/revisiones', [FlujoDisenoController::class, 'storeReview'])->name('disenos.revisiones.store');
        Route::post('/disenos/{diseno}/codigos', [FlujoDisenoController::class, 'storeBarcode'])->name('disenos.codigos.store');
        Route::delete('/disenos/{diseno}/codigos', [FlujoDisenoController::class, 'destroyBarcode'])->name('disenos.codigos.destroy');
    });

    // Administración de usuarios: solo Admin
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/usuarios', [AdminUsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [AdminUsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/editar', [AdminUsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [AdminUsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [AdminUsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});
