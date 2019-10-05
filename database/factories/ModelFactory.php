<?php

use App\Services\Notes\NoteModel;
use App\Services\Users\UserModel;
use Illuminate\Support\Facades\Hash;

$factory->define(UserModel::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => Hash::make('secret')
    ];
});

$factory->define(NoteModel::class, function (Faker\Generator $faker) {
    return [
        'title' => ucfirst(implode(" ", $faker->words)),
        'text' => $faker->text,
    ];
});
