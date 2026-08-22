<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Avatars that PokerTH has blacklisted. The list holds the MD5 of the offending
 * image; players table carries the same hash, so a cached lookup is enough.
 *
 * Background: a reported avatar is removed from the player's profile and its
 * hash lands in pokerth_ranking.avatar_blacklist. Our copy of the image is not
 * removed by that, so it has to be filtered on display.
 */
class AvatarBlacklist
{
    private const CACHE_KEY = 'avatar.blacklist';

    private const CACHE_MINUTES = 60;

    /** @return array<int,string> the blacklisted hashes */
    public static function hashes(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        try {
            return Cache::remember(self::CACHE_KEY, now()->addMinutes(self::CACHE_MINUTES), function () {
                return DB::connection('pth_ranking')
                    ->table('avatar_blacklist')
                    ->whereNotNull('avatar_hash')
                    ->where('avatar_hash', '!=', '')
                    ->pluck('avatar_hash')
                    ->all();
            });
        } catch (\Throwable $e) {
            report($e);

            // Keep filtering with the last known list rather than showing an
            // image that was taken down. Empty only when we never had one.
            return is_array($cached) ? $cached : [];
        }
    }

    public static function blocks(?string $hash): bool
    {
        return $hash !== null && $hash !== '' && in_array($hash, self::hashes(), true);
    }

    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
