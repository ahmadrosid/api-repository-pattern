<?php

namespace App\Services\Users;

use App\Exceptions\InvalidCredentialException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
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
        $user = UserModel::where('email', Arr::get($data, 'email'))->first();

        if (empty($user)) {
            throw new InvalidCredentialException;
        }

        if (!Hash::check(
            Arr::get($data, 'password'),
            Arr::get($user, 'password')
        )) {
            throw new InvalidCredentialException;
        }

        $token = Str::random(32);

        $user->update([
            'access_token' => hash('sha256', $token)
        ]);

        return [
            'code' => Response::HTTP_OK,
            'access_token' => $token
        ];
    }
}
