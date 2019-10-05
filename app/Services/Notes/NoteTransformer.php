<?php


namespace App\Services\Notes;

use App\Services\Transformer;
use App\Services\Users\UserTransformer;

class NoteTransformer extends Transformer
{

    public $type = 'notes';

    protected $availableIncludes = ['users'];

    /**
     * @param $model NoteModel
     * @return array
     */
    public function transform($model)
    {
        return [
            'id' => $model->getAttribute('id'),
            'title' => $model->getAttribute('title'),
            'text' => $model->getAttribute('text'),
            'created_at' => $model->getAttribute('created_at'),
            'updated_at' => $model->getAttribute('updated_at'),
        ];
    }

    /**
     * @param $note
     * @return \League\Fractal\Resource\Item
     */
    public function includeUsers($note)
    {
        return $this->item($note->user, new UserTransformer(), 'users');
    }
}
