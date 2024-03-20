<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attack extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'modifiers' => 'json',
            'timestamp_started' => 'datetime',
            'timestamp_ended' => 'datetime',
            'ranked_war' => 'boolean',
        ];
    }
}
