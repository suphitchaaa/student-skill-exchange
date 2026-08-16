<?php

namespace App\Providers;

use App\Models\ExchangeRequest;
use App\Models\StudentProfile;
use App\Models\UserSkill;
use App\Observers\ExchangeRequestObserver;
use App\Policies\ExchangeRequestPolicy;
use App\Policies\StudentProfilePolicy;
use App\Policies\UserSkillPolicy;
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
        Gate::policy(StudentProfile::class, StudentProfilePolicy::class);
        Gate::policy(ExchangeRequest::class, ExchangeRequestPolicy::class);
        Gate::policy(UserSkill::class, UserSkillPolicy::class);
        ExchangeRequest::observe(ExchangeRequestObserver::class);
    }
}
