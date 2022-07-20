<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\Verifytoken;

use App\Models\CotizacionArmadoTieneDirecciones;
use App\Models\CotizacionArmados;
use App\Models\cotizaciones;
use App\Models\Direccion;
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
            $arma2 = CotizacionArmados::where('cotizacion_id', $request->id)->get();
            $datos['armado']=[];
            $datos['armado']['dire']=[];
            $datos['armado']['id_arm']=[];
            $datos['armado']['costos_arm']=[];
            $datos['armado']['prec_redond_arm']=[];
            $datos['armado']['direccion']['cp_dire']=[];
            $datos['armado']['direccion']['cant_dire']=[];
            foreach($arma2 as $arm){
                $datos['armado']['id_arm']=$arm->id;
                array_push($datos['armado']['costos_arm'],$arm->sub_total);
                array_push($datos['armado']['prec_redond_arm'],$arm->prec_redond);
                $regarm = CotizacionArmadoTieneDirecciones::where('armado_id',$arm->id)->get();
                if(count($regarm)!=0){
                    array_push($datos['armado']['dire'],$regarm);
                    // $dire=$regarm;
                }
            }
            return $datos['armado']['dire'][0][0];
            foreach($datos['armado']['dire'] as $catdireccion){
                array_push($datos['armado']['direccion']['cant_dire'],$catdireccion->cant);
                if($catdireccion->cp){
                    array_push($datos['armado']['direccion']['cp_dire'],$catdireccion->cp);
                }
            }
            return $datos;
            return array_sum($datos['armado']['direccion']['costos_arm']);
            
            $armado = CotizacionArmados::with('cotizacion')->findOrFail($request->id_registro_cot_arm);
            $cotizacion = $armado->cotizacion;
            // return $armado->cant_direc_carg . ' la cant: >=' . $request->cantidad . 'y'. $request->cantidad .'<='. $armado->cant;
            if($armado->cotizacion->estat=='Abierta'){
                if($request->cantidad>0 && $request->cantidad <= $armado->cant && $armado->cant_direc_carg < $armado->cant){
                    // if ($armado->cant_direc_carg>=$request->cantidad) {
                    // return 'Aqui llega';
                    $direccion = new CotizacionArmadoTieneDirecciones();
                    $direccion->seg                       = 'No';
                    $direccion->est                       = $request->est;
                    $direccion->armado_id                 = $request->id_registro_cot_arm;
                    $direccion->created_at_dir            = $request->created_at_dir;
                    $direccion->cant                      = $request->cantidad;
                    $direccion->tam                       = $armado->tam;
                    if(strlen($request->detalles_de_la_ubicacion) == 0){
                        $direccion->detalles_de_la_ubicacion  ='Sin detalles';
                    }else{
                        $direccion->detalles_de_la_ubicacion  = $request->detalles_de_la_ubicacion;
                    }
                    if(($direccion->est == 'Ciudad de México (Ciudad de México)' OR $direccion->est == 'México (Edo. México)') && $cotizacion->sub_total>=4000) {
                        $direccion->for_loc               = 'Local';
                        $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                        $direccion->tiemp_ent             = 'De 1 a 4 dias';
                        $direccion->cost_por_env = 0.00;
                        $direccion->cost_tam_caj = 0.00;
                    }elseif(($direccion->est == 'Ciudad de México (Ciudad de México)' OR $direccion->est == 'México (Edo. México)') && $cotizacion->sub_total<4000){
                        $direccion->for_loc               = 'Local';
                        $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                        $direccion->tiemp_ent             = 'De 1 a 4 dias';
                        $direccion->cost_tam_caj = 0.00;
                        $direccion->cost_por_env = 250.00;
                    }elseif(($direccion->est == 'Puebla (H. Puebla de Zaragoza)' OR $direccion->est == 'Querétaro (Santiago de Querétaro)' OR $direccion->est == 'Hidalgo (Pachuca de Soto)' OR $direccion->est == 'Tlaxcala (Tlaxcala de Xicohténcatl)' OR $direccion->est == 'Morelos (Cuernavaca)') && $cotizacion->sub_total>=20000){
                        $direccion->for_loc               = 'Foráneo';
                        $direccion->tiemp_ent             = 'De 2 a 10 dias';
                        $direccion->met_de_entreg         = 'Transportes Ferro';
                        $direccion->cost_por_env = null;
                        if($direccion->tam == 'Mediano'){
                            $direccion->cost_tam_caj = 30.00;
                        }elseif($direccion->tam == 'Chico'){
                            $direccion->cost_tam_caj = 20.00;
                        }elseif($direccion->tam == 'Grande'){
                            $direccion->cost_tam_caj = 40.00;
                        }
                    }else{
                        $direccion->for_loc               = 'Foráneo';
                        $direccion->tiemp_ent             = 'De 7 a 10 dias';
                        $direccion->met_de_entreg         = 'Transportes Ferro';
                        $direccion->cost_por_env = null;
                        if($direccion->tam == 'Mediano'){
                            $direccion->cost_tam_caj = 30.00;
                        }elseif($direccion->tam == 'Chico'){
                            $direccion->cost_tam_caj = 20.00;
                        }elseif($direccion->tam == 'Grande'){
                            $direccion->cost_tam_caj = 40.00;
                        }
                    }
                    if($request->cost_tam_caj > 0){
                        $direccion->cost_por_env += $request->cost_tam_caj *  $request->cantidad;
                    }
                    $direccion->save();
                    $armado->cost_env         += $direccion->cost_por_env;
                    $armado->cant_direc_carg  += $direccion->cant;
                    $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                    $armado->save();
                    $this->calculaValoresCotizacion($cotizacion);
                    $cotizacion->save();
                    return response()->json(['data'=>[],"message"=>"Se ha agregado direccion correctamente","code"=>200]);
                    // }
                    // else{
                    //     return response()->json(['data'=>[],"message"=>"No se pueden ingresar más arcones de los que existen en la cotización","code"=>200]);
                    // }
                }else{
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
                foreach ($cot->armados as $armado){
                    $dir = CotizacionArmadoTieneDirecciones::where('armado_id',$armado->id)->get();
                    array_push($item,$dir);
                }
                $direcciones = $item[0];
                return response()->json(['data'=>$direcciones,"message"=>"Direcciones encontradas correctamente","code"=>200]);
            }
        }catch (\Throwable $th){
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
    public function nuevadir(Request $request){
        try {
            $validated = $request->validate([
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                $esNuevaDireccion=true;
                $armado = CotizacionArmados::with('cotizacion')->findOrFail($request->id_registro_cot_arm);
                $cotizacion = $armado->cotizacion;
                $validacion = Direccion::where('user_id', $cotizacion->user_id)->get();
                foreach($validacion as $valido){
                    if (strcasecmp($valido->cod_post, $request->cp) == 0) {
                        if (strcasecmp($valido->col, $request->colonia) == 0) {
                            $esNuevaDireccion=false;
                        }
                    }
                }
                if($armado->cotizacion->estat=='Abierta'){
                    if($request->cantidad>0 && $request->cantidad <= $armado->cant && $armado->cant_direc_carg < $armado->cant){
                        $direccion = new CotizacionArmadoTieneDirecciones();
                        $direccion->seg                       = 'No';
                        $direccion->est                       = $request->est;
                        $direccion->cp                        = $request->cp;
                        $direccion->mun                       = $request->del_o_munic;
                        $direccion->armado_id                 = $request->id_registro_cot_arm;
                        $direccion->created_at_dir            = $request->created_at_dir;
                        $direccion->cant                      = $request->cantidad;
                        $direccion->tam                       = $armado->tam;
                        if(strlen($request->detalles_de_la_ubicacion) == 0){
                            $direccion->detalles_de_la_ubicacion  ='Sin detalles';
                        }else{
                            $direccion->detalles_de_la_ubicacion  = $request->detalles_de_la_ubicacion;
                        }
                        if(($direccion->est == 'Ciudad de México (Ciudad de México)' OR $direccion->est == 'México (Edo. México)') && $cotizacion->sub_total>=4000) {
                            $direccion->for_loc               = 'Local';
                            $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                            $direccion->tiemp_ent             = 'De 1 a 4 dias';
                            $direccion->cost_por_env = 0.00;
                            $direccion->cost_tam_caj = 0.00;
                        }elseif(($direccion->est == 'Ciudad de México (Ciudad de México)' OR $direccion->est == 'México (Edo. México)') && $cotizacion->sub_total<4000){
                            $direccion->for_loc               = 'Local';
                            $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                            $direccion->tiemp_ent             = 'De 1 a 4 dias';
                            $direccion->cost_tam_caj = 0.00;
                            $direccion->cost_por_env = 250.00;
                        }elseif(($direccion->est == 'Puebla (H. Puebla de Zaragoza)' OR $direccion->est == 'Querétaro (Santiago de Querétaro)' OR $direccion->est == 'Hidalgo (Pachuca de Soto)' OR $direccion->est == 'Tlaxcala (Tlaxcala de Xicohténcatl)' OR $direccion->est == 'Morelos (Cuernavaca)') && $cotizacion->sub_total>=20000){
                            $direccion->for_loc               = 'Foráneo';
                            $direccion->tiemp_ent             = 'De 2 a 10 dias';
                            $direccion->met_de_entreg         = 'Transportes Ferro';
                            $direccion->cost_por_env = null;
                        }else{
                            $direccion->for_loc               = 'Foráneo';
                            $direccion->tiemp_ent             = 'De 7 a 10 dias';
                            $direccion->met_de_entreg         = 'Transportes Ferro';
                            $direccion->cost_por_env = null;
                        }
                        if($direccion->cost_tam_caj > 0){
                            $direccion->cost_por_env += $direccion->cost_tam_caj *  $request->cantidad;
                        }
                        $direccion->save();
                        $armado->cost_env         += $direccion->cost_por_env;
                        $armado->cant_direc_carg  += $direccion->cant;
                        $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                        if ($esNuevaDireccion==true) {
                            $this->direcciones($direccion, $cotizacion->user_id, $request);
                        }
                        $armado->save();
                        $this->calculaValoresCotizacion($cotizacion);
                        $cotizacion->save();
                        return response()->json(['data'=>[],"message"=>"Se ha agregado la dirección correctamente","code"=>200]);
                    }else{
                        return response()->json(['data'=>[],"message"=>"No se pueden ingresar arcones a la cotización","code"=>200]);
                    }
                }else{
                    return response()->json(['data'=>[],"message"=>"La cotización no se encuentra abierta","code"=>200]);
                }
            }else{
                return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
    public function verdirec(Request $request){
        try {
            $datos['armados']=[];
            $cot = cotizaciones::where('id',$request->id)->with("armados")->first();
            if ($request->user_id==$cot->user_id) {
                $arma2 = CotizacionArmados::where('cotizacion_id', $request->id)->get();
                foreach($arma2 as $arm){
                    $regarm = CotizacionArmadoTieneDirecciones::where('armado_id',$arm->id)->get();
                    if(count($regarm)!=0){
                        foreach($regarm as $direccion){
                            $data['id']=$direccion->id;
                            $data['nom']=$arm->nom;
                            $data['cant']=$direccion->cant;
                            $data['cp']=$direccion->cp;
                            $data['est']=$direccion->est;
                            $data['tot']=$direccion->cant * $arm->prec_redond;
                            $data['cost_por_env']=$direccion->cost_por_env;
                            array_push($datos['armados'],$data);
                        }
                    }
                }
                return response()->json(['data'=>$datos,"message"=>"Armados encontrados","code"=>200]);
            }else{
                return response()->json(['data'=>[],"message"=>"Usuario no coincide","code"=>200]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
    public function delete(Request $request){
        try {
            $validated = $request->validate([
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                DB::beginTransaction();
                $direccion = CotizacionArmadoTieneDirecciones::where('id',$request->id)->firstOrFail();
                $armado = CotizacionArmados::with('cotizacion')->findOrFail($direccion->armado_id);
                $cotizacion = $armado->cotizacion;
                if($armado->cotizacion->estat=='Abierta'){
                    $direccion->forceDelete();
                    // ACTUALIZA Y GENERA LOS NUEVOS PRECIOS DEL ARMADO
                    $armado->cost_env        -= $direccion->cost_por_env;
                    $armado->cant_direc_carg -= $direccion->cant;
                    $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                    $armado->save();
                    // GENERA LOS NUEVOS PRECIOS DE LA COTIZACIÓN
                    $this->calculaValoresCotizacion($cotizacion);
                    // IMPORTANTE NO SE IMPLEMENTARA PAPELERA DE RECICLAJE (POR LOS PRECIOS DE LOS ARMADOS RELACIONADOS A LA COTIZACIÓN)
                    DB::commit();
                    return response()->json(['data'=>[],"message"=>"La dirección se ha eliminado con éxito","code"=>200]);
                }
            }else{
                return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
    public function direcciones($direccion, $user_id, $request){
        $nuevadir = new Direccion();
        $nuevadir->nom_ref_uno = 'Pendiente';
        $nuevadir->calle = 'Pendiente';
        $nuevadir->no_ext = 'Falta';
        if (strlen($request->ciudad) == 0) {
            $nuevadir->ciudad = $request->ciudad;
        }else{
            $nuevadir->ciudad = 'S/N';
        }
        $nuevadir->col = $request->colonia;
        $nuevadir->del_o_munic = $request->del_o_munic;
        $nuevadir->cod_post = $direccion->cp;
        $nuevadir->user_id = $user_id;
        $user=DB::table('users')->where("id",$user_id)->first();
        $nuevadir->created_at_direc = $user->email_registro;
        $nuevadir->updated_at_direc = $user->email_registro;
        $nuevadir->save();
    }
    public function agregaravariasdirecciones(Request $request){
        try {
            $validated = $request->validate([
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                $data['armados']=[];
                $listaids = explode(',',$request->ids);
                $armadosporcot = CotizacionArmados::with('cotizacion')->where("cotizacion_id",$request->id)->with('cotizacion')->get();
                $cotizacion = cotizaciones::where('id',$request->id)->first();
                if($cotizacion->estat=='Abierta'){
                    // $variable=[];
                    foreach($armadosporcot as $armado){
                        if (in_array($armado->id,$listaids) && $armado->cant > $armado->cant_direc_carg) {
                            // array_push($variable,$armado->id);
                            $direccion = new CotizacionArmadoTieneDirecciones();
                            $direccion->seg                       = 'No';
                            $direccion->est                       = 'Tarifa única (Varios estados)';
                            $direccion->armado_id                 = $armado->id;
                            $direccion->created_at_dir            = $request->created_at_dir;
                            $direccion->cant                      = $armado->cant - $armado->cant_direc_carg;
                            $direccion->tam                       = $armado->tam;
                            if($direccion->tam == 'Mediano'){
                                $direccion->cost_tam_caj = 30.00;
                            }elseif($direccion->tam == 'Chico'){
                                $direccion->cost_tam_caj = 20.00;
                            }elseif($direccion->tam == 'Grande'){
                                $direccion->cost_tam_caj = 40.00;
                            }
                            if(strlen($request->detalles_de_la_ubicacion) == 0){
                                $direccion->detalles_de_la_ubicacion  ='Sin detalles';
                            }else{
                                $direccion->detalles_de_la_ubicacion  = $request->detalles_de_la_ubicacion;
                            }
                            $direccion->for_loc               = 'Foráneo';
                            $direccion->met_de_entreg         = 'Transportes Ferro';
                            $direccion->tiemp_ent             = 'De 7 a 10 dias';
                            $direccion->cost_por_env = 250.00;
                            if($direccion->cost_tam_caj > 0){
                                $direccion->cost_por_env += $direccion->cost_tam_caj *  $direccion->cant;
                            }
                            $direccion->save();
                            $armado->cost_env         += $direccion->cost_por_env;
                            $armado->cant_direc_carg  += $direccion->cant;
                            $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                            $armado->save();
                            $this->calculaValoresCotizacion($cotizacion);
                            $cotizacion->save();
                        }
                    }
                    // return $variable;
                    return response()->json(['data'=>[],"message"=>"Se ha agregado la dirección correctamente","code"=>200]);
                }else{
                    return response()->json(['data'=>[],"message"=>"No se pueden ingresar arcones a la cotización","code"=>200]);
                }
            }else{
                return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
    public function agregardireccionescontarifaunica(Request $request){
        try {
            $validated = $request->validate([
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                $armado = CotizacionArmados::with('cotizacion')->findOrFail($request->id_registro_cot_arm);
                $cotizacion = $armado->cotizacion;
                // return $cotizacion;
                // $armado = CotizacionArmados::with('cotizacion')->where('cotizacion_id',$request->id)->get();
                // $cotizacion = cotizaciones::where('id',$request->id)->first();
                if($armado->cotizacion->estat=='Abierta'){
                    if($request->cantidad>0 && $request->cantidad <= $armado->cant && $armado->cant_direc_carg < $armado->cant){
                        $direccion = new CotizacionArmadoTieneDirecciones();
                        $direccion->seg                       = 'No';
                        $direccion->est                       = $request->est;
                        $direccion->armado_id                 = $armado->id;
                        $direccion->created_at_dir            = $request->created_at_dir;
                        $direccion->cant                      = $request->cantidad;
                        $direccion->tam                       = $armado->tam;
                        if(strlen($request->detalles_de_la_ubicacion) == 0){
                            $direccion->detalles_de_la_ubicacion  ='Sin detalles';
                        }else{
                            $direccion->detalles_de_la_ubicacion  = $request->detalles_de_la_ubicacion;
                        }
                        if ($direccion->est == 'Tarifa única (Cdmx y AM)') {
                            $direccion->for_loc               = 'Local';
                            $direccion->met_de_entreg         = 'Transporte interno de la empresa';
                            $direccion->tiemp_ent             = 'De 1 a 4 dias';
                            $direccion->cost_por_env = 250.00 * $request->cantidad;
                            $direccion->cost_tam_caj = 0.00;
                        }elseif ($direccion->est == 'Tarifa única (Varios estados)' ){
                            $direccion->for_loc               = 'Foráneo';
                            $direccion->tiemp_ent             = 'De 2 a 10 dias';
                            $direccion->met_de_entreg         = 'Transportes Ferro';
                            $direccion->cost_por_env = 250.00 * $request->cantidad;
                            if($direccion->tam == 'Mediano'){
                                $direccion->cost_tam_caj = 30.00;
                            }elseif($direccion->tam == 'Chico'){
                                $direccion->cost_tam_caj = 20.00;
                            }elseif($direccion->tam == 'Grande'){
                                $direccion->cost_tam_caj = 40.00;
                            }
                        }
                        // if ($direccion->cant > 1) {
                        //     $direccion->cost_por_env += $direccion->cost_por_env *  $request->cantidad;
                        // }
                        if($direccion->cost_tam_caj > 0){
                            $direccion->cost_por_env += $direccion->cost_tam_caj *  $request->cantidad;
                        }
                        $direccion->save();
                        $armado->cost_env         += $direccion->cost_por_env;
                        $armado->cant_direc_carg  += $direccion->cant;
                        $armado                   = $this->sumaValoresArmadoCotizacion($armado);
                        $armado->save();
                        $this->calculaValoresCotizacion($cotizacion);
                        $cotizacion->save();
                        return response()->json(['data'=>[],"message"=>"Se ha agregado la dirección correctamente","code"=>200]);
                    }else{
                        return response()->json(['data'=>[],"message"=>"No se pueden ingresar arcones a la cotización","code"=>200]);
                    }
                }else{
                    return response()->json(['data'=>[],"message"=>"La cotización no se encuentra abierta","code"=>200]);
                }
            }else{
                return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }

}

