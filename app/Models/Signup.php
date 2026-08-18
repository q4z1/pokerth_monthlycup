<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signup extends Model
{
    protected $fillable = [
        'year', 'month', 'playername', 'registered_at', 'ip', 'fp', 'fpnew', 'valid',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'valid' => 'boolean',
        'registered_at' => 'datetime',
    ];

    public function scopeForCup($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }
}
