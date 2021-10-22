<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailsListController;
use App\Http\Controllers\PollController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Auth
Route::group(['middleware' => ['api'], 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('invitation', [AuthController::class, 'loginByInvitation']);
    Route::post('azure', [AuthController::class, 'loginByAzure']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('me', [AuthController::class, 'me']);
    });
});

// EmailsList
Route::group(['middleware' => ['api', 'auth:api'], 'prefix' => 'emails-lists'], function () {
    Route::get('/', [EmailsListController::class, 'list']);
    Route::get('{id}', [EmailsListController::class, 'get']);
    Route::post('/', [EmailsListController::class, 'create']);
    Route::put('{id}', [EmailsListController::class, 'update']);
    Route::delete('{id}', [EmailsListController::class, 'delete']);
});

// Poll
Route::group(['middleware' => ['api'], 'prefix' => 'polls'], function () {
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/', [PollController::class, 'list']);
        Route::get('{id}', [PollController::class, 'get']);
        Route::post('/', [PollController::class, 'create']);
        Route::put('{id}', [PollController::class, 'update']);
        Route::delete('{id}', [PollController::class, 'delete']);
        Route::get('{id}/can-vote', [PollController::class, 'canVote']);
        Route::post('{id}/vote', [PollController::class, 'vote']);
    });

    Route::get('{id}/view', [PollController::class, 'view']);
    Route::get('{id}/results/{token}', [PollController::class, 'result']);
    Route::get('{id}/results', [PollController::class, 'results']);
    Route::get('{id}/statistic', [PollController::class, 'statistic']);
});
