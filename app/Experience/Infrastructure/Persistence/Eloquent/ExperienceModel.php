<?php

namespace App\Experience\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class ExperienceModel extends Model
{
    protected $table = 'experiences';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'provider_id',
        'title',
        'description',
    ];
}
