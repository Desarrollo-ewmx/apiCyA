<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Verifytoken;
use App\Models\CotizacionArmadoTieneDirecciones;
use App\Models\CotizacionArmados;
use App\Models\cotizaciones;
use App\Models\carmados;

class CotizacionArmadoDireccionController extends Controller
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
        return CotizacionArmadoTieneDirecciones::all();
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
        try {
            $armado = CotizacionArmados::with('cotizacion')->findOrFail($request->id_registro_cot_arm);
            $cotizacion = $armado->cotizacion;
            // return $armado->cant_direc_carg . ' la cant: >=' . $request->cantidad . 'y'. $request->cantidad .'<='. $armado->cant;
            if ($armado->cotizacion->estat=='Abierta') {
                if ($request->cantidad>0 && $request->cantidad <= $armado->cant && $armado->cant_direc_carg < $armado->cant) {
                    // if ($armado->cant_direc_carg>=$request->cantidad) {
                    // return 'Aqui llega';
                        $direccion = new CotizacionArmadoTieneDirecciones();
                        $direccion->seg                       = 'No';
                        $direccion->est                       = $request->est;
                        $direccion->armado_id                 = $request->id_registro_cot_arm;
                        $direccion->created_at_dir            = $request->created_at_dir;
                        $direccion->cant                      = $request->cantidad;
                        $direccion->tam                       = $armado->tam;
                        if (strlen($request->detalles_de_la_ubicacion) == 0) {
                            $direccion->detalles_de_la_ubicacion='Sin detalles';
                        }else{
                            $direccion->detalles_de_la_ubicacion  = $request->detalles_de_la_ubicacion;
                        }
                        if($direccion->est == 'Ciudad de México (Ciudad de México)' OR $direccion->est == 'México (Edo. México)') {
                            $direccion->for_loc               = 'Local';
                            $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                            $direccion->tiemp_ent             = 'De 1 a 4 dias';
                            $direccion->cost_tam_caj = 0.00;
                        }else {
                            $direccion->for_loc               = 'Foráneo';
                            $direccion->tiemp_ent             = 'De 7 a 10 dias';
                            $direccion->met_de_entreg         = 'Transportes Ferro';
                            if ($direccion->tam == 'Mediano') {
                                $direccion->cost_tam_caj = 30.00;
                            }elseif ($direccion->tam == 'Chico'){
                                $direccion->cost_tam_caj = 20.00;
                            }elseif ($direccion->tam == 'Grande'){
                                $direccion->cost_tam_caj = 40.00;
                            }
                        }
                        $direccion->save();
                        $armado->cost_env         += $direccion->cost_por_env;
                        $armado->cant_direc_carg  += $direccion->cant;
                        $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                        $armado->save();
                        $this->calculaValoresCotizacion($cotizacion);
                        return response()->json(['data'=>[],"message"=>"Se ha agregado direccion correctamente","code"=>200]);
                    // }
                    // else{
                    //     return response()->json(['data'=>[],"message"=>"No se pueden ingresar más arcones de los que existen en la cotización","code"=>200]);
                    // }
                }
                else{
                    return response()->json(['data'=>[],"message"=>"No se pueden ingresar arcones a la cotización","code"=>200]);
                }
            }else{
                return response()->json(['data'=>[],"message"=>"La cotización no se encuentra abierta","code"=>200]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $catd = CotizacionArmadoTieneDirecciones::where('armado_id',$request->armado_id)->get();
        $datos['direccion']=[];
        for ($i=0; $i < count($catd); $i++) { 
            $arreglo = $catd[$i];
            array_push($datos['direccion'],$arreglo);
        }
        return $datos['direccion'];
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
    public function calculaValoresCotizacion($cotizacion)
    {
        // return $cotizacion->armados;
        $cotizacion->tot_arm    = $cotizacion->armados->sum('cant');
        $cotizacion->cost_env   = $cotizacion->armados->sum('cost_env');
        $cotizacion->desc       = $cotizacion->armados->sum('desc');
        $cotizacion->sub_total  = $cotizacion->armados->sum('sub_total');
        $cotizacion->iva        = $cotizacion->armados->sum('iva');

        if ($cotizacion->con_com == 'on') {
            $total                = $cotizacion->armados->sum('tot');
            $comision             = $total * 1.05;
            $cotizacion->com      = $comision - $total;
            $cotizacion->tot      = $comision;
        } else {
            $cotizacion->com = 0.00;
            $cotizacion->tot = $cotizacion->armados->sum('tot');
        }
        $cotizacion->save();
        return $cotizacion;
    }
    public function sumaValoresArmadoCotizacion($armado) {
        $sub_total          = ($armado->cant * $armado->prec_redond) + $armado->cost_env;
        $armado->sub_total  = $sub_total - $armado->desc;
        if($armado->con_iva == 'Con IVA') {
            $armado->iva  = $armado->sub_total * 0.16;
        } elseif($armado->con_iva == 'Sin IVA') {
            $armado->iva  = 0.00;
        }
        $armado->tot    = $armado->sub_total + $armado->iva;
        return $armado;
    }
    public function muestradirecciones(Request $request){
        try {
            $validated = $request->validate([
                'token'=>'required'
            ]);
            //  return $request;
            if($this->verifica($request->token)){
            $cot = cotizaciones::where('id',$request->id)->with("armados")->first();
            $item=[];
            $dir = null;
            foreach ($cot->armados as $armado){
                $dir = CotizacionArmadoTieneDirecciones::where('armado_id',$armado->id)->get();
                array_push($item,$dir);
            }
            $direcciones = $item[0];
            return response()->json(['data'=>$direcciones,"message"=>"Direcciones encontradas correctamente","code"=>200]);
        }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
}
