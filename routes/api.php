<?php

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


Route::middleware('auth:api')->group(function(){
    Route::post('/logout', 'Auth\AuthController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    

    Route::get('members', 'User\UserController@index');
    Route::get('userInfo', 'User\UserController@authInfo');
    Route::post('fetchTransactions', 'Accounts\StatementsController@index');

    
});
Route::post('setPassword', 'Auth\UserLogin@setPassword');
Route::post('loginUser', 'Auth\UserLogin@memberLogin');
Route::post('getUserState', 'Auth\User@memberState');
Route::post('setContact', 'Auth\User@defineContact');
Route::get('testAccounts', 'User\UserController@getTraversingAccounts');




