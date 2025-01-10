<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GoogleExchangeRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\AuthCodeExchangeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        $token = $user->createToken('accessToken')->accessToken;

        $cookie = cookie('laravel_token', $token, 10080, null, null, false, true, false);

        return response()->json([
            'status' => 'success',
            'data' => null,
        ])->cookie($cookie);
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

        $cookie = cookie('laravel_token', $token, 10080, null, null, false, true, false);

        return response()->json([
            'status' => 'success',
            'data' => null,
        ])->cookie($cookie);
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
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function googleCallback(AuthCodeExchangeService $authCodeExchangeService)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Str::random(),
            ]);
        }

        $user->update([
            'google_id' => $googleUser->getId(),
        ]);

        if (! $authCode = $authCodeExchangeService->findCodeByUser($user)) {
            $authCode = $authCodeExchangeService->generateCode($user);
        }

        return redirect('http://127.0.0.1:5173/auth?code=' . $authCode->code);
    }

    public function googleExchange(GoogleExchangeRequest $request, AuthCodeExchangeService $authCodeExchangeService)
    {
        $validatedData = $request->validated();

        if (!$validatedData['code']) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'errors' => [
                        'message' => 'Invalid authentication code'
                    ]
                ]
            ], 401);
        }

        $authCode = $authCodeExchangeService->findCode($validatedData['code']);

        if (! $authCodeExchangeService->isCodeValid($authCode)) {
            return response()->json([
                'status' => 'fail',
                'data' => [
                    'errors' => [
                        'message' => 'Invalid authentication code'
                    ]
                ]
            ], 401);
        }

        $authCodeExchangeService->burnCode($authCode);

        $token = $authCode->user->createToken('accessToken')->accessToken;

        $cookie = cookie('laravel_token', $token, 10080, null, null, false, true, false);

        return response()->json([
            'status' => 'success',
            'data' => null,
        ])->cookie($cookie);
    }
}
