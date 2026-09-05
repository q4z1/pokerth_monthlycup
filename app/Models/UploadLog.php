<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** The pokerth.net game log a table upload was parsed from. */
class UploadLog extends Model
{
    protected $fillable = ['year', 'type', 'table_name', 'month', 'pdb', 'game_id'];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'game_id' => 'integer',
    ];

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function getUrlAttribute(): string
    {
        return "https://www.pokerth.net/gamelog?pdb={$this->pdb}&game_id={$this->game_id}";
    }
}
