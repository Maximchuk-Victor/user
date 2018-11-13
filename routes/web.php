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
    return redirect('/user');
});

Auth::routes();


Route::get('/user', 'UserController@profile')->middleware('auth');

Route::post('/user/create', 'UserController@create')->middleware('auth');
