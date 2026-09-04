<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipment\StoreRequest;
use App\Http\Resources\Admin\Shipment\ShipmentResource;
use App\Models\Inventory;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input("limit", 5);
            $shipment = Shipment::filter([
                "search" => $request->input("search") ?? "",
            ])
                ->latest()
                ->paginate($limit);

            $currentPage = $shipment->currentPage();
            $hasNextPage = $shipment->hasMorePages();

            return response()->json([
                "message" => "Shipments retrieved successfully",
                "meta" => [
                    "total" => $shipment->count(),
                    "limit" => $limit,
                    "current_page" => $currentPage,
                    "has_next_page" => $hasNextPage,
                    "last_page" => $shipment->lastPage(),
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => ShipmentResource::collection($shipment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get shipments",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->validated();

            $shipment = DB::transaction(function () use ($data) {
                // Check inventory stock
                foreach ($data['shipment_items'] as $item) {
                    $inventory = Inventory::where("product_id", $item["product_id"])
                        ->where("warehouse_id", $data["warehouse_id"])
                        ->first();

                    if (!$inventory) {
                        throw new \Exception("No inventory record found for product: {$item['product_id']} in this warehouse", 422);
                    }

                    if ($inventory->quantity < (int) $item["quantity"]) {
                        throw new \Exception("Insufficient inventory for product: {$item['product_id']}. Available: {$inventory->quantity}, Requested: {$item['quantity']}", 422);
                    }
                }

                // create shipment
                $shipment = Shipment::create([
                    "warehouse_id" => $data["warehouse_id"],
                    "shipment_number" => $data["shipment_number"],
                    "shipped_at" => $data["shipped_at"],
                    "created_by" => Auth::id(),
                ]);

                // create shipment item and decrease inventory
                foreach ($data['shipment_items'] as $item) {
                    $shipment->shipmentItems()->create([
                        "product_id" => $item["product_id"],
                        "quantity" => $item["quantity"],
                    ]);

                    $inventory = Inventory::where("product_id", $item["product_id"])
                        ->where("warehouse_id", $data["warehouse_id"])->first();

                    $inventory->quantity -= $item["quantity"];
                    $inventory->save();
                    // $inventory->decrement("quantity", $item["quantity"]);
                }

                return $shipment;
            });

            return response()->json([
                "message" => "Shipment created and Inventory updated successfully",
                "data" => ShipmentResource::make($shipment),
            ], 201);
        } catch (\Exception $e) {
            $statusCode = $e->getCode() === 422 ? 422 : 500;
            $message = $statusCode === 422 ? $e->getMessage() : "Failed to create shipment";

            return response()->json([
                "message" => $message,
                "error" => $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        try {
            return response()->json([
                "message" => "Shipment retrieved successfully",
                "data" => ShipmentResource::make($shipment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get shipment",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        try {
            $shipment->delete();

            return response()->json([
                "message" => "Shipment deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete shipment",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
