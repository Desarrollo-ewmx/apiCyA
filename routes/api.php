<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArmadosController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\FacturasController;


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

Route::group(['middleware' => 'api',['cors']], function ($router) {
    // Route::post('login', 'App\Http\Controllers\UserController@authenticate');

    Route::post('pruebas','App\Http\Controllers\CotizacionesController@index');
    Route::post('cyaquery','App\Http\Controllers\UserController@index');
    Route::post('userupdate','App\Http\Controllers\UserController@update');

    Route::post('cotizacion','App\Http\Controllers\CotizacionesController@create');
    Route::post('useradd','App\Http\Controllers\UserController@create');
    Route::post('pedido','App\Http\Controllers\PedidosController@pedido');
    
    Route::get('muestraarmados','App\Http\Controllers\ArmadosController@armados');
    Route::get('muestraarmado/{id}','App\Http\Controllers\ArmadosController@show');
    Route::get('gama/{gama}','App\Http\Controllers\ArmadosController@gamas');

    Route::get('muestraproducto/{id}','App\Http\Controllers\ProductosController@show');
    Route::get('muestraproductos','App\Http\Controllers\ProductosController@productos');

    Route::get('armadosporproductos/{id}','App\Http\Controllers\ArmadoProductoController@show');
    Route::get('muestraarmadoyproductos/{id}','App\Http\Controllers\ArmadoProductoController@muestratodo');
    Route::get('muestraarmadoyproductos','App\Http\Controllers\ArmadoProductoController@muestratodos');

    Route::get('fac','App\Http\Controllers\FacturasController@fact');
    Route::get('facpornombre','App\Http\Controllers\FacturasController@factone');

    Route::get('plantilla','App\Http\Controllers\PlantillaController@mostrarplantilla');
    
    // Route::get('marca','App\Http\Controllers\MarcaTieneArmadoController@filtro');

    Route::get('info','App\Http\Controllers\SistemaController@index');

    Route::post('cot','App\Http\Controllers\CotizacionesController@create');
    Route::put('cotizacion','App\Http\Controllers\CotizacionesController@update');
    Route::get('cotizacion','App\Http\Controllers\CotizacionesController@ver');
    Route::get('cotizacionp','App\Http\Controllers\CotizacionesController@vermas');
    Route::get('cotizaciond/{id}','App\Http\Controllers\CotizacionesController@delete');
    Route::post('nomcot','App\Http\Controllers\CotizacionesController@updatenom');

    Route::get('cotarmados','App\Http\Controllers\CotizacionArmadosController@index');
    Route::post('cotarmados','App\Http\Controllers\CotizacionArmadosController@store');
    Route::post('upcotarmados','App\Http\Controllers\CotizacionArmadosController@update');
    Route::post('cotarmadosd','App\Http\Controllers\CotizacionArmadosController@delete');

    Route::get('prodenarmado/{id}','App\Http\Controllers\CotizacionArmadoProductosController@index');

    Route::post('msgpassemail','App\Http\Controllers\UserController@mensajecambio');
    Route::post('msgpass','App\Http\Controllers\UserController@cambiopass');

    Route::post('updatecot','App\Http\Controllers\CotizacionArmadosController@updatecot');
    
    Route::post('aprobarcot','App\Http\Controllers\PedidosController@aprobarcotizacion');

    Route::post('direcciones','App\Http\Controllers\DireccionController@store');
    Route::get('cotuserid','App\Http\Controllers\CotizacionesController@paraligar');
    Route::get('cotarmdir','App\Http\Controllers\CotizacionArmadoDireccionController@show');
    Route::get('tabdir','App\Http\Controllers\CotizacionArmadoDireccionController@muestradirecciones');

    
    Route::get('llenacatd','App\Http\Controllers\CotizacionArmadoDireccionController@store');
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
