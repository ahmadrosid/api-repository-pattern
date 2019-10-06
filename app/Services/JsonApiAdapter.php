<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Serializer\SerializerAbstract;
use Exception;

class JsonApiAdapter
{

    /**
     * @var Transformer
     */
    private $transformer;

    /**
     * @var string
     */
    private $url;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var SerializerAbstract
     */
    private $serializer;

    /**
     * JsonApiAdapter constructor.
     * @param Manager $fractal
     */
    public function __construct(Manager $fractal)
    {
        $this->url = url('/');
        $this->fractal = $fractal;
        $this->statusCode = 200;
    }

    /**
     * @param Transformer $transformer
     * @return $this
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        return $this->url;
    }

    /**
     * @param SerializerAbstract $serializer
     * @return $this
     */
    public function serialize(SerializerAbstract $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     */
    public function render($data)
    {
        $data = $this->serializeData($data);

        return response()->json(
            $data,
            $this->statusCode
        );
    }

    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    private function serializeData($data)
    {
        if (null == $this->transformer) {
            throw new Exception("No transformer. Please set using setTransformer");
        }

        if (null == $this->serializer) {
            $this->serializer = new JsonApiSerializer($this->baseUrl());
        }

        $this->fractal->setSerializer($this->serializer);
        $this->parseIncludeIfNeeded();

        $resource = $this->createResource($data);
        $result = $this->fractal->createData($resource)->toArray();

        return $this->appendMeta($result);
    }

    /**
     * return void
     */
    private function parseIncludeIfNeeded()
    {
        if (!empty(app('request')->query('include'))) {
            $this->fractal->parseIncludes(app('request')->query('include'));
        }
    }

    /**
     * @param $data
     * @return Collection|Item
     */
    private function createResource($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            $resource = new Collection($data, $this->transformer, $this->transformer->type);
            $paginator = new IlluminatePaginatorAdapter($data);
            $resource->setPaginator($paginator);
        } elseif ($data instanceof \Illuminate\Support\Collection) {
            $resource = new Collection($data, $this->transformer, $this->transformer->type);
        } else {
            $resource = new Item($data, $this->transformer, $this->transformer->type);
        }

        return $resource;
    }

    /**
     * @param $result
     * @return array
     */
    private function appendMeta($result)
    {
        if (isset($result['meta']['pagination'])) {
            $meta = Arr::only($result['meta']['pagination'], ['total', 'count']);
            Arr::forget($result, 'meta.pagination');
            Arr::set($result, 'meta', $meta);
        }

        return $result;
    }
}
