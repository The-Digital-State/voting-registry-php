<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

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

$router->get('hello', function (){
   return 'Hello world';
});

$router->get('poll', 'Polls@list');
$router->get('poll/{id}', 'Polls@get');

$router->get('list', 'Lists@list');
$router->put('list', 'Lists@add');

$router->get('list/{id}', 'Lists@get');
$router->put('list/{id}', 'Lists@save');
$router->delete('list/{id}', 'Lists@delete');

$router->get('migrate', 'Migrator@migrate');


