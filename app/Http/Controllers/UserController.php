<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new user
     */
    public function store(UserStoreRequest $request, User $user)
    {
        $new_user = $user->newUser($request->validated());

        return response()->created(
            "User created successfully",
            $new_user
        );
    }
}
