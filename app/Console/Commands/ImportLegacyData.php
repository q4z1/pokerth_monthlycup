<?php

namespace App\Console\Commands;

use App\Models\Award;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Signup;
use App\Models\Upload;
use App\Models\User;
use App\Services\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copies the data of the legacy per-year tables (award2021, player2021, ...)
 * into the normalised tables of the Laravel application. The legacy tables are
 * left untouched so the import can be repeated at any time.
 */
class ImportLegacyData extends Command
{
    protected $signature = 'mcup:import-legacy
                            {--fresh : truncate the target tables before importing}
                            {--year=* : only import these seasons}';

    protected $description = 'Import the legacy per-year tables into the normalised schema';

    public function handle(): int
    {
        $years = $this->option('year') ?: $this->discoverYears();

        if (empty($years)) {
            $this->error('No legacy tables found in database '.config('database.connections.mysql.database').'.');

            return self::FAILURE;
        }

        $this->info('Seasons found: '.implode(', ', $years));

        if ($this->option('fresh')) {
            $this->warn('Truncating target tables ...');
            Schema::disableForeignKeyConstraints();
            foreach (['award_player', 'awards', 'players', 'signups', 'uploads', 'settings', 'users'] as $table) {
                DB::table($table)->truncate();
            }
            Schema::enableForeignKeyConstraints();
        }

        $this->importUsers();

        foreach ($years as $year) {
            $year = (int) $year;
            $this->line("── season $year");
            $this->importSettings($year);
            $this->importPlayers($year);
            $this->importAwards($year);
            $this->importUploads($year);
            $this->importSignups($year);
            $this->importAwardAssignments($year);
        }

        Season::forget();

        $this->newLine();
        $this->info('Import finished.');
        $this->table(
            ['table', 'rows'],
            collect(['users', 'settings', 'players', 'awards', 'award_player', 'signups', 'uploads'])
                ->map(fn ($t) => [$t, DB::table($t)->count()])
                ->all()
        );

        return self::SUCCESS;
    }

    /** Every year for which at least one legacy table exists. */
    private function discoverYears(): array
    {
        $years = [];
        foreach (Schema::getTableListing() as $table) {
            $table = str_contains($table, '.') ? substr(strrchr($table, '.'), 1) : $table;
            if (preg_match('/^(?:award|player|settings|signup|upload)(\d{4})$/', $table, $m)) {
                $years[(int) $m[1]] = (int) $m[1];
            }
        }
        ksort($years);

        return array_values($years);
    }

    private function importUsers(): void
    {
        if (! Schema::hasTable('admin')) {
            $this->warn('  no legacy admin table – skipping users');

            return;
        }

        $count = 0;
        foreach (DB::table('admin')->get() as $row) {
            // Users without a password could never log in and are skipped.
            if (trim((string) $row->password) === '') {
                $this->warn("  skipped admin '{$row->username}' (no password set)");

                continue;
            }

            $user = User::firstOrNew(['username' => $row->username]);
            $user->name = $row->fullname ?: null;
            $user->email = $row->email ?: null;
            $user->active = (bool) $row->active;
            $user->last_login_at = $row->last_login ?: null;
            // Keep the MD5 hash as-is; it is upgraded to bcrypt on first login.
            $user->setRawAttributes(array_merge($user->getAttributes(), ['password' => $row->password]));
            $user->save();
            $count++;
        }

        $this->line("  users: $count");
    }

    private function importSettings(int $year): void
    {
        $table = "settings$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $count = 0;
        foreach (DB::table($table)->get() as $row) {
            if (! $row->type) {
                continue;
            }
            Setting::updateOrCreate(
                ['year' => $year, 'type' => $row->type],
                ['value' => $row->value]
            );
            $count++;
        }

        $this->line("  settings: $count");
    }

    private function importPlayers(int $year): void
    {
        $table = "player$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $count = 0;
        foreach (DB::table($table)->orderBy("{$table}_id")->get() as $row) {
            if (! $row->playername) {
                continue;
            }

            $player = Player::firstOrNew(['year' => $year, 'playername' => $row->playername]);
            $player->avatar = $row->avatar !== null ? stripslashes($row->avatar) : null;
            $player->avatar_mime = $row->avatar_mime ?: null;
            $player->save();
            $count++;
        }

        $this->line("  players: $count");
    }

    private function importAwards(int $year): void
    {
        $table = "award$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $count = 0;
        foreach (DB::table($table)->orderBy("{$table}_id")->get() as $row) {
            $award = Award::firstOrNew([
                'year' => $year,
                'month' => $row->month,
                'type' => $row->type,
            ]);
            $award->file = $row->file !== null ? stripslashes($row->file) : null;
            $award->filename = $row->filename ?: null;
            $award->mime = $row->mime ?: null;
            $award->save();
            $count++;
        }

        $this->line("  awards: $count");
    }

    /**
     * The legacy player table kept its awards in a JSON column holding
     * [{"month": 4, "type": "gold1st"}, ...]. Those become pivot rows.
     */
    private function importAwardAssignments(int $year): void
    {
        $table = "player$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $awards = Award::forYear($year)->get();
        $byMonthType = $awards->keyBy(fn ($a) => $a->month.'|'.$a->type);
        $byType = $awards->keyBy('type');

        $count = 0;
        $missing = 0;
        foreach (DB::table($table)->get() as $row) {
            if (! $row->playername || ! $row->awards) {
                continue;
            }

            $assignments = json_decode($row->awards, true);
            if (! is_array($assignments)) {
                continue;
            }

            $player = Player::where(['year' => $year, 'playername' => $row->playername])->first();
            if (! $player) {
                continue;
            }

            $ids = [];
            foreach ($assignments as $assignment) {
                $type = $assignment['type'] ?? null;
                $month = $assignment['month'] ?? null;
                if (! $type) {
                    continue;
                }

                // Season awards were looked up by type only in the legacy code.
                $award = in_array($type, Award::SEASON_TYPES, true)
                    ? ($byType[$type] ?? null)
                    : ($byMonthType[$month.'|'.$type] ?? null);

                if (! $award) {
                    $missing++;

                    continue;
                }
                $ids[] = $award->id;
            }

            if ($ids) {
                $player->awards()->syncWithoutDetaching(array_unique($ids));
                $count += count(array_unique($ids));
            }
        }

        $this->line("  award assignments: $count".($missing ? " ($missing unresolved)" : ''));
    }

    private function importSignups(int $year): void
    {
        $table = "signup$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $rows = [];
        foreach (DB::table($table)->orderBy("{$table}_id")->get() as $row) {
            if (! $row->playername || $row->month === null) {
                continue;
            }
            $rows[] = [
                'year' => $year,
                'month' => (int) $row->month,
                'playername' => $row->playername,
                'registered_at' => $row->date,
                'ip' => $row->ip,
                'fp' => $row->fp,
                'fpnew' => $row->fpnew,
                'valid' => (bool) $row->valid,
                'created_at' => $row->date ?: now(),
                'updated_at' => $row->date ?: now(),
            ];
        }

        Signup::where('year', $year)->delete();
        foreach (array_chunk($rows, 500) as $chunk) {
            Signup::insert($chunk);
        }

        $this->line('  signups: '.count($rows));
    }

    private function importUploads(int $year): void
    {
        $table = "upload$year";
        if (! Schema::hasTable($table)) {
            return;
        }

        $rows = [];
        foreach (DB::table($table)->get() as $row) {
            $rows[] = [
                'year' => $year,
                'type' => $row->type,
                'table_name' => $row->table_,
                'month' => (int) $row->month,
                'playername' => $row->playername,
                'position' => (int) $row->position,
                'points' => (int) $row->points,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Upload::where('year', $year)->delete();
        foreach (array_chunk($rows, 500) as $chunk) {
            Upload::insert($chunk);
        }

        $this->line('  uploads: '.count($rows));
    }
}
