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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});







/***
 * ADDED BY NKALLA
 */

Route::group(['middleware' => 'auth:api'], function() {

    Route::get('articles', 'ArticleController@index');
    Route::get('articles/{id}', 'ArticleController@articleById');
    Route::get('users', 'UserController@index');
    Route::post('articles', 'ArticleController@newArticle');
    Route::put('articles', 'ArticleController@updateArticle');
    Route::put('articles/{id}', 'ArticleController@publishArticle');

});



Route::post('users', 'Auth\RegisterController@register');
Route::post('users/login', 'Auth\LoginController@login');
Route::post('users/logout', 'Auth\LoginController@logout');





/***
 * END ADDED BY NKALLA
 */