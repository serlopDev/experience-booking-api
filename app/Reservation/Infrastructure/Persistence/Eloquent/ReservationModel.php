<?php

namespace App\Reservation\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class ReservationModel extends Model
{
    protected $table = 'reservations';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'session_id',
        'user_id',
        'contact_email',
        'seats',
        'total_price_in_cents',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
            'total_price_in_cents' => 'integer',
        ];
    }
}
