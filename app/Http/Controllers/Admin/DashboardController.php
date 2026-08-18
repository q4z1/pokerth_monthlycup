<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Player;
use App\Models\Signup;
use App\Models\Upload;
use App\Services\Season;

class DashboardController extends Controller
{
    public function index()
    {
        $year = Season::current();
        $month = (int) date('n');

        return view('admin.dashboard', [
            'year' => $year,
            'monthName' => Season::monthName($month),
            'stats' => [
                'signups' => Signup::forCup($year, $month)->count(),
                'signups_accepted' => Signup::forCup($year, $month)->where('valid', true)->count(),
                'players' => Player::forYear($year)->count(),
                'awards' => Award::forYear($year)->count(),
                'uploaded_tables' => Upload::forYear($year)->where('month', $month)
                    ->distinct()->get(['type', 'table_name'])->count(),
            ],
            'cup' => [
                'month_name' => Season::monthName($month),
                'date_label' => Season::cupDate($year, $month)?->format('l, F jS Y, H:i'),
            ],
        ]);
    }
}
