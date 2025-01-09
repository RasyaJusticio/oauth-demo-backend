<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        $token = $user->createToken('accessToken')->accessToken;

        return response()->json([
            'status' => 'success',
            'data' => null,
        ])->cookie('laravel_token', $token, 10080, null, null, false, true);
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
            'data' => null
        ])->cookie('laravel_token', $token, 10080, null, null, false, true);
    }

    public function logout()
    {
        Auth::user()->token()->revoke();

        return response()->json([
            'status' => 'success',
            'data' => null,
        ])->withoutCookie('laravel_token');
    }

    // Google
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'password' => Str::random()
        ]);

        $token = $user->createToken('accessToken')->accessToken;

        // TODO: Redirect to the frontend with the cookie containing the token.
        return response()->json([
            'status' => 'success',
            'data' => null
        ])->cookie('laravel_token', $token, 10080, null, null, false, true);
    }
}
