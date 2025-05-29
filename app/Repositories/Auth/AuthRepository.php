<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('main')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout($user)
    {
        $user->tokens()->delete();
    }

    public function getProfile($user)
    {
        return $user;
    }

    public function updateProfile($user, array $data)
    {
        $user->update($data);
        return $user;
    }
}