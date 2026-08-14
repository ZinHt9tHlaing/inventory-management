<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where("email", $data["email"])->first();
            if ($user) {
                return response()->json([
                    "message" => "User already exists",
                ], 409);
            }

            $user = User::create([
                "name" => $data["name"],
                "email" => $data["email"],
                "password" => Hash::make($data["password"]),
                "role" => $data["role"],
            ]);

            return response()->json([
                "message" => "User created successfully",
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                "message" => "Failed to register",
                "error" => $error->getMessage(),
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where("email", $data["email"])->first();
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

            if (!Hash::check($data["password"], $user->password)) {
                return response()->json([
                    "message" => "Invalid credentials",
                ], 401);
            }


            $token = $user->createToken("admin-token")->plainTextToken;

            $isSecure = app()->environment('production');

            $cookie = cookie(
                'access_token', // Cookie name
                $token,         // Token value
                60 * 24,        // expire time (1 day)
                '/',            // Path
                null,           // Domain
                $isSecure,      // Secure (only works on HTTPS)
                true            // HttpOnly (Cannot be used from JavaScript.)
            );

            return response()->json([
                "message" => "User logged in successfully",
                "token" => $token,
            ], 200)->withCookie($cookie);
        } catch (\Exception $error) {
            return response()->json([
                "message" => "Failed to login",
                "error" => $error->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

             // Revoke current user token only
            /** @var \Laravel\Sanctum\PersonalAccessToken $token */
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
                cookie()->forget("access_token");
            }

            return response()->json([
                "message" => "User logged out successfully",
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                "message" => "Failed to logout",
                "error" => $error->getMessage(),
            ], 500);
        }
    }


    public function me()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    "message" => "User not found",
                ], 404);
            }

            return response()->json([
                "message" => "User info retrieved successfully",
                "user" => $user,
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                "message" => "Failed to get user info",
                "error" => $error->getMessage(),
            ], 500);
        }
    }
}
