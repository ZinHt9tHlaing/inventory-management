<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /** @use HasFactory<\Database\Factories\WarehouseFactory> */
    use HasUlids, HasFactory;

    protected $fillable = [
        'name',
        'location',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * Get all productions in the warehouse.
     */
    public function productions()
    {
        return $this->hasMany(Production::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'warehouse_id');
    }

    /**
     * Scope for filtering warehouses.
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters["search"], function ($query, $search) {
            $query->where("name", "like", "%{$search}%")->orWhere("location", "like", "%{$search}%");
        });
    }
}
