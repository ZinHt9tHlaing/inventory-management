<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Resources\Admin\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input("limit", 10);
            $users = User::filter([
                "search" => $request->input("search"),
            ])->latest()->paginate($limit)->withQueryString();

            $currentPage = $users->currentPage();
            $hasNextPage = $users->hasMorePages();

            $users = UserResource::collection($users);
            return response()->json([
                "message" => "Success to get users",
                "meta" => [
                    "total" => $users->count(),
                    "per_page" => $limit,
                    "current_page" => $currentPage,
                    "has_next_page" => $hasNextPage,
                    "next_page" => $hasNextPage ? $currentPage + 1 : null,
                    "prev_page" => $currentPage > 1 ? $currentPage - 1 : null,
                ],
                "data" => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to get users",
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
            $user = User::create($request->validated());
            return response()->json([
                "message" => "Success to create users",
                "data" => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to create users",
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
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

            return response()->json([
                "message" => "Success to show user",
                "data" => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to show user",
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
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

            $user->update($request->validated());
            return response()->json([
                "message" => "Success to update user",
                "data" => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to update users",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

            $user->delete();
            return response()->json([
                "message" => "Success to delete user",
                "data" => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to delete user",
                "error" => $e->getMessage(),
            ], 500);
        }
    }
}
