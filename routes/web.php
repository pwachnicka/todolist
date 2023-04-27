<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api', /*'middleware' => 'auth'*/], function () use ($router) {
    $router->get('login', ['uses' => 'UsersController@authenticate']);
    $router->get('tasks', ['uses' => 'TaskController@showAllTasks']);
    $router->get('tasks/{id}', ['uses' => 'TaskController@showOneTask']);
    $router->post('tasks', ['uses' => 'TaskController@create']);
    $router->put('tasks/{id}', ['uses' => 'TaskController@update']);
    $router->delete('tasks/{id}', ['uses' => 'TaskController@delete']);
});
