<?php

namespace Tests\Feature;

use App\Models\Award;
use App\Models\Player;
use App\Models\Upload;
use App\Services\AvatarBlacklist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AvatarBlacklistTest extends TestCase
{
    use RefreshDatabase;

    private string $png;

    private int $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = (int) date('Y');
        $this->png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
    }

    /** The blacklist lives in another database, so it is faked through its cache. */
    private function blacklist(array $hashes): void
    {
        Cache::forever('avatar.blacklist', $hashes);
    }

    private function player(string $name): Player
    {
        return Player::create([
            'year' => $this->year,
            'playername' => $name,
            'avatar' => $this->png,
            'avatar_mime' => 'image/png',
            'avatar_hash' => md5($this->png),
        ]);
    }

    public function test_a_clean_avatar_is_served(): void
    {
        $this->blacklist([]);
        $player = $this->player('Clean');

        $this->assertTrue($player->hasAvatar());
        $this->get(route('player.avatar', $player))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_a_blacklisted_avatar_is_not_served(): void
    {
        $this->blacklist([md5($this->png)]);
        $player = $this->player('Blocked');

        $this->assertFalse($player->fresh()->hasAvatar());
        $this->get(route('player.avatar', $player))->assertNotFound();
    }

    public function test_the_hall_of_fame_omits_a_blacklisted_avatar(): void
    {
        $this->blacklist([md5($this->png)]);
        $player = $this->player('Blocked');

        $award = Award::create([
            'year' => $this->year, 'month' => 3, 'type' => 'gold1st',
            'file' => $this->png, 'mime' => 'image/png',
        ]);
        $award->players()->attach($player);
        Upload::create([
            'year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 3, 'playername' => 'Blocked', 'position' => 1, 'points' => 36,
        ]);

        $response = $this->get(route('results.halloffame'));

        $response->assertOk();
        // the player stays listed, only the avatar url is withheld
        $this->assertStringContainsString('Blocked', $response->getContent());
        $this->assertStringNotContainsString('media\\/avatar', $response->getContent());
    }

    public function test_the_legacy_avatar_url_also_refuses_a_blacklisted_image(): void
    {
        $this->blacklist([md5($this->png)]);
        $this->player('Blocked');

        $this->get('/res/avatar?playername=Blocked')->assertNotFound();
    }

    public function test_a_player_without_an_avatar_is_unaffected(): void
    {
        $this->blacklist([md5($this->png)]);
        $player = Player::create(['year' => $this->year, 'playername' => 'NoPic']);

        $this->assertFalse($player->hasAvatar());
    }

    public function test_blocks_ignores_an_empty_hash(): void
    {
        $this->blacklist([md5($this->png)]);

        $this->assertFalse(AvatarBlacklist::blocks(null));
        $this->assertFalse(AvatarBlacklist::blocks(''));
        $this->assertTrue(AvatarBlacklist::blocks(md5($this->png)));
    }
}
