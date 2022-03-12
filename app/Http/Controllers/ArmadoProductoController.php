<?php

namespace App\Http\Controllers;

use App\Models\ArmadoProducto;
use App\Models\armados;
use App\Models\MarcaTieneArmado;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Traits\Verifytoken;


use function PHPUnit\Framework\isNull;

class ArmadoProductoController extends Controller
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        return ArmadoProducto::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            //code...
            $idarmado = ArmadoProducto::where('armado_id', '=', $id)->get();
            // return count($idarmado);
            if(count($idarmado)!=0) {
                $data['productosxarmado']=[];
                for ($i=0; $i< count($idarmado); $i++){
                    $datos['id_producto']=$idarmado[$i]->producto_id;
                    array_push($data['productosxarmado'],$datos);
                }
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                return response()->json(['data'=>[],"message"=>"id del producto no encontrado","code"=>404], 404);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['data' => null,'status'=>'error','message' =>"id del producto no encontrado"], 404);
        }
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


    public function muestratodo($id){
        try {
            //code...
            $idarmado = ArmadoProducto::where('armado_id', '=', $id)->get();
            $armado = armados::findOrFail($id);
            $armado->join('marcas_has_armados', 'marcas_has_armados.armado_id','=','armados.id')->where('marcas_has_armados.marca_id','=',11)->orderBy('armados.id', 'ASC');
            if(count($idarmado)!=0) {
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
                $data['productosxarmado']=[];
                for ($i=0; $i< count($idarmado); $i++){
                    $nuevoid = $idarmado[$i]->producto_id;
                    $datos['id_producto']=$idarmado[$i]->producto_id;
                    $producto = Producto::findOrFail($nuevoid);
                    $datos['desc_producto']['id']=$producto->id;
                    $datos['desc_producto']['imagen']=$producto->img_prod_rut . $producto->img_prod_nom;
                    $datos['desc_producto']['nombre']=$producto->produc;
                    $datos['desc_producto']['catalogo']=$producto->pro_de_cat;
                    $datos['desc_producto']['sku']=$producto->sku;
                    $datos['desc_producto']['marca']=$producto->marc;
                    $datos['desc_producto']['tipo']=$producto->tip;
                    $datos['desc_producto']['tamaño']=$producto->tam;
                    $datos['desc_producto']['categoria']=$producto->categ;
                    array_push($data['productosxarmado'],$datos);
                }
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                return response()->json(['data'=>[],"message"=>"id del producto no encontrado","code"=>404], 404);
            }
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json(['data' => null,'status'=>'error','message' =>"armados no encontrados"], 404);
            }
    }
    public function muestratodos(Request $request){
        $validated = $request->validate([
            'token'=>'required'
        ]);
        if($this->verifica($request->token)){
            // $data=[];
            // $armado = armados::where('id', '!=', 0)->where('arm_de_cat','!=','No')->get();
            $armado = armados::join('marcas_has_armados', 'marcas_has_armados.armado_id','=','armados.id')->where('marcas_has_armados.marca_id','=',11)->orderBy('armados.id', 'ASC')->get();
            // return $armado;
            if($armado){
                $data['armado']=[];
                for ($i=0; $i< count($armado); $i++) {
                    // $datos=[]; 
                    $datos['id']=$armado[$i]->armado_id;
                    // return $armado[$i]->id;
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
                    $datos['productosxarmado']=[];
                    $algo = strval($armado[$i]->armado_id);
                    // return 'Esto es algo '.$algo;
                    // 
                        $idarmado = ArmadoProducto::where('armado_id', '=', $algo)->get();
                        // return $idarmado;
                        for ($j=0; $j< count($idarmado); $j++){
                            // return $j;    
                        $nuevoid = $idarmado[$j]->producto_id;
                        $arreglo['id_producto']=$idarmado[$j]->producto_id;
                        $producto = Producto::findOrFail($nuevoid);
                        $arreglo['desc_producto']['id']=$producto->id;
                        $arreglo['desc_producto']['imagen']=$producto->img_prod_rut . $producto->img_prod_nom;
                        $arreglo['desc_producto']['nombre']=$producto->produc;
                        $arreglo['desc_producto']['catalogo']=$producto->pro_de_cat;
                        $arreglo['desc_producto']['sku']=$producto->sku;
                        $arreglo['desc_producto']['marca']=$producto->marc;
                        $arreglo['desc_producto']['tipo']=$producto->tip;
                        $arreglo['desc_producto']['tamaño']=$producto->tam;
                        $arreglo['desc_producto']['categoria']=$producto->categ;
                        array_push($datos['productosxarmado'],$arreglo);
                    }
                    array_push($data['armado'],$datos);
                }
                // return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"armado no encontrado","code"=>404],404);
                }
        }
    }
}
