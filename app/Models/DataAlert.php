<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAlert extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_IGNORED = 'ignored';

    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = ['day', 'level', 'type', 'title', 'message', 'status'];

    protected function casts(): array
    {
        return [
            'day' => 'date',
        ];
    }
}
