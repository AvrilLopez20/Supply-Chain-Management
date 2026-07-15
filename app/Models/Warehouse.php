<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'capacity',
        'status'
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'inventories', 'warehouse_id', 'product_id')
                    ->withPivot(['id', 'aisle', 'bin', 'qty'])
                    ->withTimestamps();
    }
}
