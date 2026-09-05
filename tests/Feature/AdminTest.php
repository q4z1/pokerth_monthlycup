<?php

namespace Tests\Feature;

use App\Models\Award;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Signup;
use App\Models\Upload;
use App\Models\UploadLog;
use App\Models\User;
use App\Services\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private int $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = (int) date('Y');
        $this->admin = User::create(['username' => 'admin', 'password' => 'secret123', 'active' => true]);
        $this->actingAs($this->admin);

        Setting::create([
            'year' => $this->year,
            'type' => 'points',
            'value' => json_encode([
                'first' => [1 => 12, 2 => 9, 3 => 7],
                'final' => ['gold' => [1 => 36, 2 => 26, 3 => 22]],
            ]),
        ]);
        Season::forget();
    }

    // ------------------------------------------------------------- signups

    public function test_an_admin_can_accept_and_revoke_a_signup(): void
    {
        $signup = Signup::create([
            'year' => $this->year, 'month' => (int) date('n'),
            'playername' => 'Somebody', 'registered_at' => now(), 'valid' => false,
        ]);

        $this->postJson(route('admin.signups.accept', $signup))
            ->assertOk()->assertJson(['success' => true]);
        $this->assertTrue($signup->fresh()->valid);

        $this->postJson(route('admin.signups.reject', $signup))->assertOk();
        $this->assertFalse($signup->fresh()->valid);
    }

    public function test_an_admin_can_delete_a_signup(): void
    {
        $signup = Signup::create([
            'year' => $this->year, 'month' => (int) date('n'),
            'playername' => 'Somebody', 'registered_at' => now(), 'valid' => true,
        ]);

        $this->deleteJson(route('admin.signups.destroy', $signup))->assertOk();
        $this->assertDatabaseMissing('signups', ['id' => $signup->id]);
    }

    public function test_the_randomizer_only_seeds_accepted_players(): void
    {
        $month = (int) date('n');
        Signup::create(['year' => $this->year, 'month' => $month, 'playername' => 'Yes', 'registered_at' => now(), 'valid' => true]);
        Signup::create(['year' => $this->year, 'month' => $month, 'playername' => 'No', 'registered_at' => now(), 'valid' => false]);

        $response = $this->get(route('admin.randomizer'));

        $response->assertOk();
        $this->assertStringContainsString('Yes', $response->getContent());
        $this->assertStringNotContainsString('&quot;No&quot;', $response->getContent());
    }

    // -------------------------------------------------------------- awards

    public function test_an_admin_can_upload_an_award(): void
    {
        $response = $this->post(route('admin.awards.store'), [
            'year' => $this->year,
            'month' => 5,
            'type' => 'gold1st',
            'file' => UploadedFile::fake()->image('gold.png', 150, 150),
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('awards', ['year' => $this->year, 'month' => 5, 'type' => 'gold1st']);
        $this->assertNotEmpty(Award::first()->file);
    }

    public function test_the_same_award_cannot_be_uploaded_twice(): void
    {
        Award::create(['year' => $this->year, 'month' => 5, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);

        $this->post(route('admin.awards.store'), [
            'year' => $this->year, 'month' => 5, 'type' => 'gold1st',
            'file' => UploadedFile::fake()->image('gold.png'),
        ])->assertStatus(422);
    }

    public function test_assigning_an_award_replaces_the_previous_holders(): void
    {
        $award = Award::create(['year' => $this->year, 'month' => 5, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);
        $one = Player::create(['year' => $this->year, 'playername' => 'One']);
        $two = Player::create(['year' => $this->year, 'playername' => 'Two']);

        $this->postJson(route('admin.awards.assign', $award), ['players' => [$one->id]])->assertOk();
        $this->assertEquals([$one->id], $award->fresh()->players->pluck('id')->all());

        $this->postJson(route('admin.awards.assign', $award), ['players' => [$two->id]])->assertOk();
        $this->assertEquals([$two->id], $award->fresh()->players->pluck('id')->all());
    }

    public function test_only_the_admin_award_may_go_to_several_players(): void
    {
        $podium = Award::create(['year' => $this->year, 'month' => 5, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);
        $adminAward = Award::create(['year' => $this->year, 'month' => 5, 'type' => 'admin', 'file' => 'x', 'mime' => 'image/png']);
        $one = Player::create(['year' => $this->year, 'playername' => 'One']);
        $two = Player::create(['year' => $this->year, 'playername' => 'Two']);

        $this->postJson(route('admin.awards.assign', $podium), ['players' => [$one->id, $two->id]])
            ->assertStatus(422);
        $this->assertCount(0, $podium->fresh()->players);

        $this->postJson(route('admin.awards.assign', $podium), ['players' => [$one->id]])->assertOk();
        $this->assertCount(1, $podium->fresh()->players);

        $this->postJson(route('admin.awards.assign', $adminAward), ['players' => [$one->id, $two->id]])
            ->assertOk();
        $this->assertCount(2, $adminAward->fresh()->players);
    }

    public function test_award_assignments_are_rebuilt_from_the_results(): void
    {
        $winner = Player::create(['year' => $this->year, 'playername' => 'Winner']);
        $second = Player::create(['year' => $this->year, 'playername' => 'Second']);

        Upload::create(['year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 3, 'playername' => 'Winner', 'position' => 1, 'points' => 36]);
        Upload::create(['year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 3, 'playername' => 'Second', 'position' => 2, 'points' => 26]);

        $first = Award::create(['year' => $this->year, 'month' => 3, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);
        $runnerUp = Award::create(['year' => $this->year, 'month' => 3, 'type' => 'gold2nd', 'file' => 'x', 'mime' => 'image/png']);

        // the legacy bug: both players on the first place award, second place empty
        $first->players()->sync([$winner->id, $second->id]);

        $this->artisan('mcup:fix-award-assignments', ['--apply' => true])->assertSuccessful();

        $this->assertSame(['Winner'], $first->fresh()->players->pluck('playername')->all());
        $this->assertSame(['Second'], $runnerUp->fresh()->players->pluck('playername')->all());
    }

    public function test_wrong_result_points_are_repaired_from_the_season_configuration(): void
    {
        $upload = Upload::create(['year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 3, 'playername' => 'Winner', 'position' => 1, 'points' => 11]);

        $this->artisan('mcup:fix-points', ['--apply' => true])->assertSuccessful();

        $this->assertSame(36, $upload->fresh()->points);
    }

    public function test_an_award_of_another_season_cannot_be_assigned(): void
    {
        $award = Award::create(['year' => $this->year, 'month' => 5, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);
        $foreign = Player::create(['year' => $this->year - 1, 'playername' => 'Foreign']);

        $this->postJson(route('admin.awards.assign', $award), ['players' => [$foreign->id]])->assertOk();

        $this->assertCount(0, $award->fresh()->players);
    }

    public function test_deleting_an_award_removes_its_assignments(): void
    {
        $award = Award::create(['year' => $this->year, 'month' => 5, 'type' => 'gold1st', 'file' => 'x', 'mime' => 'image/png']);
        $player = Player::create(['year' => $this->year, 'playername' => 'One']);
        $award->players()->attach($player);

        $this->deleteJson(route('admin.awards.destroy', $award))->assertOk();

        $this->assertDatabaseMissing('awards', ['id' => $award->id]);
        $this->assertDatabaseMissing('award_player', ['award_id' => $award->id]);
    }

    // ------------------------------------------------------------ settings

    public function test_an_admin_can_save_the_season_settings(): void
    {
        $response = $this->putJson(route('admin.settings.update'), [
            'year' => $this->year,
            'dates' => [3 => '2026-03-28 20:00:00', 4 => ''],
            'forum_links' => [3 => 'https://forum.example/3'],
            'footer' => 'Run by the orga team',
            'points' => [
                'first' => [1 => 13, 2 => 10],
                'final' => ['gold' => [1 => 40], 'silver' => [1 => 25], 'bronze' => [1 => 17]],
            ],
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        Season::forget();
        $this->assertSame('2026-03-28 20:00:00', Season::dates($this->year)[3]);
        $this->assertNull(Season::dates($this->year)[4]);
        $this->assertSame('Run by the orga team', Season::footer($this->year));
        $this->assertSame(13, Season::points($this->year)['first'][1]);
    }

    public function test_an_admin_can_start_a_new_season(): void
    {
        $next = $this->year + 1;

        $this->postJson(route('admin.seasons.store'), ['year' => $next, 'copy_from' => $this->year])
            ->assertOk()->assertJson(['success' => true]);

        Season::forget();
        $this->assertSame(12, Season::points($next)['first'][1]);
        $this->assertNull(Season::dates($next)[1]);
    }

    public function test_an_existing_season_is_not_overwritten(): void
    {
        $this->postJson(route('admin.seasons.store'), ['year' => $this->year])
            ->assertStatus(422);
    }

    // -------------------------------------------------------------- upload

    public function test_points_are_taken_from_the_season_settings(): void
    {
        // first round grants one extra point for taking part
        $this->assertSame(13, Season::pointsFor($this->year, 'firstround', '1', 1));
        $this->assertSame(36, Season::pointsFor($this->year, 'final', 'gold', 1));
    }

    public function test_a_new_cup_appears_in_the_navigation_right_away(): void
    {
        // warm the cache the navigation reads, as a page view would
        $this->assertSame([], Season::monthsWithResults($this->year));

        Upload::create([
            'year' => $this->year, 'type' => 'firstround', 'table_name' => '1',
            'month' => 8, 'playername' => 'Someone', 'position' => 1, 'points' => 13,
        ]);

        // stale cache: without invalidation the new cup stays invisible
        $this->assertSame([], Season::monthsWithResults($this->year));

        Season::forget($this->year);

        $this->assertSame([8], Season::monthsWithResults($this->year));
    }

    public function test_the_upload_endpoint_invalidates_the_season_cache(): void
    {
        Season::monthsWithResults($this->year);

        Upload::create([
            'year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 9, 'playername' => 'Someone', 'position' => 1, 'points' => 36,
        ]);

        // the endpoint refuses the duplicate table but must still drop the cache
        $this->postJson(route('admin.upload.store'), [
            'type' => 'final', 'month' => 9, 'table' => 'gold',
            'url' => 'https://pokerth.net/gamelog?pdb=deadbeef&game_id=1',
        ])->assertStatus(422);

        Season::forget($this->year);
        $this->assertContains(9, Season::monthsWithResults($this->year));
    }

    public function test_an_already_uploaded_table_is_refused(): void
    {
        Upload::create([
            'year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => 3, 'playername' => 'Someone', 'position' => 1, 'points' => 36,
        ]);

        $this->postJson(route('admin.upload.store'), [
            'type' => 'final', 'month' => 3, 'table' => 'gold',
            'url' => 'https://pokerth.net/gamelog?pdb=deadbeef&game_id=1',
        ])->assertStatus(422)->assertJsonFragment(['success' => false]);

        $this->assertSame(1, Upload::count());
    }

    public function test_an_unknown_table_is_refused(): void
    {
        $this->postJson(route('admin.upload.store'), [
            'type' => 'final', 'month' => 3, 'table' => 'platinum',
            'url' => 'https://pokerth.net/gamelog?pdb=deadbeef&game_id=1',
        ])->assertStatus(422);
    }

    public function test_the_uploaded_log_link_can_be_reconstructed_from_pdb_and_game_id(): void
    {
        $log = UploadLog::create([
            'year' => $this->year, 'type' => 'firstround', 'table_name' => '1',
            'month' => 4, 'pdb' => 'deadbeef', 'game_id' => 7,
        ]);

        $this->assertSame('https://www.pokerth.net/gamelog?pdb=deadbeef&game_id=7', $log->url);
    }

    // ---------------------------------------------------------- forum posts

    public function test_the_seeding_excludes_table_admins_from_the_random_pool(): void
    {
        $month = (int) date('n');
        Signup::create(['year' => $this->year, 'month' => $month, 'playername' => 'Jogy', 'registered_at' => now(), 'valid' => true]);
        Signup::create(['year' => $this->year, 'month' => $month, 'playername' => 'Player1', 'registered_at' => now(), 'valid' => true]);
        Signup::create(['year' => $this->year, 'month' => $month, 'playername' => 'Player2', 'registered_at' => now(), 'valid' => true]);

        $response = $this->postJson(route('admin.forum-posts.config'), [
            'year' => $this->year,
            'month' => $month,
            'admins' => ['Jogy'],
            'admin_subs' => ['boehmi'],
            'players_per_table' => 1,
            'theme_image' => '', 'cup_date_label' => '', 'seeding_time_label' => '',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $tables = $response->json('seeding_tables');
        $this->assertCount(1, $tables);
        $this->assertSame('Jogy', $tables[0]['admin']);
        $this->assertNotContains('Jogy', $tables[0]['players']);
        $this->assertContains($tables[0]['players'][0], ['Player1', 'Player2']);
    }

    public function test_the_final_seeding_groups_players_by_first_round_finishing_place(): void
    {
        $month = (int) date('n');
        Upload::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '1',
            'month' => $month, 'playername' => 'A1', 'position' => 1, 'points' => 13]);
        Upload::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '1',
            'month' => $month, 'playername' => 'A2', 'position' => 2, 'points' => 10]);
        Upload::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '2',
            'month' => $month, 'playername' => 'B1', 'position' => 1, 'points' => 13]);
        Upload::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '2',
            'month' => $month, 'playername' => 'B2', 'position' => 2, 'points' => 10]);
        UploadLog::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '1', 'month' => $month, 'pdb' => 'abc', 'game_id' => 1]);
        UploadLog::create(['year' => $this->year, 'type' => 'firstround', 'table_name' => '2', 'month' => $month, 'pdb' => 'def', 'game_id' => 1]);

        $response = $this->get(route('admin.forum-posts', ['month' => $month]));

        $response->assertOk();
        $bbcode = $response->viewData('finalSeeding');

        $goldPos = strpos($bbcode, 'Gold Table');
        $silverPos = strpos($bbcode, 'Silver Table');
        $a1Pos = strpos($bbcode, 'A1');
        $b1Pos = strpos($bbcode, 'B1');
        $a2Pos = strpos($bbcode, 'A2');

        $this->assertTrue($goldPos < $a1Pos && $a1Pos < $silverPos, 'A1 (1st place) should be in the Gold table');
        $this->assertTrue($goldPos < $b1Pos && $b1Pos < $silverPos, 'B1 (1st place) should be in the Gold table');
        $this->assertTrue($silverPos < $a2Pos, 'A2 (2nd place) should be in the Silver table');
        $this->assertStringContainsString('Table 1: https://www.pokerth.net/gamelog?pdb=abc&game_id=1', $bbcode);
    }

    public function test_the_results_post_names_the_champion(): void
    {
        $month = (int) date('n');
        Upload::create(['year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => $month, 'playername' => 'Winner', 'position' => 1, 'points' => 36]);
        Upload::create(['year' => $this->year, 'type' => 'final', 'table_name' => 'gold',
            'month' => $month, 'playername' => 'Second', 'position' => 2, 'points' => 26]);

        $response = $this->get(route('admin.forum-posts', ['month' => $month]));

        $response->assertOk();
        $this->assertStringContainsString('Congrats Champion of', $response->viewData('results'));
        $this->assertStringContainsString('Winner', $response->viewData('results'));
    }
}
