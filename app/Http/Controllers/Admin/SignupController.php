<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signup;
use App\Services\Season;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function index(Request $request)
    {
        $year = Season::current();
        $month = (int) $request->query('month', date('n'));

        return view('admin.signups', [
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'dateLabel' => Season::cupDate($year, $month)?->format('l, F jS Y, H:i'),
            'signups' => $this->list($year, $month),
            'months' => collect(Season::MONTHS)->map(fn ($name, $m) => ['value' => $m, 'label' => $name])->values(),
        ]);
    }

    public function validateSignup(Signup $signup)
    {
        $signup->update(['valid' => true]);

        return response()->json([
            'success' => true,
            'message' => "Signup of {$signup->playername} accepted.",
            'signups' => $this->list($signup->year, $signup->month),
        ]);
    }

    public function reject(Signup $signup)
    {
        $signup->update(['valid' => false]);

        return response()->json([
            'success' => true,
            'message' => "Signup of {$signup->playername} set back to pending.",
            'signups' => $this->list($signup->year, $signup->month),
        ]);
    }

    public function destroy(Signup $signup)
    {
        [$year, $month, $name] = [$signup->year, $signup->month, $signup->playername];
        $signup->delete();

        return response()->json([
            'success' => true,
            'message' => "Signup of $name deleted.",
            'signups' => $this->list($year, $month),
        ]);
    }

    /** Random seeding order of the accepted players. */
    public function randomizer(Request $request)
    {
        $year = Season::current();
        $month = (int) $request->query('month', date('n'));
        $seats = (int) config('mcup.seats');

        $accepted = Signup::forCup($year, $month)
            ->where('valid', true)
            ->orderBy('registered_at')
            ->orderBy('id')
            ->pluck('playername');

        $players = $accepted->take($seats)->shuffle()->values()->all();

        return view('admin.randomizer', [
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'players' => $players,
            'substitutes' => $accepted->slice($seats)->values()->all(),
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    private function list(int $year, int $month): array
    {
        return Signup::forCup($year, $month)
            ->orderByDesc('registered_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Signup $s) => [
                'id' => $s->id,
                'playername' => $s->playername,
                'registered_at' => $s->registered_at?->format('Y-m-d H:i:s'),
                'ip' => $s->ip,
                'fp' => $s->fp,
                'fpnew' => $s->fpnew,
                'valid' => $s->valid,
            ])->all();
    }
}
