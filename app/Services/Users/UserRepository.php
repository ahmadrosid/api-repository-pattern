<?php

namespace App\Services\Users;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    public function index()
    {
        return UserModel::paginate(5);
    }

    public function getById($id)
    {
        return UserModel::find($id);
    }

    public function register(array $data)
    {
        $user = array_merge($data, [
            'password' => Hash::make($data['password'])
        ]);

        return UserModel::create($user);
    }

    public function login(array $data)
    {
        $user = UserModel::where('email', Arr::get($data, 'email'))
            ->first();

        if (!Hash::check($data['password'], $user->password)){
            return [
                'errors' => [
                    'Invalid email or username.'
                ]
            ];
        }

        $token = Str::random(32);

        $user->update([
            'access_token' => hash('sha256', $token)
        ]);

        return [ 'access_token' => $token ];
    }
}
