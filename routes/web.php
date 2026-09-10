<?php

use App\Http\Controllers\TrajetController;

Route::post('/trajets', [TrajetController::class, 'store']);
Route::get('/trajets', [TrajetController::class, 'index']);
Route::get('/trajets/search', [TrajetController::class, 'search']);
Route::put('/trajets/{id}/localisation', [TrajetController::class, 'updateLocalisation']);
Route::inertia('/trajets-page', 'TrajetPage')->name('trajets.page');


use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
