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
        $router->get('/laporan', 'RekamMedisController@laporan');

        $router->post('login', 'AuthController@login');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->get('me', 'AuthController@me');
        $router->post('profile', 'AuthController@profile');

        $router->post('changepassword', 'AuthController@changePassword');

        $router->post('/register', 'UserController@store');

    });

    $router->group([
        'prefix' => '/home'
    ], function () use ($router) {
        $router->get('year', 'HomeController@new_year');
        $router->get('new-year', 'HomeController@year');
        $router->get('new-month', 'HomeController@month');
        $router->get('month', 'HomeController@new_month');
        $router->get('get-data', 'HomeController@getMoreData');
        $router->get('settings', 'SettingsController@index');
        $router->get('load', 'HomeController@load_data');
        
        
    });

    // $router->group([
    //     'prefix' => '/realisasi-pendapatan'
    // ], function () use ($router) {
    //     $router->get('/', 'RealisasiPendapatan@index');
    // });

    $router->get('/realisasi-pendapatan', 'RealisasiPendapatan@index');
    $router->get('/realisasi-perhari','RealisasiPendapatan@per_hari');

    $router->get('laporan/dashboard', 'LaporanController@bulanIni');

    
    $router->group(['middleware' => 'auth:api'], function() use ($router) {
        

        $router->group([
            'prefix' => '/laporan'
        ], function() use ($router) {
            $router->get('/', 'LaporanController@index');
            $router->post('/', 'LaporanController@store');
            $router->put('/', 'LaporanController@update');
            $router->delete('/{id}', 'LaporanController@delete');
            $router->post('/status', 'LaporanController@status');
            $router->post('/asuransi', 'LaporanController@asuransi');
            $router->get('/laporan-saya','LaporanController@laporanSaya');
            $router->get('/informasi/{id}','LaporanController@informasiStatus');
            $router->put('/nopol', 'LaporanController@updateNopol');
            $router->post('/kwitansi','LaporanController@updateKwitansi');
            $router->post('/perincian','LaporanController@updatePerincian');
            $router->post('/polisi','LaporanController@updatePolisi');
            $router->post('/garansi','LaporanController@updateGaransi');
            
            
            
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
            $router->post('/update', 'UserController@editData');
            $router->delete('/{id}', 'UserController@delete');
            $router->get('/roles','UserController@roles');
            $router->post('/upload', 'UserController@uploadPicture');

        });

        

        $router->group([
            'prefix' => '/settings'
        ], function() use ($router) {
            $router->get('/', 'SettingsController@index');
            $router->put('/', 'SettingsController@update');
            $router->post('/upload', 'SettingsController@uploadLogo');
            $router->get('/provinsi', 'SettingsController@getProvinsi');
            $router->get('/kota/{id}','SettingsController@getKota');
        });

        $router->group([
            'prefix' => '/rekam-medis'
        ], function() use ($router) {
            $router->get('/', 'RekamMedisController@index');
            $router->get('/rm', 'RekamMedisController@get_rm');
            $router->put('/', 'RekamMedisController@update');
            $router->post('/', 'RekamMedisController@store');
            $router->get('/search','RekamMedisController@search');
            $router->post('/insert', 'RekamMedisController@insert');
            $router->post('/dokumen', 'RekamMedisController@dokumen');
            $router->get('/testing', 'RekamMedisController@testing');




        });


    });

});
