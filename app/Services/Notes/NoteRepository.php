<?php

namespace App\Services\Notes;

class NoteRepository
{

    public function index()
    {
        return NoteModel::paginate(5);
    }

    public function getById($id)
    {
        return NoteModel::findOrFail($id);
    }

    public function create($data)
    {
        return NoteModel::create($data);
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
