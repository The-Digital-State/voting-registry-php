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

$router->get('/email-lists', 'EmailListController@getAllLists');
$router->get('/email-list/{id:[0-9]+}', 'EmailListController@getList');
$router->post('/email-list', 'EmailListController@createNewList');
$router->put('/email-list/{id:[0-9]+}', 'EmailListController@updateList');
$router->delete('/email-list/{id:[0-9]+}', 'EmailListController@deleteList');

$router->get('/polls', 'PollsController@getAllPolls');
$router->get('/poll/{id:[0-9]+}', 'PollsController@getPoll');
$router->post('/poll/draft', 'PollsController@createDraftPoll');
$router->put('/poll/draft/{id:[0-9]+}', 'PollsController@updateDraftPoll');
$router->delete('/poll/draft/{id:[0-9]+}', 'PollsController@deleteDraftPoll');
$router->put('/poll/publish/{id:[0-9]+}', 'PollsController@publishPoll');
