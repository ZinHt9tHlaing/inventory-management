<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    /** @use HasFactory<\Database\Factories\ProductionFactory> */
    use HasUlids, HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'created_by',
        'quantity_produced',
    ];

    /**
     * Relationships that should always be loaded.
     */
    protected $with = ['product.supplier', 'warehouse', 'creator'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user who created the production.
     */
    public function creator()
    {
        // foreign key is created_by
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeFilter(Builder $query, array $filters): void
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
