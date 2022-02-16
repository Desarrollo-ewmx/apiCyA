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
        $data=[];
        $armado = armados::findOrFail($id);
        // $armado = armados::where('id', '=', $id)->get();
        if($armado){
            $data=[]; 
            $data['Id']=$armado->id;
            $data['Ruta']=$armado->img_rut;
            $data['Complemento']=$armado->img_nom;
            $data['Ruta_Completa']=$armado->img_rut . $armado->img_nom;
            $data['Clon']=$armado->clon;
            $data['Numero_clones']=$armado->num_clon;
            $data['Tipo_armado']=$armado->tip;
            $data['Nombre_armado']=$armado->nom;
            $data['Sku']=$armado->sku;
            $data['Gama']=$armado->gama;
            $data['Armado_catalogo']=$armado->arm_de_cat;
            $data['Precio_redondeado']=$armado->prec_redond;
            $data['Tamaño']=$armado->tam;
            $data['Peso']=$armado->pes;
            $data['Altura']=$armado->alto;
            $data['Ancho']=$armado->ancho;
            $data['Largo']=$armado->largo;
            return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
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
            $data=[];
            $armado = armados::where('id', '!=', 0)->get();
            if($armado){
                $data['armado']=[];
                for ($i=0; $i< count($armado); $i++) {
                    $datos=[]; 
                    $datos['Id']=$armado[$i]->id;
                    $datos['Ruta']=$armado[$i]->img_rut;
                    $datos['Complemento']=$armado[$i]->img_nom;
                    $datos['Ruta_Completa']=$armado[$i]->img_rut . $armado[$i]->img_nom;
                    $datos['Clon']=$armado[$i]->clon;
                    $datos['Numero_clones']=$armado[$i]->num_clon;
                    $datos['Tipo_armado']=$armado[$i]->tip;
                    $datos['Nombre_armado']=$armado[$i]->nom;
                    $datos['Sku']=$armado[$i]->sku;
                    $datos['Gama']=$armado[$i]->gama;
                    $datos['Armado_catalogo']=$armado[$i]->arm_de_cat;
                    $datos['Precio_redondeado']=$armado[$i]->prec_redond;
                    $datos['Tamaño']=$armado[$i]->tam;
                    $datos['Peso']=$armado[$i]->pes;
                    $datos['Altura']=$armado[$i]->alto;
                    $datos['Ancho']=$armado[$i]->ancho;
                    $datos['Largo']=$armado[$i]->largo;
                    array_push($data['armado'],$datos);
                }
                return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"armado no encontrado","code"=>404]);
                }
        }
    }
}
