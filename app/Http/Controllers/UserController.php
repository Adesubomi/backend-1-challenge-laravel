<?php

namespace App\Http\Controllers;

use App\Enums\Coin;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
     * Deposit coin to vending machine account
     */
    public function deposit(DepositRequest $request): JsonResponse
    {
        if (!Gate::allows('deposit')) {
            return response()->failed(
                message: "Only a buyer is allowed to deposit",
                statusCode: 403,
                data: [],
            );
        }

        /** @var User $user */
        $user = Auth::user();
        $is_deposited = $user->depositCoin(Coin::from($request->validated('coin')));

        if (!$is_deposited) {
            return response()->failed(
                "Unable to deposit coin",
            );
        }

        return response()->success(
            message: "Coin has been deposited",
            data: []
        );
    }

    /**
     * Reset coin deposit balance
     */
    public function reset(): JsonResponse
    {
        if (!Gate::allows('reset')) {
            return response()->failed(
                message: "Only a buyer is allowed to reset coin deposit",
                statusCode: 403,
                data: [],
            );
        }

        /** @var User $user */
        $user = Auth::user();
        $is_reset = $user->resetDeposit();

        if (!$is_reset) {
            return response()->failed(
                "Unable to reset coin deposit",
            );
        }

        return response()->success(
            message: "Coin deposit has been reset",
            data: []
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
