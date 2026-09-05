<?php

namespace App\Services;

use App\Models\Award;
use App\Models\Upload;
use App\Models\UploadLog;

/**
 * Builds the BBCode for the four forum posts boehmi writes by hand for every
 * cup: the announcement, the 1st round table seeding, the final round table
 * seeding, and the results/awards post. See https://www.pokerth.net/viewtopic.php?t=1257
 * for the posts this was modelled after.
 */
class ForumPostBuilder
{
    public static function announcement(int $year, int $month, array $config): string
    {
        $monthName = Season::monthName($month);
        $cupDate = $config['cup_date_label'] ?: '???';
        $seedingTime = $config['seeding_time_label'] ?: '???';
        $themeImage = $config['theme_image'];
        $admins = implode(', ', array_filter($config['admins']));
        $subs = implode(', ', array_filter($config['admin_subs']));

        $lines = [];
        if ($themeImage !== '') {
            $lines[] = "[align=center][img]{$themeImage}[/img][/align]";
            $lines[] = '';
        }

        $lines[] = "[align=center][size=200][color=red]{$monthName} Cup[/color][/size] [size=150]is now open for registration[/size]";
        $lines[] = "[size=150]Scheduled cup time is [/size][size=200][color=red]{$cupDate}[/color][/size].";
        $lines[] = '';
        $lines[] = '[size=200][color=blue]Please register[/color][/size] [color=red][size=200]> '
            .'[url='.route('registration')."]here[/url] < [/size][/color]";
        $lines[] = '';
        $lines[] = "[size=160]Every registration has to be confirmed by one of the Orga Team members - "
            .'so don\'t worry if your nickname does not show up immediately after registration.[/size]';
        $lines[] = '';
        $lines[] = '[size=160][color=#80FF00]The 1st round is a qualification for the final round[/color]';
        $lines[] = '[color=#40FF00]Registration[/color] will be closed and the [color=#40FF00]seeding for the 1st '
            ."round tables[/color] will be generated randomly [color=#40FF00]at {$seedingTime}[/color].";
        $lines[] = '';
        $lines[] = '[color=#40FF00]Final round[/color] with [color=#FFD700]gold[/color], [color=#C0C0C0]silver[/color], '
            .'[color=#bf8970]bronze[/color] table takes place after 1st round has finished.';
        $lines[] = '';
        $lines[] = 'The top 3 of each final table get an award in the [url='
            .route('results.halloffame', ['year' => $year])."]hall of fame[/url].[/size]";
        $lines[] = '';
        $lines[] = '[list][*][size=160]Monthlycup Series ranking points are calculated [url='
            .route('results.points', ['year' => $year])."]this way[/url].[/size][/list]";
        $lines[] = '[list][*][size=160]Table settings are [url='.route('table-settings')."]here[/url].[/size][/list]";
        $lines[] = '';
        $lines[] = '[color=red][size=200]Enjoy the CUP![/size][/color][/align]';
        $lines[] = '';
        $lines[] = "Table Admins: {$admins}";
        $lines[] = "Admin Subs: {$subs}";

        return implode("\n", $lines);
    }

    /**
     * @param  array<int,array{admin:string,players:array<int,string>}>  $tables
     * @param  array<int,string>  $substitutes
     */
    public static function seeding(int $year, int $month, array $tables, array $substitutes): string
    {
        $monthName = Season::monthName($month);
        $lines = ["[size=150][color=#337AB7]{$monthName} Cup {$year} - Table Seeding[/color][/size]"];

        foreach ($tables as $i => $table) {
            $n = $i + 1;
            $lines[] = '';
            $lines[] = "[color=#337AB7][u]{$monthName} Cup Table {$n} [/u][/color]";
            $lines[] = "[color=#3C763D][b]{$table['admin']}[/b][/color]";
            foreach ($table['players'] as $player) {
                $lines[] = $player;
            }
        }

        if ($substitutes) {
            $lines[] = '';
            $lines[] = '[color=#3C763D][b]Substitutes:[/b]'.implode(', ', $substitutes).'[/color]';
        }

        return implode("\n", $lines);
    }

    public static function finalSeeding(int $year, int $month): string
    {
        $monthName = Season::monthName($month);
        $rows = Upload::forYear($year)->where('month', $month)->where('type', 'firstround')
            ->orderBy('position')
            ->get()
            ->sortBy(fn (Upload $row) => (int) $row->table_name);

        $byTable = $rows->groupBy('table_name');
        $placements = ['gold' => [], 'silver' => [], 'bronze' => []];
        $target = [1 => 'gold', 2 => 'silver', 3 => 'bronze'];

        foreach ($byTable as $tableRows) {
            foreach ($tableRows as $row) {
                if (isset($target[$row->position])) {
                    $placements[$target[$row->position]][] = $row->playername;
                }
            }
        }

        $lines = ["[size=150][color=#337AB7]{$monthName} Cup {$year} - Final Tables[/color][/size]"];

        foreach (['gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'] as $key => $label) {
            $lines[] = '';
            $lines[] = "[color=#337AB7][u]{$label} Table [/u][/color]";
            foreach ($placements[$key] as $player) {
                $lines[] = $player;
            }
        }

        $logs = UploadLog::forYear($year)->where('month', $month)->where('type', 'firstround')
            ->get()->sortBy(fn (UploadLog $log) => (int) $log->table_name);

        $logLines = $logs->map(fn (UploadLog $log) => "Table {$log->table_name}: {$log->url}")->all();

        if ($logLines) {
            $lines[] = '';
            $lines[] = '---';
            $lines[] = '';
            $lines[] = 'log-links:';
            foreach ($logLines as $logLine) {
                $lines[] = '';
                $lines[] = $logLine;
            }
        }

        return implode("\n", $lines);
    }

    public static function results(int $year, int $month): string
    {
        $monthName = Season::monthName($month);

        $podium = Upload::forYear($year)->where('month', $month)
            ->where('type', 'final')->where('table_name', 'gold')
            ->where('position', '<=', 3)->orderBy('position')->get()
            ->keyBy('position');

        $awards = Award::forYear($year)->where('month', $month)
            ->whereIn('type', ['gold1st', 'gold2nd', 'gold3rd'])->get()->keyBy('type');

        $places = [
            1 => ['type' => 'gold1st', 'color' => '#bb8800'],
            2 => ['type' => 'gold2nd', 'color' => '#999999'],
            3 => ['type' => 'gold3rd', 'color' => '#996633'],
        ];

        $lines = ['[align=center][color=#337AB7][size=150]'.$monthName." Cup {$year} Results[/size][/color]"];

        foreach ($places as $position => $meta) {
            $name = $podium[$position]->playername ?? '???';
            $awardUrl = $awards[$meta['type']]->url ?? null;
            $lines[] = '';
            if ($awardUrl) {
                $lines[] = "[img]{$awardUrl}[/img]";
            }
            $lines[] = "[color={$meta['color']}][size=150][b]{$name}[/b][/size][/color]";
        }

        $lines[] = '';
        $lines[] = '——————————————';
        $lines[] = '';
        $lines[] = '[url='.route('results.cup', ['month' => $month, 'year' => $year])
            .'][color=#3C763D][size=150][b]Results[/b][/size][/color][/url]';
        $lines[] = '';
        $lines[] = '[url='.route('results.rankings', ['year' => $year])
            .'][color=#3C763D][size=150][b]Ranking[/b][/size][/color][/url]';
        $lines[] = '';
        $lines[] = '[url='.route('results.halloffame', ['year' => $year])
            .'][color=#3C763D][size=150][b]Hall of Fame[/b][/size][/color][/url]';
        $lines[] = '';
        $champion = $podium[1]->playername ?? '???';
        $lines[] = "[color=#FE0300][size=150]Congrats Champion of {$monthName} {$year}: {$champion}[/size][/color]";
        $lines[] = '[/align]';
        $lines[] = '';
        $lines[] = 'Thank you to all admins - thank you to all players!';

        return implode("\n", $lines);
    }
}
