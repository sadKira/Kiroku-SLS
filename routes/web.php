<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

use App\Livewire\Management\AdminDashboard;
use App\Livewire\Management\CollegeList;
use App\Livewire\Management\ShsList;
use App\Livewire\Management\FacultyList;
use App\Livewire\Management\AboutKiroku;
use App\Livewire\Management\UserLogs;
use App\Livewire\Management\UserAccounts;
use App\Livewire\Management\CourseManagement;
use App\Livewire\Management\LevelManagement;

use App\Livewire\Management\UserManagement;

use App\Livewire\Logger\LoggerDashboard;
use App\Livewire\Logger\ViewLogs;

use App\Http\Controllers\management\ExportBarcode;
use App\Http\Controllers\management\ExportStudentLogs;
use App\Http\Controllers\management\ExportDashboardReport;

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin_dashboard');
    Route::get('/college-list', CollegeList::class)->name('college_list');
    Route::get('/shs-list', ShsList::class)->name('shs_list');
    Route::get('/faculty-list', FacultyList::class)->name('faculty_list');
    Route::get('/user-logs', UserLogs::class)->name('user_logs');
    Route::get('/about-kiroku', AboutKiroku::class)->name('about_kiroku');
    Route::get('/courses', CourseManagement::class)->name('course_management');
    Route::get('/instructional-levels', LevelManagement::class)->name('level_management');

    Route::get('/export-barcode', [ExportBarcode::class, 'generatePdf'])->name('export_barcode');
    Route::get('/export-student-logs', [ExportStudentLogs::class, 'generatePdf'])->name('export_student_logs');
    Route::get('/export-dashboard-report', [ExportDashboardReport::class, 'generatePdf'])->name('export_dashboard_report');
});

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->group(function () {

    Route::get('/user-management', UserManagement::class)->name('user_management');

});

// Logger Routes
Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('/dashboard', LoggerDashboard::class)->name('logger_dashboard');
    Route::get('/view-log/{logSession}', ViewLogs::class)->name('view_logs');
    

});
