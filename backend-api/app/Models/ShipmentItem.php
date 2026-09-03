<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentItemFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'shipment_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
