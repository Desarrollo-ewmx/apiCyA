<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\armados;
use App\Models\CotizacionArmados;
use Illuminate\Http\Request;

class CotizacionArmadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $cot = CotizacionArmados::get();
        return $cot;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // $validated = $request->validate([
                // 'id_armado' => 'required', 
                // 'img_rut' => 'nullable', 
                // 'img_nom' => 'nullable', 
                // 'es_de_regalo' => 'required', 
                // 'cant' => 'required', 
                // 'cant_direc_carg' => 'required',
                // 'cost_env' => 'required', 
                // 'tip_desc' => 'nullable', 
                // 'manu' => 'nullable', 
                // 'porc' => 'nullable', 
                // 'desc' => 'required', 
                // 'sub_total' => 'required', 
                // 'con_iva' => 'required', 
                // 'iva' => 'required', 
                // 'tot' => 'required',
                // 'cotizacion_id' => 'required',
            // ]);
            // if($this->verifica($request->token)){
                // if($validated){
                    $cota = new CotizacionArmados();
                    $arm = armados::find($request->id_armado);
                    // $cota->img_rut=;
                    // $cota->img_nom=;
                    $cota->id_armado = $request->id_armado;
                    $cota->tip = $arm->tip;
                    $cota->nom = $arm->nom;
                    /**Corregir esta parte, ya modificado se refiere que si un arcon a se modificó, entonces se va a tomar en personalizados los que se modificaron para ese usuario */
                    if($arm->arm_de_cat == 'Si'){
                        $cota->ya_mod = '0';
                    }else {$cota->ya_mod = '1';}
                    $cota->sku = $arm->sku;
                    $cota->gama = $arm->gama;
                    $cota->dest = $arm->dest;
                    $cota->tam = $arm->tam;
                    $cota->pes = $arm->pes;
                    $cota->alto = $arm->alto;
                    $cota->ancho = $arm->ancho;
                    $cota->largo = $arm->largo;
                    // $cota->es_de_regalo = $request->es_de_regalo;
                    if($request->es_de_regalo =='' || $request->es_de_regalo =='No'){
                        $cota->es_de_regalo = 'No';
                    }else{$cota->es_de_regalo = 'Si';}
                    $cota->cant = $request->cant;
                    $cota->cant_direc_carg = $request->cant_direc_carg;
                    $cota->prec_de_comp = $arm->prec_de_comp;
                    $cota->prec_origin = $arm->prec_origin;
                    $cota->desc_esp = $arm->desc_esp;
                    $cota->prec_redond = $arm->prec_redond;
                    $cota->cost_env = $request->cost_env;
                    if($cota->tip_desc == ''){
                        $cota->tip_desc = 'Sin descuento';
                    }
                    if($cota->desc_cot == ''){
                        $cota->desc_cot = 0.00;
                    }
                    $cota->manu = $request->manu;
                    $cota->porc = $request->porc;
                    $cota->desc = $request->desc;
                    $cota->sub_total = $request->sub_total;
                    if($cota->con_iva == '' || $cota->con_iva == 'Con IVA' ){
                        $cota->con_iva == 'Con IVA';
                    }else{$cota->con_iva = $request->con_iva;}
                    $cota->iva = $request->iva;
                    $cota->tot = $request->tot;

                    /*Los que dicen cot son para mantener los precios al momento de la facturación y se tendrán que actualizar cuando se convierta en pedido, asignandole los precios de la cotización hasta ese momento
                    $cota->prec_cot = ;
                    $cota->sub_tot_cot = ;
                    $cota->desc_cot = ;
                    $cota->iva_cot = ;
                    $cota->tot_cot = ;*/
                    
                    $cota->cotizacion_id = $request->cotizacion_id; 
                    $cota->created_at_arm = 'Apiecommerce';
                    $cota->created_at = date('Y-m-d H:i:s');
                    $cota->updated_at = date('Y-m-d H:i:s'); 
                    // return $cota;
                    // $cota->save();


                    $prodenarmado = DB::table('armado_tiene_productos')->where('armado_id',$request->id_armado)->get();
                    // $arr['productos']=[];
                    for ($i=0; $i < count($prodenarmado); $i++) {
                        $prod = DB::table('productos')->where('id',$prodenarmado[$i]->producto_id)->get();
                        return $prod;
                        // $arreglo=[]; 
                        // $arreglo['armado_id'] = $prodenarmado[$i]->armado_id;
                        // $arreglo['producto_id'] = $prodenarmado[$i]->producto_id;
                        // array_push($arr['productos'],$arreglo);
                    }
                    // return $arr;

                    // $cota->id_producto = id_producto; 
                    // $cota->cant = cant; 
                    // $cota->produc = produc; 
                    // $cota->sku = sku; 
                    // $cota->marc = marc; 
                    // $cota->tip = tip; 
                    // $cota->tam = tam; 
                    // $cota->alto = alto; 
                    // $cota->ancho = ancho; 
                    // $cota->largo = largo; 
                    // $cota->cost_arm = cost_arm; 
                    // $cota->prove = prove; 
                    // $cota->prec_prove = prec_prove; 
                    // $cota->utilid = utilid; 
                    // $cota->prec_clien = prec_clien; 
                    // $cota->categ = categ; 
                    // $cota->etiq = etiq; 
                    // $cota->pes = pes; 
                    // $cota->cod_barras = cod_barras; 
                    // $cota->armado_id = armado_id; 
                    // $cota->created_at = created_at; 
                    // $cota->updated_at = updated_at; 
                    // $cota->deleted_at = deleted_at;
                    return $cota->id; 
                    return response()->json(['data'=>[],"message"=>"Cotización regristrada con éxito","code"=>201]);
                // }
            // }else{
            //         return response()->json(['data'=>[],"message"=>"token invalido","code"=>403],403);
            // }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th],422);
        }
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
}
