<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

$router->get('/health-check', function () {
    return 'OK';
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->put('/auth/jwt/invalidate', 'AuthController@invalidateJwt');
});

/**
 * Email Lists
 */
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

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/polls', 'PollsController@getAllPolls');
    $router->get('/poll/{id:[0-9]+}', 'PollsController@getPoll');
    $router->post('/poll/draft', 'PollsController@createDraftPoll');
    $router->put('/poll/draft/{id:[0-9]+}', 'PollsController@updateDraftPoll');
    $router->delete('/poll/draft/{id:[0-9]+}', 'PollsController@deleteDraftPoll');

    $router->put('/poll/publish/{id:[0-9]+}', 'PollsController@publishPoll');

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
