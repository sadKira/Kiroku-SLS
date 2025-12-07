<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
});

Route::post('logout', Logout::class)->name('logout');