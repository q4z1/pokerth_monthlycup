<?php

namespace App\Console\Commands;

use App\Models\Upload;
use App\Services\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Verifies that every stored result carries the points its finishing position
 * is worth according to the season configuration, and can repair the ones
 * that do not.
 */
class FixUploadPoints extends Command
{
    protected $signature = 'mcup:fix-points
                            {--year=* : limit to these seasons}
                            {--apply : write the corrections (otherwise only report)}';

    protected $description = 'Check stored result points against the season ranking points';

    public function handle(): int
    {
        $years = $this->option('year') ?: Season::years();
        sort($years);

        $wrong = [];

        foreach ($years as $year) {
            $year = (int) $year;

            foreach (Upload::forYear($year)->orderBy('month')->orderBy('position')->get() as $upload) {
                $expected = Season::pointsFor($year, $upload->type, $upload->table_name, $upload->position);

                if ($upload->points !== $expected) {
                    $wrong[] = ['upload' => $upload, 'expected' => $expected];
                }
            }
        }

        if ($wrong === []) {
            $this->info('All stored points match the season configuration.');

            return self::SUCCESS;
        }

        $this->table(
            ['season', 'month', 'table', 'place', 'player', 'stored', 'expected'],
            array_map(fn ($w) => [
                $w['upload']->year,
                Season::monthName($w['upload']->month),
                $w['upload']->type.'/'.$w['upload']->table_name,
                $w['upload']->position,
                $w['upload']->playername,
                $w['upload']->points,
                $w['expected'],
            ], $wrong)
        );

        $this->warn(count($wrong).' result row(s) carry the wrong points.');

        if (! $this->option('apply')) {
            $this->line('Nothing was changed. Re-run with --apply to write the corrections.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($wrong) {
            foreach ($wrong as $w) {
                $w['upload']->update(['points' => $w['expected']]);
            }
        });

        $this->info('Corrected '.count($wrong).' result row(s).');

        return self::SUCCESS;
    }
}
