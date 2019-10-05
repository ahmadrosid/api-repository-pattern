<?php

namespace App\Services\Users;

use App\Services\Notes\NoteTransformer;
use App\Services\Transformer;

class UserTransformer extends Transformer
{

    public $type = 'users';

    protected $availableIncludes = ['notes'];

    /**
     * @param $model UserModel
     * @return array
     */
    public function transform($model)
    {
        return [
            'id' => (int) $model->getAttribute('id'),
            'name' => $model->getAttribute('name'),
            'email' => $model->getAttribute('email'),
        ];
    }

    /**
     * @param $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeNotes($user)
    {
        return $this->collection($user->notes, new NoteTransformer(), 'notes');
    }
}
