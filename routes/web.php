<?php

use App\Http\Controllers\Admin\AwardController as AdminAwardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SignupController as AdminSignupController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/table-settings', [HomeController::class, 'tableSettings'])->name('table-settings');

Route::get('/registration', [RegistrationController::class, 'create'])->name('registration');
Route::post('/registration', [RegistrationController::class, 'store'])
    ->middleware('throttle:10,1')->name('registration.store');
Route::get('/signups', [RegistrationController::class, 'index'])->name('signups');

Route::prefix('results')->name('results.')->group(function () {
    Route::get('/', [ResultController::class, 'series'])->name('index');
    Route::get('/series', [ResultController::class, 'series'])->name('series');
    Route::get('/cup/{month?}', [ResultController::class, 'cup'])
        ->whereNumber('month')->name('cup');
    Route::get('/rankings', [ResultController::class, 'rankings'])->name('rankings');
    Route::get('/halloffame', [ResultController::class, 'hallOfFame'])->name('halloffame');
    Route::get('/points', [ResultController::class, 'points'])->name('points');
});

Route::get('/media/award/{award}', [MediaController::class, 'award'])->name('award.image');
Route::get('/media/avatar/{player}', [MediaController::class, 'avatar'])->name('player.avatar');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/signups', [AdminSignupController::class, 'index'])->name('signups');
    Route::post('/signups/{signup}/accept', [AdminSignupController::class, 'validateSignup'])->name('signups.accept');
    Route::post('/signups/{signup}/reject', [AdminSignupController::class, 'reject'])->name('signups.reject');
    Route::delete('/signups/{signup}', [AdminSignupController::class, 'destroy'])->name('signups.destroy');
    Route::get('/randomizer', [AdminSignupController::class, 'randomizer'])->name('randomizer');

    Route::get('/upload/firstround', [UploadController::class, 'firstround'])->name('upload.firstround');
    Route::get('/upload/final', [UploadController::class, 'finaltable'])->name('upload.final');
    Route::post('/upload/preview', [UploadController::class, 'preview'])->name('upload.preview');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

    Route::get('/awards', [AdminAwardController::class, 'index'])->name('awards');
    Route::post('/awards', [AdminAwardController::class, 'store'])->name('awards.store');
    Route::post('/awards/{award}', [AdminAwardController::class, 'update'])->name('awards.update');
    Route::post('/awards/{award}/assign', [AdminAwardController::class, 'assign'])->name('awards.assign');
    Route::delete('/awards/{award}', [AdminAwardController::class, 'destroy'])->name('awards.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/seasons', [SettingController::class, 'storeSeason'])->name('seasons.store');
});

/*
|--------------------------------------------------------------------------
| Redirects for the URLs of the legacy application
|--------------------------------------------------------------------------
*/
Route::redirect('/main', '/');
Route::redirect('/main/settings', '/table-settings');
Route::redirect('/main/signup', '/registration');
Route::redirect('/main/signup/show', '/signups');
Route::redirect('/main/logout', '/');
Route::redirect('/admin/signup', '/admin/signups');
Route::redirect('/admin/signup/randomizer', '/admin/randomizer');
Route::redirect('/admin/upload/firstroundtable', '/admin/upload/firstround');
Route::redirect('/admin/upload/finaltable', '/admin/upload/final');
Route::redirect('/admin/award/upload', '/admin/awards');
Route::redirect('/admin/award/edit', '/admin/awards');

Route::get('/main/results/{action?}/{month?}', [LegacyRedirectController::class, 'results']);
Route::get('/res/award', [LegacyRedirectController::class, 'award']);
Route::get('/res/avatar', [LegacyRedirectController::class, 'avatar']);
Route::get('/res/pic/{path}', [LegacyRedirectController::class, 'picture'])->where('path', '.*');

