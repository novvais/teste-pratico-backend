<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json(UserResource::collection($users), 200);
    }

    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json(new UserResource($user), 201);
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json(new UserResource($user), 200);
    }

    public function update (UpdateUserRequest $request, string $id)
    {
        $validatedData = $request->validated();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user = User::findOrFail($id);

        $user->update($validatedData);

        return response()->json(new UserResource($user), 200);
    }

    public function destroy(string $id)
    {
        $user = User::destroy($id);

        return response()->noContent();
    }
}
