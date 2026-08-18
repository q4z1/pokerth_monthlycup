<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Signup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private int $year;

    private int $month;

    protected function setUp(): void
    {
        parent::setUp();

        $this->year = (int) date('Y');
        $this->month = (int) date('n');
    }

    public function test_a_player_can_register_while_the_cup_is_open(): void
    {
        $this->scheduleCup(now()->addDays(3));

        $this->postJson(route('registration.store'), ['playername' => 'NewPlayer'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('signups', [
            'year' => $this->year,
            'month' => $this->month,
            'playername' => 'NewPlayer',
            'valid' => false,
        ]);
    }

    public function test_the_same_player_cannot_register_twice(): void
    {
        $this->scheduleCup(now()->addDays(3));

        $this->postJson(route('registration.store'), ['playername' => 'NewPlayer'])->assertOk();
        $this->postJson(route('registration.store'), ['playername' => 'NewPlayer'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('playername');

        $this->assertSame(1, Signup::count());
    }

    public function test_registration_is_rejected_once_the_cup_has_started(): void
    {
        $this->scheduleCup(now()->subMinute());

        $this->postJson(route('registration.store'), ['playername' => 'TooLate'])
            ->assertStatus(422);

        $this->assertSame(0, Signup::count());
    }

    public function test_registration_closes_an_hour_before_the_cup(): void
    {
        $this->scheduleCup(now()->addMinutes(30));

        $this->postJson(route('registration.store'), ['playername' => 'TooLate'])
            ->assertStatus(422);
    }

    public function test_the_signup_list_splits_players_and_substitutes(): void
    {
        $this->scheduleCup(now()->addDays(3));
        config(['mcup.seats' => 2]);

        foreach (['A', 'B', 'C'] as $i => $name) {
            Signup::create([
                'year' => $this->year, 'month' => $this->month, 'playername' => $name,
                'registered_at' => now()->addSeconds($i), 'valid' => true,
            ]);
        }
        // Pending signups must not show up publicly.
        Signup::create([
            'year' => $this->year, 'month' => $this->month, 'playername' => 'Pending',
            'registered_at' => now(), 'valid' => false,
        ]);

        $response = $this->get(route('signups'));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('C', $content);
        $this->assertStringNotContainsString('Pending', $content);
    }

    public function test_pending_signups_are_counted_but_never_named_publicly(): void
    {
        $this->scheduleCup(now()->addDays(3));

        Signup::create([
            'year' => $this->year, 'month' => $this->month, 'playername' => 'WaitingGuy',
            'registered_at' => now(), 'valid' => false,
        ]);

        $response = $this->get(route('signups'));

        $response->assertOk();
        // the count is public, the name is not
        $this->assertStringContainsString(':pending="1"', $response->getContent());
        $this->assertStringNotContainsString('WaitingGuy', $response->getContent());
    }

    private function scheduleCup(\DateTimeInterface $date): void
    {
        Setting::create([
            'year' => $this->year,
            'type' => 'dates',
            'value' => json_encode([$this->month => $date->format('Y-m-d H:i:s')]),
        ]);
    }
}
