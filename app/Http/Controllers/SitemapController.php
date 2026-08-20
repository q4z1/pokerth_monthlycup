<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Services\Season;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $current = Season::current();
        $urls = [];

        $add = function (string $url, string $changefreq, string $priority) use (&$urls) {
            $urls[] = compact('url', 'changefreq', 'priority');
        };

        $add(route('home'), 'weekly', '1.0');
        $add(route('registration'), 'daily', '0.9');
        $add(route('signups'), 'daily', '0.8');
        $add(route('table-settings'), 'yearly', '0.5');

        foreach (Season::years() as $year) {
            $isCurrent = $year === $current;
            $freq = $isCurrent ? 'weekly' : 'yearly';
            $priority = $isCurrent ? '0.9' : '0.4';
            $params = $isCurrent ? [] : ['year' => $year];

            $add(route('results.series', $params), $freq, $priority);
            $add(route('results.rankings', $params), $freq, $priority);
            $add(route('results.halloffame', $params), $freq, $priority);
            $add(route('results.points', $params), 'yearly', '0.3');

            // one entry per cup that actually has results
            foreach (Upload::forYear($year)->distinct()->orderBy('month')->pluck('month') as $month) {
                $add(
                    route('results.cup', $params + ['month' => (int) $month]),
                    $freq,
                    $priority
                );
            }
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
