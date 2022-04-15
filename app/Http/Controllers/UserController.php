<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
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
            message: "User created successfully",
            data: [
                "user" => $new_user,
            ],
        );
    }

    /**
     * Profile of authenticated user
     */
    public function profile(): JsonResponse
    {
        return response()->success(
            message: "User Profile",
            data: [
                "user" => Auth::user(),
            ],
        );
    }

    /**
     * User Information
     */
    public function show(User $user): JsonResponse
    {
        return response()->success(
            message: "User Information",
            data: [
                "user" => $user,
            ],
        );
    }

    /**
     * Update User
     */
    public function update(UserUpdateRequest $request):JsonResponse
    {
        $user = Auth::user();
        $user->updateUser($request->validated());

        return response()->success(
            message: "User updated",
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
            message: "User deleted",
        );
    }
}
