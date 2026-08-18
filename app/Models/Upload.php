<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    public const FINAL_TABLES = ['gold' => 'Gold', 'silver' => 'Silver', 'bronze' => 'Bronze'];

    protected $fillable = [
        'year', 'type', 'table_name', 'month', 'playername', 'position', 'points',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'position' => 'integer',
        'points' => 'integer',
    ];

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
