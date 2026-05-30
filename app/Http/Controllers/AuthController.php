<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     title="WowoClean API",
 *     version="1.0.0",
 *     description="API Documentation untuk sistem WowoClean"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Login dan dapatkan token JWT",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="admin@wowoclean.com"),
     *             @OA\Property(property="password", type="string", example="admin123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login berhasil"),
     *     @OA\Response(response=401, description="Email atau password salah")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }
        $user = Auth::guard('api')->user();
        return response()->json([
            'token' => $token,
            'role'  => $user->role,
            'name'  => $user->name,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/profile",
     *     summary="Ambil data user yang sedang login",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Data user berhasil diambil"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function profile()
    {
        return response()->json(Auth::guard('api')->user(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logout dan invalidasi token",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logout berhasil")
     * )
     */
    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}