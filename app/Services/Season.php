<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Upload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Everything that is configured per season (year): cup dates, ranking points,
 * footer text and forum links. Replaces the legacy settings<year> tables.
 */
class Season
{
    public const MONTHS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    public static function current(): int
    {
        return (int) date('Y');
    }

    /** Resolve a requested year, falling back to the current season. */
    public static function resolve(mixed $year): int
    {
        $year = (int) $year;

        return in_array($year, self::years(), true) ? $year : self::current();
    }

    /** All seasons that hold data, newest first. */
    public static function years(): array
    {
        return Cache::remember('season.years', 3600, function () {
            $years = Setting::query()->distinct()->pluck('year')
                ->merge(Upload::query()->distinct()->pluck('year'))
                ->map(fn ($y) => (int) $y)
                ->push(self::current())
                ->unique()
                ->sortDesc()
                ->values()
                ->all();

            return $years;
        });
    }

    /** Raw setting value for a season. */
    public static function get(int $year, string $type, mixed $default = null): mixed
    {
        $settings = Cache::remember("season.settings.$year", 3600, function () use ($year) {
            return Setting::forYear($year)->pluck('value', 'type')->all();
        });

        return $settings[$type] ?? $default;
    }

    public static function forget(?int $year = null): void
    {
        Cache::forget('season.years');
        foreach ($year ? [$year] : self::years() as $y) {
            Cache::forget("season.settings.$y");
        }
    }

    /** Scheduled cup dates as month => date string (or null when unset). */
    public static function dates(int $year): array
    {
        $decoded = json_decode((string) self::get($year, 'dates'), true) ?: [];

        $dates = [];
        foreach (self::MONTHS as $month => $name) {
            $value = $decoded[$month] ?? $decoded[(string) $month] ?? null;
            $dates[$month] = ($value && $value !== 'n/a') ? $value : null;
        }

        return $dates;
    }

    public static function cupDate(int $year, int $month): ?Carbon
    {
        $value = self::dates($year)[$month] ?? null;

        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Ranking points: ['first' => [pos => points], 'final' => [table => [pos => points]]].
     */
    public static function points(int $year): array
    {
        $points = json_decode((string) self::get($year, 'points'), true);

        return is_array($points) ? $points : ['first' => [], 'final' => []];
    }

    /** Points awarded for a finishing position, table '1'..'10' or gold/silver/bronze. */
    public static function pointsFor(int $year, string $type, string $table, int $position): int
    {
        $points = self::points($year);

        if ($type === 'firstround') {
            // The legacy app grants one extra point for taking part.
            return (int) ($points['first'][$position] ?? $points['first'][(string) $position] ?? 0) + 1;
        }

        return (int) ($points['final'][$table][$position] ?? $points['final'][$table][(string) $position] ?? 0);
    }

    public static function footer(int $year): string
    {
        return (string) self::get($year, 'footer', '');
    }

    /** Forum thread links as month => url. */
    public static function forumLinks(int $year): array
    {
        $links = json_decode((string) self::get($year, 'forum_links'), true);

        return is_array($links) ? $links : [];
    }

    /** Last month that can already have results in the given season. */
    public static function lastPlayableMonth(int $year): int
    {
        return $year < self::current() ? 12 : (int) date('n');
    }

    public static function monthName(int $month): string
    {
        return self::MONTHS[$month] ?? '';
    }
}
