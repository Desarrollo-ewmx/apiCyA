<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantila;

class PlantillaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function mostrarplantilla(){
        try {
            $plantilla = Plantila::get();
            // return $plantilla;
            if($plantilla){
                $data['info']=[];
                for ($i=0; $i< count($plantilla); $i++) {
                    $datos['id']=$plantilla[$i]->id;
                    $datos['nom']=$plantilla[$i]->nom;
                    $datos['mod']=$plantilla[$i]->mod;
                    $datos['asunt']=$plantilla[$i]->asunt;
                    $datos['dis_de_la_plant']=$plantilla[$i]->dis_de_la_plant;
                    // return $datos;
                    array_push($data['info'],$datos);
                }
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"Plantilla no encontrada","code"=>404]);
                }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
}
