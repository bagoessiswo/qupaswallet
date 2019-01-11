<?php

use Illuminate\Http\Request;

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


Route::post('login', 'UserApiController@login');
Route::post('register', 'UserApiController@register');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'UserApiController@logout');
 
    Route::get('user', 'UserApiController@getAuthUser');
});
