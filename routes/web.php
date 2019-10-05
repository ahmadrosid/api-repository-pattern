<?php

$router->post('/users/register', 'UserController@register');
$router->post('/users/login', 'UserController@login');

$router->group(['middleware' => 'auth'], function ()
    use ($router) {
    $router->get('/users/me', 'UserController@show');

    $router->get('/notes', 'NoteController@index');
    $router->get('/notes/{id}', 'NoteController@show');
    $router->post('/notes/', 'NoteController@store');
    $router->patch('/notes/{id}', 'NoteController@update');
    $router->delete('/notes/{id}', 'NoteController@delete');

});
