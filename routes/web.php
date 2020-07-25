<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::any("/lianxi","API\ApiController@lianxi");
Route::post("/zhuce","API\ApiController@zhuce");
Route::any("/Login","API\ApiController@Login");
Route::any("/ShowUser","API\ApiController@ShowUser")->middleware("Login");
Route::any("/phpinfo","API\ApiController@phpinfo");
Route::any("/Lists","API\ApiController@Lists");//列表
Route::any("/qiandao","API\ApiController@qiandao")->middleware("Login");//签到
Route::get("/api","API\JiaoHuController@api");


