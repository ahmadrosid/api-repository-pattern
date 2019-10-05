<?php

use App\Services\Notes\NoteModel;
use App\Services\Users\UserModel;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(UserModel::class, 50)
            ->create()
            ->each(function (UserModel $user) {
                $rand = rand(1, 12);
                for ($i = 0; $i < $rand; $i++){
                    $user->notes()->save(factory(NoteModel::class)->make());
                }
            });
    }
}
