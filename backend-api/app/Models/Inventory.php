<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /** @use HasFactory<\Database\Factories\InventoryFactory> */
    use HasUlids, HasFactory;

    protected $fillable = [
        "product_id",
        "warehouse_id",
        "quantity",
    ];

    /**
     * Relationships that should always be loaded.
     */
    protected $with = ['product.supplier', 'warehouse'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $search = "%" . trim(strtolower($search)) . "%";

            $query->where(function ($query) use ($search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'ILIKE', $search);
                })->orWhereHas('warehouse', function (Builder $warehouseQuery) use ($search) {
                    $warehouseQuery->where('name', 'ILIKE', $search);
                });
            });
        });
    }
}
