<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$factory->define(PasswordResetToken::class, function () {
    return [
        'token' => Hash::make(hash_hmac('sha256', Str::random(40), config('app.key'))),
        'created_at' => now(),
    ];
});
