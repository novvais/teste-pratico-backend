<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request) 
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json('Email/Password is incorrect', 401);
        }

        $user = Auth::user();

        $token = $user->createToken('login_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }
}
