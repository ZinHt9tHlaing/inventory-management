<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\StoreRequest;
use App\Http\Requests\Admin\Inventory\UpdateRequest;
use App\Http\Resources\Admin\Inventory\InventoryResource;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $Limit = (int) $request->input('limit', 5);
            $inventories = Inventory::filter([
                "search" => request("search"),
            ])->latest()
                ->paginate($Limit);

            $currentPage = $inventories->currentPage();
            $hasNextPage = $inventories->hasMorePages();

            return response()->json([
                "message" => "Inventories retrieved successfully",
                "meta" => [
                    "total" => $inventories->count(),
                    "limit" => $Limit,
                    "current_page" => $currentPage,
                    "has_next_page" => $hasNextPage,
                    "last_page" => $inventories->lastPage(),
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => InventoryResource::collection($inventories),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get inventories",
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
            $inventory = Inventory::create($request->validated());

            return response()->json([
                "message" => "Inventory created successfully",
                "data" => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to create inventory",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        try {
            return response()->json([
                "message" => "Inventory retrieved successfully",
                "data" => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get inventory",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Inventory $inventory)
    {
        try {
            $inventory->update($request->validated());
            return response()->json([
                "message" => "Inventory updated successfully",
                "data" => new InventoryResource($inventory),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update inventory",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return response()->json([
                "message" => "Inventory deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete inventory",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
