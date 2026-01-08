<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

use App\Livewire\Management\AdminDashboard;
use App\Livewire\Management\StudentList;
use App\Livewire\Management\AboutKiroku;
use App\Livewire\Management\StudentLogs;

use App\Livewire\Logger\LoggerDashboard;

use App\Http\Controllers\management\ExportBarcode;

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin_dashboard');
    Route::get('/student-list', StudentList::class)->name('student_list');
    Route::get('/student-logs', StudentLogs::class)->name('student_logs');
    Route::get('/about-kiroku', AboutKiroku::class)->name('about_kiroku');

    // Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('/export-barcode', [ExportBarcode::class, 'generatePdf'])->name('export_barcode');
});

// Logger Routes
Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('/dashboard', LoggerDashboard::class)->name('logger_dashboard');
    

});
