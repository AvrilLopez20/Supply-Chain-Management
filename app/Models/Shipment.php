<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $table = 'shipments';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'from',
        'to',
        'items',
        'status',
        'eta',
        'lat',
        'lng'
    ];
}
