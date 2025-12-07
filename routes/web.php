<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

use App\Livewire\Management\AdminDashboard;

use App\Livewire\Logger\LoggerDashboard;

Route::get('/', Login::class)->name('home');

Route::post('logout', Logout::class)->middleware('auth')->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin_dashboard');


    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

});

// Logger Routes
Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('/dashboard', LoggerDashboard::class)->name('logger_dashboard');
    

 

});
