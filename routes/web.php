<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

use App\Livewire\Management\AdminDashboard;
use App\Livewire\Management\StudentList;
use App\Livewire\Management\DailyRecord;
use App\Livewire\Management\HourlyRecord;
use App\Livewire\Management\MonthlyRecord;
use App\Livewire\Management\SemestralRecord;
use App\Livewire\Management\AboutKiroku;

use App\Livewire\Logger\LoggerDashboard;

Route::get('/', Login::class)->name('home');

Route::post('logout', Logout::class)->middleware('auth')->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin_dashboard');
    Route::get('/student-list', StudentList::class)->name('student_list');
    Route::get('/hourly-record', HourlyRecord::class)->name('hourly_record');
    Route::get('/daily-record', DailyRecord::class)->name('daily_record');
    Route::get('/monthly-record', MonthlyRecord::class)->name('monthly_record');
    Route::get('/semestral-record', SemestralRecord::class)->name('semestral_record');
    Route::get('/about-kiroku', AboutKiroku::class)->name('about_kiroku');

    // Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

});

// Logger Routes
Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('/dashboard', LoggerDashboard::class)->name('logger_dashboard');
    

});
