<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Player;
use App\Models\Upload;
use App\Services\Season;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    /** Winners of the gold final table of every cup that has been played. */
    public function series(Request $request)
    {
        $year = Season::resolve($request->query('year'));

        $podium = Upload::forYear($year)
            ->where('type', 'final')
            ->where('table_name', 'gold')
            ->where('position', '<=', 3)
            ->orderBy('month')
            ->orderBy('position')
            ->get()
            ->groupBy('month');

        $awards = Award::forYear($year)
            ->whereIn('type', ['gold1st', 'gold2nd', 'gold3rd'])
            ->get()
            ->keyBy(fn ($a) => $a->month.'|'.$a->type);

        $dates = Season::dates($year);
        $links = Season::forumLinks($year);

        $cups = [];
        foreach ($podium as $month => $rows) {
            $month = (int) $month;
            $places = [];
            foreach ($rows as $i => $row) {
                $type = ['gold1st', 'gold2nd', 'gold3rd'][$i] ?? null;
                $award = $type ? ($awards[$month.'|'.$type] ?? null) : null;
                $places[] = [
                    'position' => $row->position,
                    'playername' => $row->playername,
                    'award_url' => $award?->url,
                    'award_label' => $award?->label,
                ];
            }

            $cups[] = [
                'month' => $month,
                'month_name' => Season::monthName($month),
                'date' => $dates[$month] ?? null,
                'forum_link' => $links[$month] ?? $links[(string) $month] ?? null,
                'places' => $places,
            ];
        }

        return view('results.series', [
            'year' => $year,
            'cups' => $cups,
        ]);
    }

    /** Full table results of a single cup. */
    public function cup(Request $request, ?int $month = null)
    {
        $year = Season::resolve($request->query('year'));
        $month = $this->resolveMonth($year, $month);

        $rows = Upload::forYear($year)
            ->where('month', $month)
            ->orderBy('type')
            ->orderBy('table_name')
            ->orderBy('position')
            ->get();

        $tables = [];

        foreach ($rows->where('type', 'firstround')->groupBy('table_name') as $name => $entries) {
            $tables[] = [
                'key' => 'first-'.$name,
                'title' => Season::monthName($month).' Cup T'.$name,
                'variant' => 'first',
                'rows' => $this->rowsFor($entries),
            ];
        }
        usort($tables, fn ($a, $b) => strnatcmp($a['title'], $b['title']));

        foreach (Upload::FINAL_TABLES as $name => $label) {
            $entries = $rows->where('type', 'final')->where('table_name', $name)->sortBy('position');
            if ($entries->isEmpty()) {
                continue;
            }
            $tables[] = [
                'key' => 'final-'.$name,
                'title' => Season::monthName($month).' '.$label,
                'variant' => $name,
                'rows' => $this->rowsFor($entries),
            ];
        }

        return view('results.cup', [
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'tables' => $tables,
        ]);
    }

    /** Overall and per-cup ranking of a season. */
    public function rankings(Request $request)
    {
        $year = Season::resolve($request->query('year'));
        $lastMonth = Season::lastPlayableMonth($year);

        $perMonth = Upload::forYear($year)
            ->selectRaw('playername, month, SUM(points) as points')
            ->groupBy('playername', 'month')
            ->get();

        $months = [];
        for ($m = 1; $m <= $lastMonth; $m++) {
            $rows = $perMonth->where('month', $m)->sortByDesc('points')->values();
            if ($rows->isEmpty()) {
                continue;
            }
            $months[] = [
                'month' => $m,
                'month_name' => Season::monthName($m),
                'rows' => $rows->map(fn ($r, $i) => [
                    'rank' => $i + 1,
                    'playername' => $r->playername,
                    'points' => (int) $r->points,
                ])->all(),
            ];
        }

        $byPlayer = $perMonth->groupBy('playername');
        $general = $byPlayer->map(function ($rows, $playername) {
            $monthly = [];
            foreach ($rows as $row) {
                $monthly[(int) $row->month] = (int) $row->points;
            }

            return [
                'playername' => $playername,
                'points' => (int) $rows->sum('points'),
                'months' => $monthly,
            ];
        })->sortByDesc('points')->values()
            ->map(function ($row, $i) {
                $row['rank'] = $i + 1;

                return $row;
            })->all();

        return view('results.rankings', [
            'year' => $year,
            'general' => $general,
            'months' => $months,
            'monthColumns' => collect(range(1, $lastMonth))
                ->map(fn ($m) => ['month' => $m, 'name' => Season::monthName($m)])->all(),
        ]);
    }

    /** Players holding awards, ordered by the points they collected. */
    public function hallOfFame(Request $request)
    {
        $year = Season::resolve($request->query('year'));

        $points = Upload::forYear($year)
            ->selectRaw('playername, SUM(points) as points')
            ->groupBy('playername')
            ->pluck('points', 'playername');

        $players = Player::forYear($year)
            ->has('awards')
            ->with('awards')
            ->get()
            ->map(fn (Player $player) => [
                'playername' => $player->playername,
                'points' => (int) ($points[$player->playername] ?? 0),
                'avatar_url' => $player->hasAvatar() ? route('player.avatar', $player) : null,
                'awards' => $player->awards
                    ->sortBy([['month', 'asc'], ['type', 'asc']])
                    ->map(fn (Award $a) => ['url' => $a->url, 'label' => $a->label])
                    ->values()->all(),
            ])
            ->sortByDesc('points')
            ->values()
            ->map(function ($row, $i) {
                $row['rank'] = $i + 1;

                return $row;
            })
            ->all();

        return view('results.halloffame', [
            'year' => $year,
            'players' => $players,
        ]);
    }

    /** How many ranking points each place is worth. */
    public function points(Request $request)
    {
        $year = Season::resolve($request->query('year'));
        $points = Season::points($year);

        $rows = [];
        foreach (array_keys($points['first'] ?? []) as $place) {
            $rows[] = [
                'place' => (int) $place,
                'first' => $points['first'][$place] ?? null,
                'bronze' => $points['final']['bronze'][$place] ?? null,
                'silver' => $points['final']['silver'][$place] ?? null,
                'gold' => $points['final']['gold'][$place] ?? null,
            ];
        }
        usort($rows, fn ($a, $b) => $a['place'] <=> $b['place']);

        return view('results.points', [
            'year' => $year,
            'rows' => $rows,
        ]);
    }

    private function rowsFor($entries): array
    {
        return collect($entries)->sortBy('position')->map(fn (Upload $u) => [
            'position' => $u->position,
            'playername' => $u->playername,
            'points' => $u->points,
        ])->values()->all();
    }

    private function resolveMonth(int $year, ?int $month): int
    {
        if ($month !== null && $month >= 1 && $month <= 12) {
            return $month;
        }

        // Default to the most recent cup that actually has results.
        $latest = Upload::forYear($year)->max('month');

        return (int) ($latest ?: 1);
    }
}
