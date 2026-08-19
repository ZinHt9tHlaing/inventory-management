<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreRequest;
use App\Http\Requests\Admin\Product\UpdateRequest;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $Limit = $request->input('limit', 5);
            $products = Product::with(['supplier'])
                ->where('is_deleted', false)
                ->filter([
                    "search" => request("search"),
                    "min_price" => request("min_price"),
                    "max_price" => request("max_price"),
                ])->latest()->paginate($Limit);

            $currentPage = $products->currentPage();
            $hasNextPage = $products->hasMorePages();

            return response()->json([
                "message" => "Products found successfully",
                "meta" => [
                    "total" => $products->count(),
                    "limit" => $Limit,
                    "current_page" => $currentPage,
                    "has_next_page" => $hasNextPage,
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => ProductResource::collection($products),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get products",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $product = Product::create($request->validated());

            return response()->json([
                "message" => "Product updated successfully",
                "data" => new ProductResource($product),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $product = Product::with(['supplier'])
                ->where('is_deleted', false)->find($id);
            if (!$product) {
                return response()->json([
                    "message" => "Product not found",
                ], 404);
            }
            return response()->json([
                "message" => "Product found successfully",
                "data" => ProductResource::make($product),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateRequest $request, string $id)
    {
        try {
            $product = Product::with(['supplier'])
                ->where('is_deleted', false)->find($id);
            if (!$product) {
                return response()->json([
                    "message" => "Product not found",
                ], 404);
            }

            $product->update($request->validated());
            $product->save();

            return response()->json([
                "message" => "Product updated successfully",
                "data" => ProductResource::make($product),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                return response()->json([
                    "message" => "Only admin can delete warehouse",
                    "error" => "role_not_allowed",
                ], 403);
            }

            $product->update([
                "is_deleted" => true,
                "updated_at" => now(),
            ]);
            return response()->json([
                "message" => "Product deleted successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete product",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
