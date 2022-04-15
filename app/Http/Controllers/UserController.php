<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new user
     */
    public function store(UserStoreRequest $request, User $user): JsonResponse
    {
        $new_user = $user->newUser($request->validated());

        return response()->created(
            "User created successfully",
            $new_user
        );
    }

    /**
     * Profile of authenticated user
     */
    public function profile(): JsonResponse
    {
        return response()->success(
            "User Profile",
            Auth::user()
        );
    }

    /**
     * User Information
     */
    public function show(User $user): JsonResponse
    {
        return response()->success(
            "User Information",
            $user
        );
    }

    /**
     * Delete User
     */
    public function delete():JsonResponse
    {
        $user = Auth::user();
        $user->delete();

        return response()->success(
            "User deleted"
        );
    }
}
