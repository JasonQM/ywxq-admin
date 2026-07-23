<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerActionLog extends Model
{
    public const ACTION_BAN = '封号';

    public const ACTION_UNBAN = '解封';

    protected $fillable = [
        'uid',
        'action',
        'remark',
        'operated_at',
    ];

    protected function casts(): array
    {
        return [
            'operated_at' => 'datetime',
        ];
    }
}
