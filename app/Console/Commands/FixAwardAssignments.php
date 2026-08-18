<?php

namespace App\Console\Commands;

use App\Models\Award;
use App\Models\Player;
use App\Models\Upload;
use App\Services\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Recomputes which player holds which award from the uploaded cup results.
 *
 * The legacy assign dialog appended players to an award instead of replacing
 * them, so a single award could end up on several players while the award for
 * the next place stayed empty. Everything that can be derived from the results
 * is rebuilt here; awards that cannot (admin) are left untouched.
 */
class FixAwardAssignments extends Command
{
    protected $signature = 'mcup:fix-award-assignments
                            {--year=* : limit to these seasons}
                            {--apply : write the corrections (otherwise only report)}';

    protected $description = 'Rebuild award assignments from the cup results';

    /** award type => [final table, position] */
    private const PODIUM = [
        'gold1st' => ['gold', 1], 'gold2nd' => ['gold', 2], 'gold3rd' => ['gold', 3],
        'silver1st' => ['silver', 1], 'silver2nd' => ['silver', 2], 'silver3rd' => ['silver', 3],
        'bronze1st' => ['bronze', 1], 'bronze2nd' => ['bronze', 2], 'bronze3rd' => ['bronze', 3],
    ];

    /** award type => rank in the overall season ranking */
    private const SEASON_RANK = ['rank1st' => 1, 'rank2nd' => 2, 'rank3rd' => 3];

    public function handle(): int
    {
        $years = $this->option('year') ?: Season::years();
        sort($years);

        $apply = (bool) $this->option('apply');
        $changes = [];

        foreach ($years as $year) {
            $year = (int) $year;

            foreach (Award::forYear($year)->with('players')->orderBy('month')->orderBy('type')->get() as $award) {
                $expected = $this->expectedHolders($award);

                if ($expected === null) {
                    continue; // not derivable (admin award)
                }

                $current = $award->players->pluck('playername')->sort()->values()->all();
                $wanted = collect($expected)->sort()->values()->all();

                if ($current === $wanted) {
                    continue;
                }

                $changes[] = [
                    'award' => $award,
                    'year' => $year,
                    'month' => $award->month,
                    'type' => $award->type,
                    'from' => $current,
                    'to' => $wanted,
                ];
            }
        }

        if ($changes === []) {
            $this->info('All derivable award assignments already match the results.');

            return self::SUCCESS;
        }

        $this->table(
            ['season', 'month', 'award', 'currently', 'should be'],
            array_map(fn ($c) => [
                $c['year'],
                $c['month'] ? Season::monthName($c['month']) : '—',
                Award::TYPES[$c['type']] ?? $c['type'],
                $c['from'] ? implode(', ', $c['from']) : '—',
                $c['to'] ? implode(', ', $c['to']) : '—',
            ], $changes)
        );

        $this->newLine();
        $this->warn(count($changes).' award(s) differ from the results.');

        if (! $apply) {
            $this->line('Nothing was changed. Re-run with --apply to write the corrections.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($changes) {
            foreach ($changes as $change) {
                $ids = Player::forYear($change['year'])
                    ->whereIn('playername', $change['to'])
                    ->pluck('id')
                    ->all();

                $change['award']->players()->sync($ids);
            }
        });

        $this->info('Corrections applied to '.count($changes).' award(s).');

        return self::SUCCESS;
    }

    /**
     * @return array<int,string>|null the playernames that should hold the award,
     *                                or null when it cannot be derived
     */
    private function expectedHolders(Award $award): ?array
    {
        if (isset(self::PODIUM[$award->type])) {
            [$table, $position] = self::PODIUM[$award->type];

            $name = Upload::forYear($award->year)
                ->where('month', $award->month)
                ->where('type', 'final')
                ->where('table_name', $table)
                ->where('position', $position)
                ->value('playername');

            return $name ? [$name] : [];
        }

        if (isset(self::SEASON_RANK[$award->type])) {
            $ranking = $this->seasonRanking($award->year);
            $name = $ranking[self::SEASON_RANK[$award->type] - 1] ?? null;

            return $name ? [$name] : [];
        }

        // The admin award is the only one that may legitimately be held by
        // several players, and top20 has no rule that follows from the results.
        return null;
    }

    /** @return array<int,string> playernames ordered by total points, best first */
    private function seasonRanking(int $year): array
    {
        static $cache = [];

        return $cache[$year] ??= Upload::forYear($year)
            ->selectRaw('playername, SUM(points) as points')
            ->groupBy('playername')
            ->orderByDesc('points')
            ->pluck('playername')
            ->all();
    }
}
