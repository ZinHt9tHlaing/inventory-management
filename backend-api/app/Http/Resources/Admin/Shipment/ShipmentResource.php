<?php

namespace App\Http\Resources\Admin\Shipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "warehouse" => $this->whenLoaded("warehouse", fn($warehouse) => [
                "id" => $warehouse->id,
                "name" => $warehouse->name,
                "location" => $warehouse->location,
            ]),
            "creator" => $this->whenLoaded("creator", fn($creator) => [
                "id" => $creator->id,
                "name" => $creator->name,
                "email" => $creator->email,
                "email_verified_at" => $creator->email_verified_at,
                "role" => $creator->role,
            ]),
            // "shipmentItems"=> ShipmentItemResource::collection($this->whenLoaded("shipmentItems")),
            "shipmentItems" => $this->whenLoaded("shipmentItems"),
            "shipment_number" => $this->shipment_number,
            "shipped_at" => $this->shipped_at?->format("Y-m-d"),
            "created_at" => $this->created_at?->format("Y-m-d H:i:s"),
            "updated_at" => $this->updated_at?->format("Y-m-d H:i:s"),
        ];
    }
}
