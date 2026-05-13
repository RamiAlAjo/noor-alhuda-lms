<?php

namespace App\Providers;

use App\Models\CourseOffering;
use App\Models\User;
use App\Policies\CourseOfferingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        CourseOffering::class => CourseOfferingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Additional authorization gates can be defined here
        Gate::define('admin-access', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('super-admin');
        });

        Gate::define('teacher-access', function (User $user) {
            return $user->hasRole('teacher') || $user->hasRole('admin') || $user->hasRole('super-admin');
        });

        Gate::define('student-access', function (User $user) {
            return $user->hasRole('student') || $user->hasRole('admin') || $user->hasRole('super-admin');
        });
    }
}
