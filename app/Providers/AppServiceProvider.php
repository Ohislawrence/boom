<?php

namespace App\Providers;

use App\Models\Bookmaker;
use App\Services\DeepSeekService;
use App\Services\FootballApiService;
use App\Services\MatchAnalysisService;
use App\Services\TipResultService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FootballApiService::class);
        $this->app->singleton(DeepSeekService::class);
        $this->app->singleton(MatchAnalysisService::class);
        $this->app->singleton(TipResultService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials.footer', function ($view) {
            $view->with('footerBookmakers', Bookmaker::active()->take(5)->get(['name', 'slug', 'logo_url', 'affiliate_url', 'rating']));
        });
    }
}
