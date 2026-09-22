<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentCarCheckController;
use App\Http\Middleware\EnsureProfile;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified', EnsureProfile::class])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::inertia('home', 'home')->name('acceuil');
});

Route::middleware('auth')->group(function () {
    // Affichage du formulaire
    Route::get('/upload-profile', function () {
        return Inertia::render('upload/uploadProfile');
    })->name('profile.upload.show');

    // Traitement de l'envoi des documents (photo + scan + numPiece)
    Route::post('/upload-profile', [UserController::class, 'update'])->name('profile.upload');

    // Alternative pour mise à jour par ID/slug si nécessaire
    Route::match(['put', 'post'], '/users/{user}', [UserController::class, 'update'])->name('users.update');

    // Route sécurisée pour les avatars / fichiers privés
    Route::get('/users/{user}/avatar', [UserController::class, 'streamAvatar'])->name('users.avatar');
    // Route pour streamer le scan de la pièce d'identité (décrypté)
    Route::get('/users/{user}/scan', [UserController::class, 'streamScan'])->middleware('auth')->name('users.scan');

    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

    // Cars Routes
    Route::prefix('cars')->name('cars.')->group(function () {
        Route::get('/create', [\App\Http\Controllers\CarController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CarController::class, 'store'])->name('store');
        Route::get('/{car}/carte-grise-scan', [\App\Http\Controllers\CarController::class, 'streamCarteGriseScan'])->name('carte-grise-scan');
        Route::put('/{car}', [\App\Http\Controllers\CarController::class, 'update'])->name('update');
        Route::delete('/{car}', [\App\Http\Controllers\CarController::class, 'destroy'])->name('destroy');
    });

    // Rides Routes
    Route::prefix('rides')->name('rides.')->group(function () {
        Route::get('/verify-before-publish', [DocumentCarCheckController::class, 'verifyBeforePublish'])->name('verify-before-publish');
        Route::get('/check', [DocumentCarCheckController::class, 'check'])->name('check');
        Route::inertia('/create', 'rides/create')->name('create');
    });
});

// temporaire (affiche phpinfo sans provoquer d'envoi direct d'entête)
Route::get('/__phpinfo', function () {
    ob_start();
    phpinfo();
    $info = ob_get_clean();
    return response($info);
});

require __DIR__.'/settings.php';
