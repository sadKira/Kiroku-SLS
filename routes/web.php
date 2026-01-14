<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Auth\Login;
use App\Livewire\Actions\Logout;

use App\Livewire\Management\AdminDashboard;
use App\Livewire\Management\StudentList;
use App\Livewire\Management\AboutKiroku;
use App\Livewire\Management\StudentLogs;

use App\Livewire\Logger\LoggerDashboard;
use App\Livewire\Logger\ViewLogs;

use App\Http\Controllers\management\ExportBarcode;
use App\Http\Controllers\management\ExportStudentLogs;
use App\Http\Controllers\management\ExportDashboardReport;

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin-dashboard', AdminDashboard::class)->name('admin_dashboard');
    Route::get('/student-list', StudentList::class)->name('student_list');
    Route::get('/student-logs', StudentLogs::class)->name('student_logs');
    Route::get('/about-kiroku', AboutKiroku::class)->name('about_kiroku');

    Route::get('/export-barcode', [ExportBarcode::class, 'generatePdf'])->name('export_barcode');
    Route::get('/export-student-logs', [ExportStudentLogs::class, 'generatePdf'])->name('export_student_logs');
    Route::get('/export-dashboard-report', [ExportDashboardReport::class, 'generatePdf'])->name('export_dashboard_report');
});

// Logger Routes
Route::middleware(['auth', 'logger'])->group(function () {

    Route::get('/dashboard', LoggerDashboard::class)->name('logger_dashboard');
    Route::get('/view-log/{logSession}', ViewLogs::class)->name('view_logs');
    

});
