<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Player;
use App\Services\Season;
use Illuminate\Http\Request;

/**
 * Keeps the bookmarked URLs of the legacy application working.
 */
class LegacyRedirectController extends Controller
{
    public function results(Request $request, ?string $action = null, ?string $month = null)
    {
        $query = $request->only('year');

        return match ($action) {
            'cup' => redirect()->route('results.cup', array_filter(['month' => $month]) + $query, 301),
            'rankings' => redirect()->route('results.rankings', $query, 301),
            'halloffame' => redirect()->route('results.halloffame', $query, 301),
            'points' => redirect()->route('results.points', $query, 301),
            default => redirect()->route('results.series', $query, 301),
        };
    }

    /**
     * Award images used to be addressed as /res/award/?type=gold1st&month=4&year=2025.
     * Those links live on in forum posts, so they keep working.
     */
    public function award(Request $request)
    {
        $type = (string) $request->query('type');
        $year = (int) ($request->query('year') ?: Season::current());
        $month = (int) ($request->query('month') ?: date('n'));

        $award = Award::forYear($year)
            ->where('type', $type)
            ->when(! in_array($type, Award::SEASON_TYPES, true), fn ($q) => $q->where('month', $month))
            ->first();

        abort_unless($award, 404);

        return redirect()->route('award.image', $award, 301);
    }

    /** Avatars used to be addressed as /res/avatar/?playername=Somebody. */
    public function avatar(Request $request)
    {
        $player = Player::forYear(Season::current())
            ->where('playername', (string) $request->query('playername'))
            ->first();

        abort_unless($player && $player->hasAvatar(), 404);

        return redirect()->route('player.avatar', $player, 301);
    }

    /** Template pictures moved from /res/pic/<template>/<file> to /images/<file>. */
    public function picture(string $path)
    {
        return redirect('/images/'.basename($path), 301);
    }
}
