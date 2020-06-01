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


Route::get('login', 'Admin\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Admin\Auth\LoginController@login')->name('login');

Route::group(['middleware' => 'auth:admin'], function() {
    Route::post('logout', 'Admin\Auth\LoginController@logout')->name('logout');

    Route::get('/', 'Admin\HomeController@index')->name('home');
});