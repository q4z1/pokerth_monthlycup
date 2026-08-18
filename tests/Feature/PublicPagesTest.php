<?php

namespace Tests\Feature;

use App\Models\Award;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Signup;
use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    private int $year;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = (int) date('Y');
        $this->seedSeason($this->year);
    }

    public function test_home_page_shows_the_next_cup(): void
    {
        $this->get('/')->assertOk()->assertSee('home-component', false);
    }

    public function test_series_page_lists_the_gold_podium(): void
    {
        $this->get('/results/series')
            ->assertOk()
            ->assertSee('Winner')
            ->assertSee('Runner');
    }

    public function test_cup_page_shows_every_table_of_the_month(): void
    {
        $response = $this->get('/results/cup/3');

        $response->assertOk();
        // one first round table plus the gold final table
        $this->assertStringContainsString('Cup T1', $response->getContent());
        $this->assertStringContainsString('Gold', $response->getContent());
    }

    public function test_rankings_add_up_the_points_of_a_player(): void
    {
        $response = $this->get('/results/rankings');

        $response->assertOk();
        // Winner scored 13 in the first round and 36 in the gold final
        $this->assertStringContainsString('49', $response->getContent());
    }

    public function test_hall_of_fame_only_lists_players_holding_awards(): void
    {
        $response = $this->get('/results/halloffame');

        $response->assertOk();
        $this->assertStringContainsString('Winner', $response->getContent());
        $this->assertStringNotContainsString('Nobody', $response->getContent());
    }

    public function test_points_page_renders_the_configured_points(): void
    {
        $this->get('/results/points')->assertOk()->assertSee('points-component', false);
    }

    public function test_archive_year_is_honoured(): void
    {
        $this->seedSeason($this->year - 3);

        $this->get('/results/series?year='.($this->year - 3))
            ->assertOk()
            ->assertSee((string) ($this->year - 3), false);
    }

    public function test_unknown_year_falls_back_to_the_current_season(): void
    {
        $this->get('/results/series?year=1999')
            ->assertOk()
            ->assertSee((string) $this->year, false);
    }

    public function test_award_image_is_served_from_the_database(): void
    {
        $award = Award::first();

        $this->get(route('award.image', $award))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');
    }

    public function test_legacy_urls_redirect(): void
    {
        $this->get('/main/results/halloffame/?year='.$this->year)
            ->assertRedirect(route('results.halloffame', ['year' => $this->year]));

        $this->get('/main/signup/show')->assertRedirect('/signups');
    }

    public function test_legacy_award_and_picture_urls_redirect(): void
    {
        $award = Award::where('type', 'gold1st')->first();

        $this->get("/res/award?type=gold1st&month=3&year={$this->year}")
            ->assertRedirect(route('award.image', $award));

        $this->get('/res/pic/default/pth_logo.png')
            ->assertRedirect('/images/pth_logo.png');

        $this->get("/res/award?type=gold1st&month=11&year={$this->year}")->assertNotFound();
    }

    private function seedSeason(int $year): void
    {
        Setting::create([
            'year' => $year,
            'type' => 'dates',
            'value' => json_encode(collect(range(1, 12))
                ->mapWithKeys(fn ($m) => [$m => sprintf('%d-%02d-25 20:00:00', $year, $m)])->all()),
        ]);
        Setting::create([
            'year' => $year,
            'type' => 'points',
            'value' => json_encode([
                'first' => [1 => 12, 2 => 9, 3 => 7, 4 => 6, 5 => 5, 6 => 4, 7 => 3, 8 => 2, 9 => 1, 10 => 0],
                'final' => [
                    'gold' => [1 => 36, 2 => 26, 3 => 22, 4 => 17, 5 => 13, 6 => 10, 7 => 7, 8 => 5, 9 => 3, 10 => 1],
                    'silver' => [1 => 24, 2 => 18, 3 => 14, 4 => 11, 5 => 9, 6 => 7, 7 => 5, 8 => 3, 9 => 1, 10 => 1],
                    'bronze' => [1 => 16, 2 => 11, 3 => 8, 4 => 6, 5 => 5, 6 => 4, 7 => 3, 8 => 2, 9 => 1, 10 => 0],
                ],
            ]),
        ]);

        foreach (['Winner', 'Runner', 'Third', 'Nobody'] as $i => $name) {
            Player::create(['year' => $year, 'playername' => $name]);
            Upload::create([
                'year' => $year, 'type' => 'firstround', 'table_name' => '1', 'month' => 3,
                'playername' => $name, 'position' => $i + 1, 'points' => 13 - $i,
            ]);
            if ($i < 3) {
                Upload::create([
                    'year' => $year, 'type' => 'final', 'table_name' => 'gold', 'month' => 3,
                    'playername' => $name, 'position' => $i + 1, 'points' => [36, 26, 22][$i],
                ]);
            }
        }

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        foreach (['gold1st', 'gold2nd', 'gold3rd'] as $i => $type) {
            $award = Award::create([
                'year' => $year, 'month' => 3, 'type' => $type,
                'file' => $png, 'filename' => "$type.png", 'mime' => 'image/png',
            ]);
            $award->players()->attach(
                Player::where(['year' => $year, 'playername' => ['Winner', 'Runner', 'Third'][$i]])->first()
            );
        }

        Signup::create([
            'year' => $year, 'month' => (int) date('n'), 'playername' => 'Winner',
            'registered_at' => now(), 'valid' => true,
        ]);
    }
}
