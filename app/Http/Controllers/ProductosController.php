<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductosController extends Controller
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
        try {
            //code...
            $producto = Producto::findOrFail($id);
            if($producto){
                $data['id']=$producto->id;
                $data['imagen']=$producto->img_prod_rut . $producto->img_prod_nom;
                $data['nombre']=$producto->produc;
                $data['catalogo']=$producto->pro_de_cat;
                $data['sku']=$producto->sku;
                $data['marca']=$producto->marc;
                $data['tipo']=$producto->tip;
                $data['tamaño']=$producto->tam;
                $data['categoria']=$producto->categ;
                return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"producto no encontrado","code"=>404]);
                }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th,'code'=>404]);
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
    
    public function productos(){
        try {
            //code...
            $productos = Producto::where('id', '!=', 0)->get();
            if($productos){
                $data['productos']=[];
                for ($i=0; $i< count($productos); $i++) {
                    $datos['id']=$productos[$i]->id;
                    $datos['imagen']=$productos[$i]->img_prod_rut . $productos[$i]->img_prod_nom;
                    $datos['nombre']=$productos[$i]->produc;
                    $datos['catalogo']=$productos[$i]->pro_de_cat;
                    $datos['sku']=$productos[$i]->sku;
                    $datos['marca']=$productos[$i]->marc;
                    $datos['tipo']=$productos[$i]->tip;
                    $datos['tamaño']=$productos[$i]->tam;
                    $datos['categoria']=$productos[$i]->categ;
                    // return $datos;
                    array_push($data['productos'],$datos);
                }
                return response(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                    return response()->json(['data'=>[],"message"=>"producto no encontrado","code"=>404]);
                }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
}
