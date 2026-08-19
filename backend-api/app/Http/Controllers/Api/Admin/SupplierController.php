<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Supplier\StoreRequest;
use App\Http\Requests\Admin\Supplier\UpdateRequest;
use App\Http\Resources\Admin\Supplier\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input("limit", 10);
            $suppliers = Supplier::filter([
                "search" => $request->input("search"),
            ])->latest()
                ->paginate($limit)
                ->withQueryString();

            $currentPage = $suppliers->currentPage();
            $hasNextPage = $suppliers->hasMorePages();

            return response()->json([
                "message" => "Suppliers found successfully",
                "meta" => [
                    "total" => $suppliers->count(),
                    "per_page" => $limit,
                    "current_page" => $currentPage,
                    "last_page" => $suppliers->lastPage(),
                    "has_next_page" => $hasNextPage,
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "suppliers" => SupplierResource::collection($suppliers),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get suppliers",
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
            $supplier = Supplier::create($request->validated());
            return response()->json([
                "message" => "Supplier created successfully",
                "supplier" => SupplierResource::make($supplier),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to create supplier",
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
            $supplier = Supplier::find($id);
            if (!$supplier) {
                return response()->json([
                    "message" => "Supplier not found",
                ], 404);
            }
            return response()->json([
                "message" => "Supplier found successfully",
                "supplier" => SupplierResource::make($supplier),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to find supplier",
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
            $supplier = Supplier::find($id);
            if (!$supplier) {
                return response()->json([
                    "message" => "Supplier not found",
                ], 404);
            }

            $supplier->update($request->validated());
            return response()->json([
                "message" => "Supplier updated successfully",
                "supplier" => SupplierResource::make($supplier),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update supplier",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json([
                    "message" => "Only admin can delete supplier",
                    "error" => "role_not_allowed",
                ], 403);
            }

            // before supplier deleted, related product's supplier_id will be null
            $supplier->products()->update(['supplier_id' => null]);
            $supplier->delete();
            return response()->json([
                "message" => "Supplier deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete supplier",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
