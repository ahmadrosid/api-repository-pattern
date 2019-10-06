<?php

namespace App\Services\Notes;

use Illuminate\Support\Facades\Auth;

class NoteRepository
{

    public function index()
    {
        return NoteModel::paginate(15);
    }

    public function getById($id)
    {
        return NoteModel::findOrFail($id);
    }

    public function create($data)
    {
        return NoteModel::create(array_merge(
            $data, ['user_id' => Auth::user()->id]
        ));
    }

    public function delete($id)
    {
        return NoteModel::destroy($id);
    }

    public function update($id, array $data)
    {
        $note = $this->getById($id);
        $note->update($data);

        return $note;
    }

}
