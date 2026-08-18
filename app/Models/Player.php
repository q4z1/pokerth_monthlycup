<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    protected $fillable = ['year', 'playername', 'avatar', 'avatar_mime'];

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
        return $this->avatar_mime !== null && $this->avatar_mime !== '';
    }
}
