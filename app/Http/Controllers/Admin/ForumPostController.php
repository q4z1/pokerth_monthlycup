<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Signup;
use App\Services\ForumPostBuilder;
use App\Services\Season;
use Illuminate\Http\Request;

/**
 * Generates the BBCode boehmi posts by hand for every cup: the announcement,
 * the 1st round table seeding, the final round table seeding and the
 * results/awards post. See https://www.pokerth.net/viewtopic.php?t=1257
 */
class ForumPostController extends Controller
{
    public function index(Request $request)
    {
        $year = Season::current();
        $month = (int) $request->query('month', date('n'));
        $config = $this->withDateDefault(Season::forumPostConfig($year, $month), $year, $month);

        [$tables, $substitutes] = $this->seedTables($year, $month, $config);

        return view('admin.forum-posts', [
            'year' => $year,
            'month' => $month,
            'monthName' => Season::monthName($month),
            'months' => collect(Season::MONTHS)->map(fn ($name, $m) => ['value' => $m, 'label' => $name])->values(),
            'config' => $config,
            'signupCount' => Signup::forCup($year, $month)->where('valid', true)->count(),
            'announcement' => ForumPostBuilder::announcement($year, $month, $config),
            'seeding' => ForumPostBuilder::seeding($year, $month, $tables, $substitutes),
            'seedingTables' => $tables,
            'seedingSubstitutes' => $substitutes,
            'finalSeeding' => ForumPostBuilder::finalSeeding($year, $month),
            'results' => ForumPostBuilder::results($year, $month),
        ]);
    }

    /** Save the announcement/seeding config and return both regenerated posts. */
    public function saveConfig(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'admins' => ['present', 'array'],
            'admins.*' => ['nullable', 'string', 'max:64'],
            'admin_subs' => ['present', 'array'],
            'admin_subs.*' => ['nullable', 'string', 'max:64'],
            'players_per_table' => ['required', 'integer', 'min:1', 'max:20'],
            'theme_image' => ['nullable', 'string', 'max:512'],
            'cup_date_label' => ['nullable', 'string', 'max:128'],
            'seeding_time_label' => ['nullable', 'string', 'max:128'],
        ]);

        $year = (int) $data['year'];
        $month = (int) $data['month'];

        $config = [
            'admins' => array_values(array_filter($data['admins'], fn ($v) => trim((string) $v) !== '')),
            'admin_subs' => array_values(array_filter($data['admin_subs'], fn ($v) => trim((string) $v) !== '')),
            'players_per_table' => (int) $data['players_per_table'],
            'theme_image' => trim((string) ($data['theme_image'] ?? '')),
            'cup_date_label' => trim((string) ($data['cup_date_label'] ?? '')),
            'seeding_time_label' => trim((string) ($data['seeding_time_label'] ?? '')),
        ];

        $decoded = json_decode((string) Season::get($year, 'forum_post_config'), true) ?: [];
        $decoded[$month] = $config;
        Setting::updateOrCreate(['year' => $year, 'type' => 'forum_post_config'], ['value' => json_encode($decoded)]);
        Season::forget($year);

        [$tables, $substitutes] = $this->seedTables($year, $month, $config);

        return response()->json([
            'success' => true,
            'message' => 'Table admins saved.',
            'announcement' => ForumPostBuilder::announcement($year, $month, $this->withDateDefault($config, $year, $month)),
            'seeding' => ForumPostBuilder::seeding($year, $month, $tables, $substitutes),
            'seeding_tables' => $tables,
            'seeding_substitutes' => $substitutes,
        ]);
    }

    /** Re-shuffle the non-admin signups into the tables, keeping the saved admins. */
    public function shuffle(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);

        $year = (int) $data['year'];
        $month = (int) $data['month'];
        $config = Season::forumPostConfig($year, $month);

        [$tables, $substitutes] = $this->seedTables($year, $month, $config);

        return response()->json([
            'success' => true,
            'seeding' => ForumPostBuilder::seeding($year, $month, $tables, $substitutes),
            'seeding_tables' => $tables,
            'seeding_substitutes' => $substitutes,
        ]);
    }

    /**
     * Fills in the cup date/time (from Admin > Settings) and the season's
     * theme image (the same one shown on the homepage) when the admin
     * hasn't typed something of their own. Never persisted, so a later
     * change of either is always picked up.
     */
    private function withDateDefault(array $config, int $year, int $month): array
    {
        if ($config['cup_date_label'] === '') {
            $date = Season::cupDate($year, $month);
            $config['cup_date_label'] = $date ? $date->format('F jS - H:i T') : '';
        }

        if ($config['theme_image'] === '') {
            $config['theme_image'] = asset(config('mcup.theme_image'));
        }

        return $config;
    }

    /**
     * @return array{0:array<int,array{admin:string,players:array<int,string>}>,1:array<int,string>}
     */
    private function seedTables(int $year, int $month, array $config): array
    {
        $admins = $config['admins'];
        $perTable = $config['players_per_table'];

        if (! $admins) {
            return [[], []];
        }

        $adminNames = collect($admins)->map(fn ($n) => mb_strtolower(trim($n)))->all();

        $pool = Signup::forCup($year, $month)->where('valid', true)
            ->orderBy('registered_at')->orderBy('id')->pluck('playername')
            ->reject(fn ($name) => in_array(mb_strtolower(trim($name)), $adminNames, true))
            ->values()
            ->shuffle();

        $tables = [];
        $offset = 0;
        foreach ($admins as $admin) {
            $tables[] = [
                'admin' => $admin,
                'players' => $pool->slice($offset, $perTable)->values()->all(),
            ];
            $offset += $perTable;
        }

        $substitutes = $pool->slice($offset)->values()->all();

        return [$tables, $substitutes];
    }
}
