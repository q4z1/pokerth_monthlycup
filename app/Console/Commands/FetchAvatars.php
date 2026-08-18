<?php

namespace App\Console\Commands;

use App\Models\Player;
use App\Services\Season;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Fetches the current game avatar of every player of a season from pokerth.net.
 * Replaces the legacy cron/avatars.php.
 */
class FetchAvatars extends Command
{
    protected $signature = 'mcup:fetch-avatars {--year= : the season to update, defaults to the current one}';

    protected $description = 'Fetch the PokerTH game avatars of all players of a season';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: Season::current());
        $players = Player::forYear($year)->orderBy('playername')->get();

        if ($players->isEmpty()) {
            $this->warn("No players found for season $year.");

            return self::SUCCESS;
        }

        $updated = 0;
        $bar = $this->output->createProgressBar($players->count());
        $bar->start();

        foreach ($players as $player) {
            try {
                $profile = Http::timeout(15)
                    ->get('https://pokerth.net/pthranking/player/show', ['username' => $player->playername])
                    ->json();

                $hash = $profile['player']['avatar_hash'] ?? '';
                $mime = $profile['player']['avatar_mime'] ?? '';

                if ($hash === '' || $mime === '') {
                    $bar->advance();

                    continue;
                }

                $image = Http::timeout(30)->get("https://pokerth.net/images/avatars/game/$hash.$mime");

                if (! $image->successful()) {
                    $bar->advance();

                    continue;
                }

                $player->update(['avatar' => $image->body(), 'avatar_mime' => $mime]);
                $updated++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("  {$player->playername}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Updated $updated of {$players->count()} avatars for season $year.");

        return self::SUCCESS;
    }
}
