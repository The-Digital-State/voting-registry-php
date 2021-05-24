<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/health-check', function () {
    return 'OK';
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->put('/auth/jwt/invalidate', 'AuthController@invalidateJwt');
});

// Email Lists
$router->group(['middleware' => 'auth'], function () use ($router) {
    // list
    $router->get('email-lists', 'EmailListController@list');
    // get
    $router->get('email-lists/{id:[0-9]+}', 'EmailListController@get');
    // create
    $router->post('email-lists', 'EmailListController@create');
    // update
    $router->put('email-lists/{id:[0-9]+}', 'EmailListController@update');
    // delete
    $router->delete('email-lists/{id:[0-9]+}', 'EmailListController@delete');
});

// Polls
$router->group(['middleware' => 'auth'], function () use ($router) {
    // list
    $router->get('polls', 'PollsController@list');
    // get
    $router->get('polls/{id:[0-9]+}', 'PollsController@get');
    // create
    $router->post('polls', 'PollsController@create');
    // update
    $router->put('polls/{id:[0-9]+}/', 'PollsController@update');
    // publish
    $router->put('polls/{id:[0-9]+}/publish', 'PollsController@publish');
    // delete
    $router->delete('polls/{id:[0-9]+}', 'PollsController@delete');
});

// Voter
$router->group(['middleware'=>'auth'], function() use ($router){
    $router->post('/voter/poll/cast/{id:[0-9]+}', 'VoterController@castVote');
});

// Auth
$router->group(['prefix' => 'auth'], function () use ($router) {
    // Invitation
    $router->get('jwt/{invitationToken:[0-9a-zA-Z]{32}}', 'AuthController@getJwt');

    // Azure
    $router->post('azure', 'AuthController@loginByAzure');

    // Invalidate
    $router->post('jwt/invalidate', [
        'middleware' => 'auth',
        'uses' => 'AuthController@invalidateJwt'
    ]);
});
