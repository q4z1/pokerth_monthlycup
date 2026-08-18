<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Upload;
use App\Services\LogParser;
use App\Services\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Imports the result of a played cup table from a PokerTH game log.
 */
class UploadController extends Controller
{
    public function firstround()
    {
        return view('admin.upload', [
            'mode' => 'firstround',
            'title' => 'Upload 1st round table',
            'tables' => collect(range(1, 10))->map(fn ($i) => ['value' => (string) $i, 'label' => 'Table '.$i])->all(),
            'months' => $this->months(),
            'month' => (int) date('n'),
            'year' => Season::current(),
        ]);
    }

    public function finaltable()
    {
        return view('admin.upload', [
            'mode' => 'final',
            'title' => 'Upload final table',
            'tables' => collect(Upload::FINAL_TABLES)->map(fn ($label, $key) => ['value' => $key, 'label' => $label])->values()->all(),
            'months' => $this->months(),
            'month' => (int) date('n'),
            'year' => Season::current(),
        ]);
    }

    /** Parses the log and shows what would be stored, without writing anything. */
    public function preview(Request $request)
    {
        $data = $this->validated($request);
        $players = $this->parse($data['url']);

        if (! is_array($players)) {
            return response()->json(['success' => false, 'message' => $players], 422);
        }

        $year = Season::current();

        if ($this->alreadyUploaded($year, $data)) {
            return response()->json([
                'success' => false,
                'message' => Season::monthName($data['month']).' – '.$data['type'].' table '
                    .$data['table'].' was already uploaded!',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'rows' => $this->rows($year, $data, $players),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $players = $this->parse($data['url']);

        if (! is_array($players)) {
            return response()->json(['success' => false, 'message' => $players], 422);
        }

        $year = Season::current();

        if ($this->alreadyUploaded($year, $data)) {
            return response()->json([
                'success' => false,
                'message' => Season::monthName($data['month']).' – '.$data['type'].' table '
                    .$data['table'].' was already uploaded!',
            ], 422);
        }

        $rows = $this->rows($year, $data, $players);

        DB::transaction(function () use ($year, $data, $rows) {
            foreach ($rows as $row) {
                Player::firstOrCreate(['year' => $year, 'playername' => $row['playername']]);

                Upload::create([
                    'year' => $year,
                    'type' => $data['type'],
                    'table_name' => $data['table'],
                    'month' => $data['month'],
                    'playername' => $row['playername'],
                    'position' => $row['position'],
                    'points' => $row['points'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => Season::monthName($data['month']).' – '.$data['type'].' table '
                .$data['table'].' successfully uploaded!',
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:firstround,final'],
            'month' => ['required', 'integer', 'between:1,12'],
            'table' => ['required', 'string', 'max:16'],
            'url' => ['required', 'string', 'max:512'],
        ]);

        $allowed = $data['type'] === 'final'
            ? array_keys(Upload::FINAL_TABLES)
            : array_map('strval', range(1, 10));

        abort_unless(in_array($data['table'], $allowed, true), 422, 'Unknown table.');

        $data['month'] = (int) $data['month'];

        return $data;
    }

    /** @return array<int,string>|string the finishing order, or an error message */
    private function parse(string $url): array|string
    {
        $result = (new LogParser)->process_log($url);

        if (($result['status'] ?? 'error') !== 'success') {
            return is_string($result['data'] ?? null) ? $result['data'] : 'The game log could not be read.';
        }

        $players = $result['data']['player_list'][1] ?? null;

        if (! is_array($players) || $players === []) {
            return 'The game log contains no players.';
        }

        return array_values($players);
    }

    private function alreadyUploaded(int $year, array $data): bool
    {
        return Upload::forYear($year)
            ->where('type', $data['type'])
            ->where('month', $data['month'])
            ->where('table_name', $data['table'])
            ->exists();
    }

    private function rows(int $year, array $data, array $players): array
    {
        $rows = [];
        foreach ($players as $i => $playername) {
            $position = $i + 1;
            $rows[] = [
                'position' => $position,
                'playername' => $playername,
                'points' => Season::pointsFor($year, $data['type'], $data['table'], $position),
            ];
        }

        return $rows;
    }

    private function months(): array
    {
        return collect(Season::MONTHS)->map(fn ($name, $m) => ['value' => $m, 'label' => $name])->values()->all();
    }
}
