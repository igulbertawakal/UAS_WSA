<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register
     *
     * Membuat akun baru dan memberikan token Sanctum.
     *
     * @group Authentication
     *
     * @unauthenticated
     *
     * @bodyParam name string required Nama pengguna. Example: Alesa Wahab
     * @bodyParam email string required Email unik pengguna. Example: alesa@example.com
     * @bodyParam password string required Password minimal 8 karakter. Example: password123
     * @bodyParam password_confirmation string required Konfirmasi password. Example: password123
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login
     *
     * Menghasilkan bearer token untuk mengakses endpoint yang dilindungi.
     *
     * @group Authentication
     *
     * @unauthenticated
     *
     * @bodyParam email string required Email pengguna. Example: admin@uas-wsa.test
     * @bodyParam password string required Password pengguna. Example: password
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password tidak sesuai.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout
     *
     * Menghapus token yang sedang digunakan.
     *
     * @group Authentication
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}
