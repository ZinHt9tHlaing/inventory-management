<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warehouse\StoreRequest;
use App\Http\Requests\Admin\Warehouse\UpdateRequest;
use App\Http\Resources\Admin\Warehouse\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input("limit", 10);
            $warehouses = Warehouse::filter([
                "search" => $request->input("search"),
            ])->latest()
                ->paginate($limit)
                ->withQueryString();

            $currentPage = $warehouses->currentPage();
            $hasNextPage = $warehouses->hasMorePages();

            return response()->json([
                "message" => "Warehouses found successfully",
                "meta" => [
                    "total" => $warehouses->count(),
                    "per_page" => $limit,
                    "current_page" => $currentPage,
                    "last_page" => $warehouses->lastPage(),
                    "has_next_page" => $hasNextPage,
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => WarehouseResource::collection($warehouses),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get warehouses",
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
            $warehouse = Warehouse::create($request->validated());
            return response()->json([
                "message" => "Warehouse created successfully",
                "data" => new WarehouseResource($warehouse),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to create warehouse",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $warehouse = Warehouse::find($id);
            if (!$warehouse) {
                return response()->json([
                    "message" => "Warehouse not found",
                    "error" => "Warehouse not found",
                ], 404);
            }

            return response()->json([
                "message" => "Warehouse found successfully",
                "data" => new WarehouseResource($warehouse),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get warehouse",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            $warehouse = Warehouse::find($id);
            if (!$warehouse) {
                return response()->json([
                    "message" => "Warehouse not found",
                    "error" => "Warehouse not found",
                ], 404);
            }

            $warehouse->update($request->validated());
            return response()->json([
                "message" => "Warehouse updated successfully",
                "data" => new WarehouseResource($warehouse),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update warehouse",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json([
                    "message" => "Only admin can delete warehouse",
                    "error" => "role_not_allowed",
                ], 403);
            }

            $warehouse->delete();
            return response()->json([
                "message" => "Warehouse deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete warehouse",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}