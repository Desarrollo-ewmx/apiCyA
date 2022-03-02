<?php

namespace App\Http\Controllers;

use App\Models\ArmadoProducto;
use App\Models\armados;
use App\Models\Producto;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class ArmadoProductoController extends Controller
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
        //
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
            return response()->json(['data'=>[],"message"=>"id del producto no encontrado","code"=>404]);
        }
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
        $idarmado = ArmadoProducto::where('armado_id', '=', $id)->get();
        $armado = armados::findOrFail($id);
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
            return response()->json(['data'=>[],"message"=>"id del producto no encontrado","code"=>404]);
        }
        // return $idarmado;
    }
}
