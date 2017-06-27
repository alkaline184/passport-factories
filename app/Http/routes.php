<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'FactoriesController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


Route::get('/factories/{id}', 'FactoriesController@getFactory');
Route::get('/factories/', 'FactoriesController@getFactories');
Route::delete('/factories/{id}', 'FactoriesController@deleteFactory');
Route::post('/factories/', 'FactoriesController@createFactory');
Route::put('/factories/{id}', 'FactoriesController@updateFactory');
Route::put('/factories/{id}/children', 'FactoriesController@addChildren');