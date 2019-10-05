<?php


namespace App\Services\Notes;


use App\Services\Users\UserModel;
use Illuminate\Database\Eloquent\Model;

class NoteModel extends Model
{
    protected $table = 'notes';

    protected $fillable = [ 'title', 'text', 'user_id' ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
