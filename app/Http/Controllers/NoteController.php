<?php

namespace App\Http\Controllers;

use App\Services\JsonApiAdapter;
use App\Services\Notes\NoteRepository;
use App\Services\Notes\NoteRequest;
use App\Services\Notes\NoteTransformer;
use Illuminate\Http\Response;

class NoteController
{

    /**
     * @var NoteRepository
     */
    private $repository;

    /**
     * @var JsonApiAdapter
     */
    private $adapter;
    /**
     * @var NoteTransformer
     */
    private $transformer;

    public function __construct(
        NoteRepository $repository,
        JsonApiAdapter $adapter,
        NoteTransformer $transformer
    )
    {
        $this->repository = $repository;
        $this->adapter = $adapter;
        $this->transformer = $transformer;
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        $notes = $this->repository->index();

        return $this->adapter
            ->setTransformer($this->transformer)
            ->render($notes);
    }

    public function show($id)
    {
        $note = $this->repository->getById($id);

        return $this->adapter
            ->setTransformer($this->transformer)
            ->render($note);
    }

    public function store(NoteRequest $request)
    {
        $note = $this->repository->create(
            $request->validated()
        );

        return $this->adapter
            ->setTransformer($this->transformer)
            ->setStatusCode(Response::HTTP_CREATED)
            ->render($note);
    }

    public function update($id, NoteRequest $request)
    {
        $note = $this->repository->update(
            $id,
            $request->validated()
        );

        return $this->adapter
            ->setTransformer($this->transformer)
            ->setStatusCode(Response::HTTP_ACCEPTED)
            ->render($note);
    }

    public function delete($id)
    {
        $this->repository->delete($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
