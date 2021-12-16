<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\UserController;


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
Route::post('login', 'App\Http\Controllers\UserController@authenticate');

Route::group(['middleware' => 'api'], function ($router) {

    Route::post('cyaquery','App\Http\Controllers\CotizacionesController@index');
    Route::post('cyaadd','App\Http\Controllers\CotizacionesController@create');


});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
