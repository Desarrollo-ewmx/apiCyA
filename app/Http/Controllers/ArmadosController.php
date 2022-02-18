<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Traits\Verifytoken;
use App\Models\armados;

class ArmadosController extends Controller
{
    use Verifytoken;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        // $data=[];
        $armado = armados::findOrFail($id);
        // $armado = armados::where('id', '=', $id)->get();
        if($armado){
            // $data=[]; 
            $data['id']=$armado->id;
            $data['ruta']=$armado->img_rut;
            $data['complemento']=$armado->img_nom;
            $data['ruta_Completa']=$armado->img_rut . $armado->img_nom;
            $data['clon']=$armado->clon;
            $data['numero_clones']=$armado->num_clon;
            $data['tipo_armado']=$armado->tip;
            $data['nombre_armado']=$armado->nom;
            $data['sku']=$armado->sku;
            $data['gama']=$armado->gama;
            $data['armado_catalogo']=$armado->arm_de_cat;
            $data['precio_redondeado']=$armado->prec_redond;
            $data['tamaño']=$armado->tam;
            $data['peso']=$armado->pes;
            $data['altura']=$armado->alto;
            $data['ancho']=$armado->ancho;
            $data['largo']=$armado->largo;
            return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
            // return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
        }else{
                return response()->json(['data'=>[],"message"=>"armado no encontrado","code"=>404]);
            }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    public function armados(Request $request){
        $validated = $request->validate([
            'token'=>'required'
        ]);
        if($this->verifica($request->token)){
            // $data=[];
            $armado = armados::where('id', '!=', 0)->get();
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
                    array_push($data['armado'],$datos);
                }
                // return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
                return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"armado no encontrado","code"=>404]);
                }
        }
    }
}
