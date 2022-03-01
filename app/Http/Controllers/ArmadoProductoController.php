<?php

namespace App\Http\Controllers;

use App\Models\ArmadoProducto;
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
}
