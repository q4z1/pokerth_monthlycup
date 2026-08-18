<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_log_in_with_a_bcrypt_password(): void
    {
        $user = User::create([
            'username' => 'admin', 'password' => 'secret123', 'active' => true,
        ]);

        $this->post('/login', ['username' => 'admin', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_legacy_md5_password_still_works_and_is_upgraded(): void
    {
        $user = User::create(['username' => 'oldadmin', 'password' => 'placeholder', 'active' => true]);
        // bypass the hashed cast so the row really holds a bare MD5, as the legacy app left it
        DB::table('users')->where('id', $user->id)->update(['password' => md5('secret123')]);
        $this->assertSame(md5('secret123'), DB::table('users')->where('id', $user->id)->value('password'));

        $this->post('/login', ['username' => 'oldadmin', 'password' => 'secret123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $stored = $user->fresh()->password;
        $this->assertNotSame(md5('secret123'), $stored);
        $this->assertTrue(Hash::check('secret123', $stored));
    }

    public function test_the_login_form_posts_as_json_and_returns_a_redirect(): void
    {
        $user = User::create(['username' => 'admin', 'password' => 'secret123', 'active' => true]);

        $this->postJson('/login', ['username' => 'admin', 'password' => 'secret123', 'remember' => false])
            ->assertOk()
            ->assertJson(['success' => true, 'redirect' => route('admin.dashboard')]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_failed_json_login_reports_the_error_on_the_username_field(): void
    {
        User::create(['username' => 'admin', 'password' => 'secret123', 'active' => true]);

        $this->postJson('/login', ['username' => 'admin', 'password' => 'wrong'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('username');

        $this->assertGuest();
    }

    public function test_a_wrong_password_is_rejected(): void
    {
        User::create(['username' => 'admin', 'password' => 'secret123', 'active' => true]);

        $this->post('/login', ['username' => 'admin', 'password' => 'wrong'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_an_inactive_user_cannot_log_in(): void
    {
        User::create(['username' => 'gone', 'password' => 'secret123', 'active' => false]);

        $this->post('/login', ['username' => 'gone', 'password' => 'secret123'])
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_admin_pages_require_authentication(): void
    {
        foreach (['/admin', '/admin/signups', '/admin/awards', '/admin/settings',
            '/admin/upload/firstround', '/admin/randomizer'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_admin_write_endpoints_require_authentication(): void
    {
        $this->postJson(route('admin.upload.store'), [])->assertUnauthorized();
        $this->postJson(route('admin.awards.store'), [])->assertUnauthorized();
    }
}
