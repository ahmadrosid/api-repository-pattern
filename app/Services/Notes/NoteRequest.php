<?php

namespace App\Services\Notes;

use App\Http\Requests\RequestValidation;

class NoteRequest extends RequestValidation
{
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'text' => 'required|string',
            'user_id' => 'required|integer',
        ];
    }
}
