<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Season;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $year = Season::resolve($request->query('year'));

        return view('admin.settings', [
            'year' => $year,
            'years' => Season::years(),
            'settings' => $this->payload($year),
            'months' => collect(Season::MONTHS)->map(fn ($name, $m) => ['value' => $m, 'label' => $name])->values()->all(),
            'finalTables' => ['gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'dates' => ['present', 'array'],
            'dates.*' => ['nullable', 'string', 'max:32'],
            'forum_links' => ['present', 'array'],
            'forum_links.*' => ['nullable', 'string', 'max:255'],
            'footer' => ['nullable', 'string'],
            'points' => ['required', 'array'],
            'points.first' => ['required', 'array'],
            'points.first.*' => ['required', 'integer'],
            'points.final' => ['required', 'array'],
            'points.final.*' => ['required', 'array'],
            'points.final.*.*' => ['required', 'integer'],
        ]);

        $year = (int) $data['year'];

        $this->put($year, 'dates', json_encode($this->cleanMonthMap($data['dates'])));
        $this->put($year, 'forum_links', json_encode($this->cleanMonthMap($data['forum_links'])));
        $this->put($year, 'footer', $data['footer'] ?? '');
        $this->put($year, 'points', json_encode($data['points']));

        Season::forget($year);

        return response()->json([
            'success' => true,
            'message' => "Settings for $year saved.",
            'settings' => $this->payload($year),
        ]);
    }

    /**
     * Start a new season. Replaces the legacy cron/new_year.php – with the
     * normalised schema no tables have to be created any more, only the
     * settings of the new season.
     */
    public function storeSeason(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'copy_from' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $year = (int) $data['year'];

        if (Setting::forYear($year)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Season $year already exists.",
            ], 422);
        }

        $source = (int) ($data['copy_from'] ?? $year - 1);

        $this->put($year, 'points', Season::get($source, 'points') ?: json_encode(['first' => [], 'final' => []]));
        $this->put($year, 'dates', json_encode(array_fill_keys(range(1, 12), null)));
        $this->put($year, 'forum_links', json_encode(array_fill_keys(range(1, 12), null)));
        $this->put($year, 'footer', Season::get($source, 'footer') ?: '');

        Season::forget();

        return response()->json([
            'success' => true,
            'message' => "Season $year created (ranking points copied from $source).",
            'redirect' => route('admin.settings', ['year' => $year]),
        ]);
    }

    private function put(int $year, string $type, ?string $value): void
    {
        Setting::updateOrCreate(['year' => $year, 'type' => $type], ['value' => $value]);
    }

    /** Keep only months 1-12 and turn blank strings into null. */
    private function cleanMonthMap(array $input): array
    {
        $out = [];
        foreach (range(1, 12) as $month) {
            $value = $input[$month] ?? $input[(string) $month] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $out[$month] = ($value === '' || $value === 'n/a') ? null : $value;
        }

        return $out;
    }

    private function payload(int $year): array
    {
        $points = Season::points($year);

        // Normalise to plain 1..10 integer maps so the form is always complete.
        $first = [];
        $final = [];
        foreach (range(1, 10) as $place) {
            $first[$place] = (int) ($points['first'][$place] ?? $points['first'][(string) $place] ?? 0);
            foreach (['gold', 'silver', 'bronze'] as $table) {
                $final[$table][$place] = (int) ($points['final'][$table][$place] ?? $points['final'][$table][(string) $place] ?? 0);
            }
        }

        $links = Season::forumLinks($year);

        return [
            'year' => $year,
            'dates' => Season::dates($year),
            'forum_links' => collect(range(1, 12))
                ->mapWithKeys(fn ($m) => [$m => $links[$m] ?? $links[(string) $m] ?? null])->all(),
            'footer' => Season::footer($year),
            'points' => ['first' => $first, 'final' => $final],
        ];
    }
}
