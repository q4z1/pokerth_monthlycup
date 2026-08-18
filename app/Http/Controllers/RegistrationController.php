<?php

namespace App\Http\Controllers;

use App\Models\Signup;
use App\Services\Season;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /** The registration form for the cup of the current month. */
    public function create()
    {
        $year = Season::current();
        $month = (int) date('n');

        return view('registration', [
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'cup' => $this->cupInfo($year, $month),
        ]);
    }

    /** Public list of everybody registered for the current cup. */
    public function index()
    {
        $year = Season::current();
        $month = (int) date('n');

        $signups = Signup::forCup($year, $month)
            ->where('valid', true)
            ->orderBy('registered_at')
            ->orderBy('id')
            ->get(['playername', 'registered_at']);

        $seats = (int) config('mcup.seats');

        // Registrations only show up publicly once the orga team accepted them.
        $pending = Signup::forCup($year, $month)->where('valid', false)->count();

        return view('signups', [
            'pending' => $pending,
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'cup' => $this->cupInfo($year, $month),
            'players' => $signups->take($seats)->values()->map(fn ($s, $i) => [
                'no' => $i + 1,
                'playername' => $s->playername,
            ])->all(),
            'substitutes' => $signups->slice($seats)->pluck('playername')->values()->all(),
        ]);
    }

    public function store(Request $request)
    {
        $year = Season::current();
        $month = (int) date('n');

        $data = $request->validate([
            'playername' => ['required', 'string', 'max:64'],
            'fp' => ['nullable', 'string', 'max:64'],
            'fpnew' => ['nullable', 'string', 'max:64'],
        ]);

        $playername = trim($data['playername']);
        $cup = $this->cupInfo($year, $month);

        if (! $cup['open']) {
            throw ValidationException::withMessages([
                'playername' => 'Registration is closed.',
            ]);
        }

        if (config('mcup.verify_playername') && ! $this->playerExistsAtPokerTH($playername)) {
            throw ValidationException::withMessages([
                'playername' => "Playername $playername does not exist at PokerTH!",
            ]);
        }

        $exists = Signup::forCup($year, $month)->where('playername', $playername)->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'playername' => "The player $playername is already registered!",
            ]);
        }

        Signup::create([
            'year' => $year,
            'month' => $month,
            'playername' => $playername,
            'registered_at' => Carbon::now(),
            'ip' => $request->header('CF-Connecting-IP') ?: $request->ip(),
            'fp' => $data['fp'] ?? null,
            'fpnew' => $data['fpnew'] ?? null,
            'valid' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Thank you $playername – you have registered!",
        ]);
    }

    /** Playernames must exist in the PokerTH ranking database. */
    private function playerExistsAtPokerTH(string $playername): bool
    {
        try {
            return DB::connection('pth_ranking')
                ->table('player')
                ->where('username', $playername)
                ->exists();
        } catch (\Throwable $e) {
            report($e);

            // Never block a registration because the ranking database is down.
            return true;
        }
    }

    /** Scheduled date of a cup plus whether registration is still open. */
    private function cupInfo(int $year, int $month): array
    {
        $date = Season::cupDate($year, $month);
        $closesAt = $date?->copy()->subMinutes((int) config('mcup.registration_closes_before'));

        return [
            'month' => $month,
            'month_name' => Season::monthName($month),
            'date' => $date?->toIso8601String(),
            'date_label' => $date?->format('l, F jS Y, H:i'),
            'closes_at' => $closesAt?->toIso8601String(),
            'open' => $date !== null && $closesAt->isFuture(),
        ];
    }
}
