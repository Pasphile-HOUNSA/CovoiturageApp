<?php

use Illuminate\Support\Facades\Route;
Route::inertia('/trajets-page', 'TrajetPage')->name('trajets.page');

Route::inertia('/', 'welcome')->name('home');

Route::inertia('/trajets-create', 'Trajet/Create')->name('trajets.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
