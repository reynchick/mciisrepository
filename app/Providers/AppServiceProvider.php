<?php

namespace App\Providers;

use App\Models\{
    Agenda,
    CompiledReport,
    Faculty, 
    Keyword,
    Program,
    Research,
    Researcher,
    Role,
    SDG,
    SRIG,
    User
};
use App\Policies\{
    AgendaPolicy,
    CompiledReportPolicy,
    FacultyPolicy,
    KeywordPolicy,
    ProgramPolicy,
    ResearchPolicy,
    ResearcherPolicy,
    RolePolicy,
    SDGPolicy,
    SRIGPolicy,
    UserPolicy
};
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Observers\FacultyObserver;

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
        Gate::policy(CompiledReport::class, CompiledReportPolicy::class);
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(Keyword::class, KeywordPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(Research::class, ResearchPolicy::class);
        Gate::policy(Researcher::class, ResearcherPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Faculty::observe(FacultyObserver::class);
    }
}
