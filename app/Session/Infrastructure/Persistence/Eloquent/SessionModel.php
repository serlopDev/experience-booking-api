<?php

namespace App\Session\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class SessionModel extends Model
{
    protected $table = 'sessions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'experience_id',
        'starts_at',
        'max_capacity',
        'reserved_seats',
        'price_in_cents',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'max_capacity' => 'integer',
            'reserved_seats' => 'integer',
            'price_in_cents' => 'integer',
        ];
    }
}
