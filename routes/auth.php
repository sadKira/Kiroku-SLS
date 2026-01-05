<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('home');
});

Route::post('logout', Logout::class)->middleware('auth')->name('logout');