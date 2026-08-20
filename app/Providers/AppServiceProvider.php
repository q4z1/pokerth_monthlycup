<?php

namespace App\Providers;

use App\Services\Season;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Data the layout needs on every page: season navigation and footer.
        View::composer('*', function ($view) {
            $selected = Season::resolve(request()->query('year'));

            $current = Season::current();

            // Every season and the cups it actually holds, so the navigation is
            // built from the data instead of a hard coded list of years.
            $seasons = [];
            foreach (Season::years() as $year) {
                $seasons[$year] = Season::monthsWithResults($year);
            }

            $view->with([
                'navSeasons' => array_keys($seasons),
                'navSeasonMonths' => $seasons,
                'navCurrentYear' => $current,
                'navSelectedYear' => $selected,
                'navMonths' => Season::MONTHS,
                'navLastMonth' => Season::lastPlayableMonth($selected),
                'navFooter' => Season::footer($selected) ?: Season::footer($current),
            ]);
        });
    }
}
