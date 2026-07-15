<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'sku',
        'name',
        'category',
        'price'
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id');
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'inventories', 'product_id', 'warehouse_id')
                    ->withPivot(['id', 'aisle', 'bin', 'qty'])
                    ->withTimestamps();
    }
}
