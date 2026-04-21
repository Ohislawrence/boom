<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bookmaker;
use App\Models\Fixture;
use App\Models\League;
use Carbon\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // ── Static pages ──────────────────────────────────────────────
        $statics = [
            ['url' => route('home'),                      'priority' => 1.0,  'freq' => 'daily'],
            ['url' => route('fixture.betting-tips.index'),'priority' => 0.9,  'freq' => 'daily'],
            ['url' => route('accumulator.index'),         'priority' => 0.8,  'freq' => 'daily'],
            ['url' => route('tips.index'),                'priority' => 0.8,  'freq' => 'daily'],
            ['url' => route('bookmakers.index'),          'priority' => 0.7,  'freq' => 'weekly'],
            ['url' => route('page.about'),                'priority' => 0.5,  'freq' => 'monthly'],
            ['url' => route('page.how-it-works'),         'priority' => 0.5,  'freq' => 'monthly'],
            ['url' => route('page.editorial-policy'),     'priority' => 0.3,  'freq' => 'monthly'],
            ['url' => route('page.privacy-notice'),       'priority' => 0.3,  'freq' => 'monthly'],
            ['url' => route('page.responsible-gambling'), 'priority' => 0.4,  'freq' => 'monthly'],
        ];

        foreach ($statics as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['freq'])
                    ->setLastModificationDate(Carbon::now())
            );
        }

        // ── Fixtures (upcoming + recent with a slug) ───────────────────
        Fixture::query()
            ->whereNotNull('slug')
            ->where('match_date', '>=', Carbon::now()->subDays(3))
            ->where('match_date', '<=', Carbon::now()->addDays(14))
            ->orderBy('match_date')
            ->select(['slug', 'updated_at', 'match_date'])
            ->chunk(200, function ($fixtures) use ($sitemap) {
                foreach ($fixtures as $fixture) {
                    $sitemap->add(
                        Url::create(route('fixture.betting-tips', $fixture->slug))
                            ->setPriority(0.8)
                            ->setChangeFrequency('daily')
                            ->setLastModificationDate($fixture->updated_at ?? $fixture->match_date)
                    );
                }
            });

        // ── Leagues ────────────────────────────────────────────────────
        League::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('name')
            ->select(['slug', 'updated_at'])
            ->chunk(100, function ($leagues) use ($sitemap) {
                foreach ($leagues as $league) {
                    $sitemap->add(
                        Url::create(route('league.show', $league->slug))
                            ->setPriority(0.7)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($league->updated_at)
                    );
                }
            });

        // ── Bookmakers ─────────────────────────────────────────────────
        Bookmaker::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->orderBy('sort_order')
            ->select(['slug', 'updated_at'])
            ->chunk(50, function ($bookmakers) use ($sitemap) {
                foreach ($bookmakers as $bookmaker) {
                    $sitemap->add(
                        Url::create(route('bookmakers.show', $bookmaker->slug))
                            ->setPriority(0.6)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($bookmaker->updated_at)
                    );
                }
            });

        return $sitemap->toResponse(request());
    }
}
