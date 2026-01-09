<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\RegisterUserService;

class AuthenticatedSessionController extends Controller
{
    /**
     * SPA Login (Sanctum session-based)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        // 🔑 Create token
        $token = $user->createToken('spa-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

public function register(Request $request, RegisterUserService $service)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:4|confirmed',
    ]);

    $user = $service->handle($validated);

    $token = $user->createToken('spa-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => $user,
    ], 201);
}




    /**
     * SPA Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true
        ]);
    }

}
