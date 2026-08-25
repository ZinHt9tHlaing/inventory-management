<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Production\StoreRequest;
use App\Http\Resources\Admin\Production\ProductionResource;
use App\Models\Inventory;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->input("limit", 5);
            $production = Production::filter([
                "search" => $request->input("search") ?? "",
            ])
                ->latest()
                ->paginate($limit);

            $currentPage = $production->currentPage();
            $hasNextPage = $production->hasMorePages();

            return response()->json([
                "message" => "Productions retrieved successfully",
                "meta" => [
                    "total" => $production->count(),
                    "limit" => $limit,
                    "current_page" => $currentPage,
                    "has_next_page" => $hasNextPage,
                    "last_page" => $production->lastPage(),
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => ProductionResource::collection($production),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get productions",
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
            $data['created_by'] = Auth::id();

            $production = Production::firstOrNew([
                "product_id"   => $data["product_id"],
                "warehouse_id" => $data["warehouse_id"],
            ], $data);

            // Check if not the production already exists, create a new production
            $isNew = !$production->exists;
            if ($isNew) {
                $production->save();
            }

            // Update the quantity in the inventory
            Inventory::firstOrCreate(
                [
                    "product_id" => $data["product_id"],
                    "warehouse_id" => $data["warehouse_id"]
                ],
                ["quantity" => 0]
            )->increment("quantity", $data["quantity_produced"]);

            return response()->json([
                "message" => $isNew ? "Production created successfully" : "Inventory quantity updated successfully",
                "data"    => ProductionResource::make($production),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to create production",
                "error"   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Production $production)
    {
        try {
            return response()->json([
                "message" => "Production retrieved successfully",
                "data" => ProductionResource::make($production),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get production",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Production $production)
    {
        try {
            $production->delete();

            return response()->json([
                "message" => "Production deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete production",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
