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
    User,
    UserAuditLog
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

use App\Observers\{
    FacultyObserver,
    UserObserver
};

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
        Gate::policy(CompiledReport::class, CompiledReportPolicy::class);
        Gate::policy(Faculty::class, FacultyPolicy::class);
        Gate::policy(Keyword::class, KeywordPolicy::class);
        Gate::policy(Program::class, ProgramPolicy::class);
        Gate::policy(Research::class, ResearchPolicy::class);
        Gate::policy(Researcher::class, ResearcherPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        User::observe(UserObserver::class);
        Faculty::observe(FacultyObserver::class);

        Gate::define('viewLogs', function (User $user) {
            return $user->isAdministrator();
        });
    }
}
