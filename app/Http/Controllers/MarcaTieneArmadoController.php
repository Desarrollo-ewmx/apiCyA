<?php

namespace App\Http\Controllers;
use App\Models\MarcaTieneArmado;
use App\Models\armados;

use Illuminate\Http\Request;

class MarcaTieneArmadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return MarcaTieneArmado::all();
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

    public function filtro(){
            // $marcas = MarcaTieneArmado::get();
            // $data=[];
            $armado = armados::join('marcas_has_armados','armados.id', '=', 'marcas_has_armados.armado_id')->where('marcas_has_armados.marca_id','=',11)->get();
            if($armado){
                $data['armado']=[];
                for ($i=0; $i< count($armado); $i++) {
                    // $datos=[]; 
                    $datos['id']=$armado[$i]->id;
                    $datos['ruta']=$armado[$i]->img_rut;
                    $datos['complemento']=$armado[$i]->img_nom;
                    $datos['ruta_Completa']=$armado[$i]->img_rut . $armado[$i]->img_nom;
                    $datos['clon']=$armado[$i]->clon;
                    $datos['numero_clones']=$armado[$i]->num_clon;
                    $datos['tipo_armado']=$armado[$i]->tip;
                    $datos['nombre_armado']=$armado[$i]->nom;
                    $datos['sku']=$armado[$i]->sku;
                    $datos['gama']=$armado[$i]->gama;
                    $datos['armado_catalogo']=$armado[$i]->arm_de_cat;
                    $datos['precio_redondeado']=$armado[$i]->prec_redond;
                    $datos['tamaño']=$armado[$i]->tam;
                    $datos['peso']=$armado[$i]->pes;
                    $datos['altura']=$armado[$i]->alto;
                    $datos['ancho']=$armado[$i]->ancho;
                    $datos['largo']=$armado[$i]->largo;
                    $datos['destacado']=$armado[$i]->dest;
                    array_push($data['armado'],$datos);
                }
                // return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"armado no encontrado","code"=>404]);
                }
    }
}
