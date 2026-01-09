<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function customers(Request $request)
    {
        $authUser = $request->user();

        // 🔐 Only admin allowed
        if ($authUser->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Fetch only customers
        // ❌ Exclude logged-in admin
        $customers = User::where('role', 'customer')
            ->where('id', '!=', $authUser->id)
            ->select('id', 'name', 'email', 'role')
            ->paginate(10);

        return response()->json($customers);
    }
    public function destroy(User $user)
{
    $this->authorize('delete', $user);

    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'Customer deleted successfully',
    ]);
}

}
