<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\NotificationNasabah;
use App\Models\Notifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:200|unique:users',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                Log::warning("Validation failed for registration", [
                    'errors' => $validator->errors(),
                    'input' => $request->except(['password', 'password_confirmation']),
                ]);
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Validasi gagal',
                        'errors' => $validator->errors(),
                    ],
                    422,
                );
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'nasabah',
                'deposit_balance' => 0,
            ]);

            // Buat notifikasi untuk operator
            Notifications::create([
                'user_id' => $user->id,
                'type' => 'registrasi',
                'message' => "Nasabah baru terdaftar: {$user->name}",
                'status' => 'pending',
                'created_at' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info("User registered and notification created", [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]);

            return response()->json(
                [
                    'message' => 'Registrasi berhasil',
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
                201,
            );
        } catch (\Exception $e) {
            Log::error("Error registering user: {$e->getMessage()}", [
                'input' => $request->except(['password', 'password_confirmation']),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Registrasi gagal',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:100',
    //         'email' => 'required|string|email|max:200|unique:users',
    //         'phone' => 'required|string|max:15',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(
    //             [
    //                 'status' => false,
    //                 'message' => 'Validasi gagal',
    //                 'errors' => $validator->errors(),
    //             ],
    //             422,
    //         );
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'password' => Hash::make($request->password),
    //         'role' => 'nasabah',
    //         'deposit_balance' => 0,
    //     ]);

    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json(
    //         [
    //             'message' => 'Registrasi berhasil',
    //             'user' => $user,
    //             'access_token' => $token,
    //             'token_type' => 'Bearer',
    //         ],
    //         201,
    //     );
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Kredensial tidak valid',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
