<?php

namespace App\Services;

use App\Models\AuthCode;
use App\Models\User;
use Illuminate\Support\Str;

class AuthCodeExchangeService
{
    public function generateCode(User $user)
    {
        return AuthCode::create([
            'code' => Str::uuid(),
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    public function burnCode(AuthCode $authCode)
    {
        if (!$this->isCodeValid($authCode)) {
            return;
        }

        $authCode->update([
            'is_used' => true,
        ]);
    }

    public function findCode($uuid)
    {
        return AuthCode::query()->where('code', $uuid)->first();
    }

    public function findCodeByUser(User $user)
    {
        return AuthCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function isCodeValid(AuthCode $authCode)
    {
        return $authCode->expires_at->isFuture() && ! $authCode->is_used;
    }
}
