<?php

namespace App\Providers;

use App\Models\Faculty;
use App\Models\Research;
use App\Policies\FacultyPolicy;
use App\Policies\ResearchPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(Research::class, ResearchPolicy::class);
    }
}
