<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'warehouse_id',
        'created_by',
        'shipment_number',
        'shipped_at',
    ];

    /**
     * Relationships that should always be loaded.
     */
    protected $with = ['warehouse', 'creator', 'shipmentItems.product'];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class, 'shipment_id');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = "%" . trim(strtolower($search)) . "%";

            $query->where(function ($query) use ($search) {
                $query->where('shipment_number', 'ILIKE', $search)
                    ->orWhereHas('warehouse', function (Builder $warehouseQuery) use ($search) {
                        $warehouseQuery->where('name', 'ILIKE', $search);
                    });
            });
        });
    }
}
