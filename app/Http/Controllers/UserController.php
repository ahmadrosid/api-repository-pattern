<?php


namespace App\Http\Controllers;

use App\Services\JsonApiAdapter;
use App\Services\Users\LoginRequest;
use App\Services\Users\LoginTransformer;
use App\Services\Users\RegisterRequest;
use App\Services\Users\UserRepository;
use App\Services\Users\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class UserController
{

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var JsonApiAdapter
     */
    private $adapter;
    /**
     * @var UserTransformer
     */
    private $transformer;

    public function __construct(
        UserRepository $repository,
        JsonApiAdapter $adapter,
        UserTransformer $transformer
    )
    {
        $this->repository = $repository;
        $this->adapter = $adapter;
        $this->transformer = $transformer;
    }

    public function show(Request $request)
    {
        return $this->adapter->setTransformer(new UserTransformer())
            ->render($request->user());
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->repository->register(
            $request->validated()
        );

        return $this->adapter
            ->setTransformer($this->transformer)
            ->setStatusCode(Response::HTTP_CREATED)
            ->render($user);
    }

    public function login(LoginRequest $request)
    {
        return $this->repository->login(
            $request->validated()
        );
    }

}
