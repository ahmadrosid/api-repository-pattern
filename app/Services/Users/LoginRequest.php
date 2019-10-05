<?php


namespace App\Services\Users;


use App\Http\Requests\RequestValidation;

class LoginRequest extends RequestValidation
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required',
            'password' => 'required'
        ];
    }
}
