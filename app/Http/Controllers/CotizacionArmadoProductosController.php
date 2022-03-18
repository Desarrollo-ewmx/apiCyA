<?php

namespace App\Http\Controllers;

use App\Models\CotizacionArmadoProductos;
use App\Models\CotizacionArmados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionArmadoProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        try {
            $coti= CotizacionArmados::where('id', '=', $id)->get();
            if (count($coti)!==0){
                return response()->json(['data'=>$coti,"message"=>"success","code"=>200]);
            }else{
                return response()->json(['data'=>[],"message"=>"armado en cotización no encontrado","code"=>404],404);
            }
            // $coti= CotizacionArmadoProductos::where('id', '=', $id)->get();
        } catch (\Throwable $th) {
            return response()->json(['data' => null,'status'=>$th,'message' =>"id del armado en cotización no encontrado"], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function cotizacion(){
        
    }
}
