<?php

namespace App\Models;

use App\Services\AvatarBlacklist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    protected $fillable = ['year', 'playername', 'avatar', 'avatar_mime', 'avatar_hash'];

    protected $casts = ['year' => 'integer'];

    /** The avatar blob must never be shipped to the browser inside JSON. */
    protected $hidden = ['avatar'];

    public function awards(): BelongsToMany
    {
        return $this->belongsToMany(Award::class)->withTimestamps();
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function hasAvatar(): bool
    {
        if ($this->avatar_mime === null || $this->avatar_mime === '') {
            return false;
        }

        // An avatar reported and blacklisted at PokerTH must not be shown here
        // either, even though our cached copy still exists.
        return ! AvatarBlacklist::blocks($this->avatar_hash);
    }
}
