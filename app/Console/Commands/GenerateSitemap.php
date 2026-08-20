<?php

namespace App\Console\Commands;

use App\Models\Upload;
use App\Services\Season;
use Illuminate\Console\Command;

/**
 * Writes public/sitemap.xml. Built as a command rather than a route so nginx
 * can serve a static file; the schedule lives in the pthranking application,
 * which is the one driven by cron.
 */
class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate
                            {--output= : target file, defaults to public/sitemap.xml}
                            {--dry-run : only count, write nothing}';

    protected $description = 'Generate sitemap.xml';

    public function handle(): int
    {
        $urls = $this->urls();
        $this->line(count($urls).' URLs');

        if ($this->option('dry-run')) {
            $this->line('dry run, nothing written');

            return self::SUCCESS;
        }

        $target = $this->option('output') ?: public_path('sitemap.xml');
        $xml = view('sitemap', ['urls' => $urls])->render();

        if (file_put_contents($target, $xml) === false) {
            $this->error("could not write $target");

            return self::FAILURE;
        }

        $this->info($target.' written ('.number_format(strlen($xml) / 1024, 1).' KB)');

        return self::SUCCESS;
    }

    private function urls(): array
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

            foreach (Upload::forYear($year)->distinct()->orderBy('month')->pluck('month') as $month) {
                $add(route('results.cup', $params + ['month' => (int) $month]), $freq, $priority);
            }
        }

        return $urls;
    }
}
