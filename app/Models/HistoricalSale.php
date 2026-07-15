<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricalSale extends Model
{
    protected $table = 'historical_sales';

    protected $fillable = [
        'order_id',
        'product_name',
        'sku',
        'category',
        'date',
        'qty',
        'revenue'
    ];
}
