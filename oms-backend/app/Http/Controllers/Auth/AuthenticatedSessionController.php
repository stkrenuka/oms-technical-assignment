<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * SPA Login (Sanctum session-based)
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // 🔐 Attempt login
        if (! Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        // 🔁 Regenerate session (important)
        $request->session()->regenerate();

        $user = $request->user();

        // ✅ Return role to frontend
        return response()->json([
            'success' => true,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role, // admin | customer
            ],
        ]);
    }
   public function register(Request $request): JsonResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:4|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'customer',
    ]);

    // ✅ AUTO LOGIN (IMPORTANT)
    Auth::login($user);
    $request->session()->regenerate();

    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ], 201);
}


    /**
     * SPA Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            activity()
                ->performedOn($user)
                ->causedBy($user)
                ->event('logout')
                ->withProperties([
                    'role' => $user->role,
                    'ip'   => $request->ip(),
                ])
                ->log($user->name . ' logged out');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }
}
