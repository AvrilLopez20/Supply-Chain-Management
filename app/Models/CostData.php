<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostData extends Model
{
    protected $table = 'cost_data';

    protected $fillable = [
        'category',
        'spending',
        'budget',
        'variance'
    ];
}
