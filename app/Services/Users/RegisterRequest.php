<?php


namespace App\Services\Users;


use App\Http\Requests\RequestValidation;

class RegisterRequest extends RequestValidation
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|min:6'
        ];
    }
}
