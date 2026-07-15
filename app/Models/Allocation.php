<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $table = 'allocations';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'item',
        'qty',
        'to',
        'time'
    ];
}
