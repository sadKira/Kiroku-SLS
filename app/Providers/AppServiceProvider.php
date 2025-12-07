<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

use App\Enums\UserRole;

use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Kiroku Gates
         */

        // Super Admin Capabilities
        Gate::define('SA', fn(User $user) => 

            $user->role == UserRole::SuperAdmin 
        
        );

        /**
         * Dynamic Titles
         */
        View::composer('*', function ($view) {
            $user = Auth::user();

            // Handle case when user is not authenticated
            if (!$user) {
                $view->with('title', config('app.name'));
                return;
            }

            $titles = match ($user->role) {
                UserRole::Admin, UserRole::SuperAdmin, UserRole::Logger  => [

                    // Management
                    'admin_dashboard' => 'Admin Dashboard',
                    'student_list' => 'Student List',
                    'hourly_record' => 'Hourly Record',
                    'daily_record' => 'Daily Record',
                    'monthly_record' => 'Monthly Record',
                    'semestral_record' => 'Semestral Record',
                    'about_kiroku' => 'About Kiroku ALS',
                    'profile.edit' => 'Profile',
                    'user-password.edit' => 'Password',
                    'appearance.edit' => 'Appearance',

                    // Logger
                    'logger_dashboard' => 'Dashboard'
                ],
                default => [
                    'error' => "error",
                ],
            };

            $title = collect($titles)
                ->first(fn($label, $route) => request()->routeIs($route)) ?? 'Set Title';

            $view->with('title', $title);
        });
    }
}
