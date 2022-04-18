<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\armados;
use App\Models\CotizacionArmados;
use Illuminate\Http\Request;
use App\Models\CotizacionArmadoProductos;
use App\Models\Producto;
use App\Models\cotizaciones;
use Illuminate\Support\Facades\Storage;
use App\Traits\Verifytoken;

class CotizacionArmadosController extends Controller
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
            $validated = $request->validate([
                'id_armado' => 'required', 
                // 'img_rut' => 'nullable', 
                // 'img_nom' => 'nullable', 
                // 'es_de_regalo' => 'required', 
                'cant' => 'required', 
                'cant_direc_carg' => 'required',
                'cost_env' => 'required', 
                // 'tip_desc' => 'nullable', 
                // 'manu' => 'nullable', 
                // 'porc' => 'nullable', 
                // 'desc' => 'required', 
                // 'sub_total' => 'required', 
                // 'con_iva' => 'required', 
                // 'iva' => 'required', 
                // 'tot' => 'required',
                'cotizacion_id' => 'required'
            ]);
            if($this->verifica($request->token)){
                if($validated){
                    $cota = new CotizacionArmados();
                    $arm = armados::find($request->id_armado);
                    if($arm->img_rut_min != null){
                        $nueva_ruta = 'cotizacion/'.time().'.jpeg';
                        Storage::disk('s3')->copy($arm->img_nom_min , $nueva_ruta); 
                        $cota->img_rut = $arm->img_rut_min;
                        $cota->img_nom = $nueva_ruta;
                    }
                    $cota->id_armado = $request->id_armado;
                    $cota->tip = $arm->tip;
                    $cota->nom = $arm->nom;
                    /**Corregir esta parte, ya modificado se refiere que si un arcon a se modificó, entonces se va a tomar en personalizados los que se modificaron para ese usuario */
                    if($arm->num_clon > 0){
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
                    // $cota->manu = $request->manu;
                    // $cota->porc = $request->porc;
                    $cota->desc = 0.00;
                    $sub = $arm->prec_redond * $request->cant;
                    $cota->sub_total = $sub;
                    if($cota->con_iva == '' || $cota->con_iva == 'Con IVA' ){
                        $cota->con_iva == 'Con IVA';
                        $cota->iva = $sub * 0.16;
                        $cota->tot = $sub * 1.16;
                    }else{$cota->con_iva = $request->con_iva; $cota->iva = $request->iva; $cota->tot = $request->tot;}
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
                    $cota->save();

                    $prodenarmado = DB::table('armado_tiene_productos')->where('armado_id',$request->id_armado)->get();
                    $arr['productos']=[];
                    for ($i=0; $i < count($prodenarmado); $i++) {
                        $prod = DB::table('productos')->where('id',$prodenarmado[$i]->producto_id)->get();
                        // return $prod;
                        $arreglo=[]; 
                        $arreglo['armado_id'] = $prodenarmado[$i]->armado_id;
                        $arreglo['producto_id'] = $prodenarmado[$i]->producto_id;
                        array_push($arr['productos'],$arreglo);
                    }
                    $this->rellenarcatp($arr['productos'],$cota->id);
                    $this->updatecot($cota);
                    // return $cota->id; 
                    return response()->json(['data'=>[],"message"=>"Cotización regristrada con éxito","code"=>201]);
                }
            }else{
                    return response()->json(['data'=>[],"message"=>"token invalido","code"=>403],403);
            }
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

    public function rellenarcatp($productos,$armado_id){
        for ($i=0; $i < count($productos); $i++) {
            // return $productos[$i]['producto_id'];
            $producto = Producto::find($productos[$i]['producto_id']);
            // return $producto;
            $catp = new CotizacionArmadoProductos();
            $catp->id_producto = $productos[$i]['producto_id'];
            $catp->cant = 1; 
            $catp->produc = $producto->produc; 
            $catp->sku = $producto->sku; 
            $catp->marc = $producto->marc; 
            $catp->tip = $producto->tip; 
            $catp->tam = $producto->tam; 
            $catp->alto = $producto->alto; 
            $catp->ancho = $producto->ancho; 
            $catp->largo = $producto->largo; 
            $catp->cost_arm = $producto->cost_arm; 
            $catp->prove = $producto->prove; 
            $catp->prec_prove = $producto->prec_prove; 
            $catp->utilid = $producto->utilid; 
            $catp->prec_clien = $producto->prec_clien; 
            $catp->categ = $producto->categ; 
            $catp->etiq = $producto->etiq; 
            $catp->pes = $producto->pes; 
            $catp->cod_barras = $producto->cod_barras; 
            $catp->armado_id = $armado_id;
            $catp->save();
            // return $catp;        
        }
        // return count($productos);
    }
    
    public function updatecot($cotizacion){
        try {
                $coti = Cotizaciones::find($cotizacion->cotizacion_id);
                $tot_arm=$coti->tot_arm + $cotizacion->cant;
                $sub_total=$coti->sub_total + $cotizacion->sub_total;
                $iva=$coti->iva + $cotizacion->iva;
                $tot=$coti->tot + $cotizacion->tot;
                $cost_env=$coti->cost_env + $cotizacion->cost_env;
                Cotizaciones::where('id',$cotizacion->cotizacion_id)->update([
                    'tot_arm' => $tot_arm,
                    'sub_total' => $sub_total,
                    'iva' => $iva,
                    'tot' => $tot,
                    'cost_env' => $cost_env
                ]);
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th],422);
        }
    }
}
