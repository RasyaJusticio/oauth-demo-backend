<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        $token = $user->createToken('accessToken')->accessToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        if (! Auth::attempt($validatedData)) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'errors' => [
                        'message' => 'Email or password is incorrect',
                    ],
                ],
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('accessToken')->accessToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
