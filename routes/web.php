<?php

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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\V1'], function($api){

    $api->group(['middleware' => ['checkuserauth']], function($api){
        
        //测试路由
        $api->post('/addforder', ['as'=>'vr.netspace.addforder', 'uses'=>'NetspaceController@AddForder']);

        $api->post('/renameforder/{fileid}', ['as'=>'vr.netspace.renameforder', 'uses'=>'NetspaceController@RenameForder']);

        $api->delete('/delete/{fileid}', ['as'=>'vr.netspace.delete', 'uses'=>'NetspaceController@DeleteForderFiles']);

        $api->post('/upload', ['as'=>'vr.netspace.uploadfile', 'uses'=>'NetspaceController@AddFile']);

        $api->get('/myfiles', ['as'=>'vr.netspace.getfiles', 'uses'=>'NetspaceController@GetFiles']);

        $api->get('/download/{fileid}', ['as'=>'vr.netspace.downloadfile', 'uses'=>'NetspaceController@DownloadFile']);

    });


});