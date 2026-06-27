<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminMovieController;
use Illuminate\Support\Facades\Route;

/* ----------------------------- Público ----------------------------- */

// Landing / Inicio
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Cartelera completa
Route::get('/cartelera', [MovieController::class, 'index'])->name('cartelera');

// Detalle de película
Route::get('/pelicula/{slug}', [MovieController::class, 'show'])->name('movies.show');

// Sedes
Route::get('/sedes', [SedeController::class, 'index'])->name('sedes');

// Promociones
Route::get('/promociones', [PromotionController::class, 'index'])->name('promociones');

// Boletín (suscripción)
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

/* ----------------------------- Compra de entradas ----------------------------- */
Route::middleware(['auth'])->group(function () {
    // Flujo de compra
    Route::get('/reserva/{showtime}', [BookingController::class, 'create'])->name('bookings.create');

    // Procesar la reserva (POST del stepper)
    Route::post('/reserva', [BookingController::class, 'store'])->name('bookings.store');

    // Mis reservas
    Route::get('/mis-reservas', [BookingController::class, 'index'])->name('bookings.index');
});

/* ----------------------------- Autenticación y Dashboard por defecto ----------------------------- */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

/* ----------------------------- Panel de administración ----------------------------- */
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Películas (CRUD)
    Route::get('/peliculas', [AdminMovieController::class, 'index'])->name('movies.index');
    Route::get('/peliculas/crear', [AdminMovieController::class, 'create'])->name('movies.create');
    Route::post('/peliculas', [AdminMovieController::class, 'store'])->name('movies.store');
    Route::get('/peliculas/{movie}/editar', [AdminMovieController::class, 'edit'])->name('movies.edit');
    Route::put('/peliculas/{movie}', [AdminMovieController::class, 'update'])->name('movies.update');
    Route::delete('/peliculas/{movie}', [AdminMovieController::class, 'destroy'])->name('movies.destroy');
});

require __DIR__.'/settings.php';

