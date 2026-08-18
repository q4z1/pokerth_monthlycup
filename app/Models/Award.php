<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Award extends Model
{
    /** Award types that belong to a whole season rather than a single cup. */
    public const SEASON_TYPES = ['admin', 'rank1st', 'rank2nd', 'rank3rd', 'top20'];

    public const TYPES = [
        'gold1st' => 'Gold 1st',
        'gold2nd' => 'Gold 2nd',
        'gold3rd' => 'Gold 3rd',
        'silver1st' => 'Silver 1st',
        'silver2nd' => 'Silver 2nd',
        'silver3rd' => 'Silver 3rd',
        'bronze1st' => 'Bronze 1st',
        'bronze2nd' => 'Bronze 2nd',
        'bronze3rd' => 'Bronze 3rd',
        'rank1st' => 'Ranking 1st',
        'rank2nd' => 'Ranking 2nd',
        'rank3rd' => 'Ranking 3rd',
        'top20' => 'Top 20',
        'admin' => 'Admin',
    ];

    protected $fillable = ['year', 'month', 'type', 'file', 'filename', 'mime'];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
    ];

    /** The image blob must never be shipped to the browser inside JSON. */
    protected $hidden = ['file'];

    protected $appends = ['label', 'url'];

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class)->withTimestamps();
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function getLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getUrlAttribute(): string
    {
        return route('award.image', ['award' => $this->id]);
    }

    public function isSeasonAward(): bool
    {
        return in_array($this->type, self::SEASON_TYPES, true);
    }
}
