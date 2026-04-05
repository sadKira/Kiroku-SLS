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

        // Prevent lazy loading
        Model::preventLazyLoading();

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
                    'home' => 'Kiroku SLS',
                    'admin_dashboard' => 'Dashboard',
                    'college_list' => 'College List',
                    'shs_list' => 'SHS List',
                    'faculty_list' => 'Faculty List',
                    'user_logs' => 'User Logs',
                    'about_kiroku' => 'About Kiroku SLS',
                    'user_accounts' => 'Manage Accounts',
                    'user_management' => 'User Management',
                    'course_management' => 'Course Management',
                    'level_management' => 'Instructional Level Management',

                    // Logger
                    'logger_dashboard' => 'Dashboard',
                    'view_logs' => 'View Log',
                    
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
