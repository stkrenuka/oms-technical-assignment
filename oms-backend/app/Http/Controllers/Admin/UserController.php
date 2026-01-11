<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService; // ✅ CORRECT SERVICE
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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


  public function addCustomer(Request $request): JsonResponse
{
    // 🔐 Ensure admin
    $this->userService->ensureAdmin($request->user());

    // ✅ Validate input
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role'  => 'required|in:admin,customer',
    ]);

    // ✅ Create customer via service
    $customer = $this->userService->addCustomer($validated);

    return response()->json([
        'success' => true,
        'message' => 'Customer added successfully',
        'data' => $customer
    ], 201);
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
 public function updateCustomer(Request $request, User $customer)
{
    // 🔐 Admin authorization
    $this->authorize('update', $customer);

    // ✅ Validation
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $customer->id,
        'role'  => 'required|in:admin,customer',
    ]);

    // ✅ Update via service
    $this->userService->update($customer, $validated);

    return response()->json([
        'success' => true,
        'message' => 'Customer updated successfully',
        'data'    => $customer->fresh(),
    ]);
}

}
