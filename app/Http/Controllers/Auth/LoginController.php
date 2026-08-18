<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login:'.$request->ip().'|'.strtolower($credentials['username']);
        if (RateLimiter::tooManyAttempts($key, 10)) {
            throw ValidationException::withMessages([
                'username' => 'Too many login attempts. Please try again in '
                    .RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! $this->passwordMatches($user, $credentials['password'])) {
            RateLimiter::hit($key, 300);
            throw ValidationException::withMessages([
                'username' => 'Username and password combination mismatch!',
            ]);
        }

        if (! $user->active) {
            throw ValidationException::withMessages([
                'username' => 'Login not allowed!',
            ]);
        }

        RateLimiter::clear($key);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        $redirect = redirect()->intended(route('admin.dashboard'))
            ->with('status', 'Hello '.$user->displayName().'!');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirect->getTargetUrl(),
            ]);
        }

        return $redirect;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Accepts the bcrypt hashes of this application and, once, the bare MD5
     * hashes inherited from the legacy application – those are upgraded in place.
     */
    private function passwordMatches(User $user, string $password): bool
    {
        if ($user->hasLegacyPassword()) {
            if (! hash_equals(strtolower($user->getAuthPassword()), md5($password))) {
                return false;
            }

            $user->forceFill(['password' => Hash::make($password)])->save();

            return true;
        }

        return Hash::check($password, $user->getAuthPassword());
    }
}
