<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');
// Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/spin-wheel', function () {
    return view('game');
})->middleware('auth')->name('spin-wheel');

require __DIR__.'/auth.php';
