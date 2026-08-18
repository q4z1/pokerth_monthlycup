<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['year', 'type', 'value'];

    protected $casts = ['year' => 'integer'];

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
