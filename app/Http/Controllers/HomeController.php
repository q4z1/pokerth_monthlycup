<?php

namespace App\Http\Controllers;

use App\Models\Signup;
use App\Models\Upload;
use App\Services\Season;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $year = Season::resolve($request->query('year'));
        $month = (int) date('n');

        $nextCup = null;
        foreach (Season::dates($year) as $m => $date) {
            $cupDate = Season::cupDate($year, $m);
            if ($cupDate && $cupDate->isFuture()) {
                $nextCup = [
                    'month' => $m,
                    'month_name' => Season::monthName($m),
                    'date' => $cupDate->toIso8601String(),
                    'date_label' => $cupDate->format('l, F jS Y, H:i'),
                ];
                break;
            }
        }

        $latestMonth = (int) Upload::forYear($year)->max('month');
        $podium = $latestMonth
            ? Upload::forYear($year)
                ->where('month', $latestMonth)
                ->where('type', 'final')
                ->where('table_name', 'gold')
                ->where('position', '<=', 3)
                ->orderBy('position')
                ->get(['playername', 'position'])
                ->all()
            : [];

        return view('home', [
            'year' => $year,
            'nextCup' => $nextCup,
            'signupCount' => Signup::forCup($year, $month)->where('valid', true)->count(),
            'latestCup' => $latestMonth ? [
                'month' => $latestMonth,
                'month_name' => Season::monthName($latestMonth),
                'podium' => $podium,
            ] : null,
        ]);
    }

    /** Static description of the table settings used for the cup games. */
    public function tableSettings()
    {
        return view('table-settings');
    }
}
