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

Route::get('/lists','ListsController@lists');

Route::prefix('/web')->middleware('check.login')->group(function(){
    Route::get('/login','User\UserController@loginView');       // 登录
    Route::post('/login','User\UserController@login');       // 登录
    Route::get('/logout','User\UserController@logout');       // 登录
    Route::get('/check/token','User\UserController@checkToken');       // 登录


});
