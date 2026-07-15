<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'routes';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'origin',
        'destination',
        'distance',
        'time',
        'cost'
    ];
}
