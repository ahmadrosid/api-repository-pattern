<?php


namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Validator;

abstract class RequestValidation
{

    /**
     * @var Request
     */
    private $request;

    /**
     * NoteRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public abstract function rules(): array ;

    /**
     * @return array
     * @throws ValidationException
     */
    public function validated()
    {
        $validator = Validator::make($this->request->all(), $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->request->all();
    }
}
