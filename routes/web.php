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

$router->group(['prefix' => 'api/'], function () use ($router) {
    $router->group([
        'prefix' => '/auth'
    ], function () use ($router) {
        $router->post('login', 'AuthController@login');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->get('me', 'AuthController@me');
        $router->post('profile', 'AuthController@profile');

        $router->post('changepassword', 'AuthController@changePassword');
    });

    
    $router->group(['middleware' => 'auth:api'], function() use ($router) {
        $router->group([
            'prefix' => '/home'
        ], function () use ($router) {
            $router->get('year', 'HomeController@year');
            $router->get('month', 'HomeController@month');
            $router->get('get-data', 'HomeController@getMoreData');
        });

        $router->group([
            'prefix' => '/target'
        ], function() use ($router) {
            $router->get('/', 'TargetController@index');
            $router->post('/', 'TargetController@store');
            $router->delete('/{id}', 'TargetController@delete');
            $router->put('/', 'TargetController@update');
            $router->get('/simpad', 'TargetController@index_simpad');
            $router->post('/simpad', 'TargetController@store_simpad');
            $router->get('/bphtb', 'TargetController@index_bphtb');
            $router->post('/bphtb', 'TargetController@store_bphtb');
            $router->get('/pajak', 'TargetController@jenis_pajak');
        });

        $router->group([
            'prefix' => '/user'
        ], function() use ($router) {
            $router->get('/', 'UserController@index');
            $router->post('/', 'UserController@store');
            $router->put('/', 'UserController@update');
            $router->delete('/{id}', 'UserController@delete');
            $router->get('/roles','UserController@roles');
        });

        $router->group([
            'prefix' => '/jenis-pajak'
        ], function() use ($router) {
            $router->get('/', 'JenisPajakController@index');
            $router->post('/', 'JenisPajakController@store');
            $router->put('/', 'JenisPajakController@update');
            $router->delete('/{id}', 'JenisPajakController@delete');
        });

        $router->group([
            'prefix' => '/settings'
        ], function() use ($router) {
            $router->get('/', 'SettingsController@index');
        });
    });

});
