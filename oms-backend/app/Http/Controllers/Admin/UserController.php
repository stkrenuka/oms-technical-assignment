<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService; // ✅ CORRECT SERVICE
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function customers(Request $request)
    {
        $this->userService->ensureAdmin($request->user());

        return response()->json(
            $this->userService->getCustomers($request->user()->id)
        );
    }

    public function search(Request $request)
    {
        $this->userService->ensureAdmin($request->user());

        $request->validate([
            'search' => 'required|string|min:2',
            'per_page' => 'nullable|integer|max:20',
        ]);

        return response()->json([
            'data' => $this->userService->searchCustomers(
                $request->search,
                $request->per_page ?? 10
            ),
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->delete($user);

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ]);
    }
}
