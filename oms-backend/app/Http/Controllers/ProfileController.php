<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class ProfileController extends Controller
{
    //
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->successResponse($user, 'User found');
    }
}
