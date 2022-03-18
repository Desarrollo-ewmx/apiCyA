<?php

namespace App\Http\Controllers;

use App\Models\cotizaciones;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\Verifytoken;
use App\Models\carmados;
use App\Models\Serie;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class CotizacionesController extends Controller
{
    use Verifytoken;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //return $request;
        $validated = $request->validate([
            'email' => 'required|email',
            'token'=>'required'
        ]);
        //  return $request;
        if($this->verifica($request->token)){
            $data=[];
        // $data[]
        //return $this->verifica("3RRZ4Czrz9KDMMG5Xo3IzaCU5WV7ZluKDYhNiw9lNZvUdRgFDnNUePyByJF8LVgIXPEE5gzJgQrzqa5RFaPu69oK893wNFWpY6xEoVLtzmNH3seFecjKBCHrjJXkTFo0DjDrR13NKF1R4uTxhxDnSw");
        $user= User::where('email', '=', $request->email)->first();
        if($user){
            $data['user']['nombre']=$user->nom;
            $data['user']['apellido']=$user->apell;
            $data['user']['email']=$user->email_registro;
            $data['user']['tel_fijo']=$user->tel_fij;
            $data['user']['movil']=$user->tel_mov;
            $data['cotizaciones']=[];
            $cot=cotizaciones::where('user_id',$user->id)->whereDate('created_at',">", '2021-06-31')->get();  ////todas las cotizaciones
            for($a=0;$a<count($cot);$a++){
                if($cot[$a]["deleted_at"]==NULL){
                    $item=[];
                    $item['Total']=$cot[$a]['tot'];
                    $item['Serie']=$cot[$a]['serie'];
                    $item['Arcones_Totales']=$cot[$a]['tot_arm'];
                    $item['Fecha']=$cot[$a]['created_at'];
                    $item['Arcones']=[];
                    $armados=carmados::where('cotizacion_id',$cot[$a]->id)->get();
                    for($b=0;$b<count($armados);$b++){
                        $arm=[];
                        $arm['Sku']=$armados[$b]['sku'];
                        $arm['Nombre']=$armados[$b]['nom'];
                        $arm['Gama']=$armados[$b]['gama'];
                        $arm['Cantidad']=$armados[$b]['cant'];
                        $arm['Precio_Unitario_Sin_Iva']=$armados[$b]['prec_redond'];
                        $arm['Total']=$armados[$b]['tot'];
                        $arm['Tipo']=$armados[$b]['tip'];
                        array_push($item['Arcones'],$arm);
                    }
                array_push($data['cotizaciones'],$item);
                }
            }
        return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
        }else{
            return response()->json(['data'=>[],"message"=>"usuario no encontrado","code"=>404]);
        }
        }else{
            return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'estat'=> 'required',
                'desc_cot'=> 'required',
                'tot_arm'=> 'required',
                'cost_env'=> 'required',
                'desc'=> 'required',
                'sub_total'=> 'required',
                'iva'=> 'required',
                'com'=> 'required',
                'tot'=> 'required',
                'token'=>'required'
            ]);
            
            if($this->verifica($request->token)){
                if($validated){
                    $coti = Cotizaciones::find($request->id);
                    // $coti = Cotizaciones::find(2);
                    // return $coti;
                    $coti->estat = $request->has('estat') ? $request->get('estat') : $coti->estat;
                    $coti->desc_cot = $request->has('desc_cot') ? $request->get('desc_cot') : $request->desc_cot;
                    // if($coti->desc_cot == ''){
                    //     $coti->desc_cot = 'Sin descripcion';
                    // }
                    $coti->tot_arm = $request->has('tot_arm') ? $request->get('tot_arm') : $request->tot_arm;
                    $coti->cost_env = $request->has('cost_env') ? $request->get('cost_env') : $request->cost_env;
                    $coti->desc = $request->has('desc') ? $request->get('desc') : $request->desc;
                    $coti->sub_total = $request->has('sub_total') ? $request->get('sub_total') : $request->sub_total;
                    $coti->iva = $request->has('iva') ? $request->get('iva') : $request->iva;
                    // $coti->com = $request->has('com') ? $request->get('com') : $request->com;
                    $coti->tot = $request->has('tot') ? $request->get('tot') : $request->tot;
                    // return $coti; 
                    $coti->save();
                    return response()->json(['data'=>[],"message"=>"Cotización actualizada con éxito","code"=>201],201);
                }
            }else{
                    return response()->json(['data'=>[],"message"=>"token invalido","code"=>403],403);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th],422);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'estat'=> 'required',
                'desc_cot'=> 'nullable',
                'tot_arm'=> 'required',
                'cost_env'=> 'required',
                'desc'=> 'required',
                'sub_total'=> 'required',
                'iva'=> 'required',
                'com'=> 'required',
                'tot'=> 'required',
                'user_id'=> 'required',
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                if($validated){
                    $fecha_actual = date("d-m-Y");
                    $coti = new Cotizaciones();
                    $cot = Cotizaciones::orderby('created_at', 'desc')->first();
                    $str = substr($cot->serie, 4);
                    $seriemasuno = (int)$str+=1;
                    $coti->serie = $cot->ser.(String)$seriemasuno;
                    $coti->ser = $cot->ser;
                    $coti->estat = 'Abierta';
                    $coti->valid = date('Y-m-d H:i:s',strtotime($fecha_actual."+ 1 week"));
                    $coti->desc_cot = $request->desc_cot;
                    if($coti->desc_cot == ''){
                        $coti->desc_cot = 'Sin descripcion';
                    }
                    $coti->tot_arm = $request->tot_arm;
                    $coti->cost_env = $request->cost_env;
                    $coti->desc = $request->desc;
                    $coti->sub_total = $request->sub_total;
                    $coti->iva = $request->iva;
                    $coti->com = $request->com;
                    $coti->tot = $request->tot;
                    $coti->user_id = $request->user_id;
                    $coti->asignado_cot = 'Apiecommerce';
                    $coti->created_at_cot = 'Apiecommerce';
                    $coti->created_at = date('Y-m-d H:i:s');
                    $coti->updated_at = date('Y-m-d H:i:s');
                    $serie = Serie::where('input', '=', 'Cotizaciones (Serie)' )->first();                    
                    $serie->ult_ser += 1;
                    $serie->save();
                    $coti->save();
                    return response()->json(['data'=>[],"message"=>"Cotización regristrada con éxito","code"=>201]);
                }
            }else{
                    return response()->json(['data'=>[],"message"=>"token invalido","code"=>403],403);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th->status],422);
        }
    }
    public function ver(Request $request){
        // return cotizaciones::where('id','=',$id)->get();
        // //return $request;
        $validated = $request->validate([
            'user_id'=>'required',
            'token'=>'required'
        ]);
        // return $request;
        if($this->verifica($request->token)){
            $data['cotizaciones']=[];
            $cot=cotizaciones::where('user_id',$request->user_id)->get();  ////todas las cotizaciones
            if($cot){
                for($a=0;$a<count($cot);$a++){
                    if($cot[$a]["deleted_at"]==NULL){
                        $item=[];
                        $item['Total']=$cot[$a]['tot'];
                        $item['Serie']=$cot[$a]['serie'];
                        $item['Arcones_Totales']=$cot[$a]['tot_arm'];
                        $item['Fecha']=$cot[$a]['created_at'];
                        $item['Arcones']=[];
                        $armados=carmados::where('cotizacion_id',$cot[$a]->id)->get();
                        for($b=0;$b<count($armados);$b++){
                            $arm=[];
                            $arm['Sku']=$armados[$b]['sku'];
                            $arm['Nombre']=$armados[$b]['nom'];
                            $arm['Gama']=$armados[$b]['gama'];
                            $arm['Cantidad']=$armados[$b]['cant'];
                            $arm['Precio_Unitario_Sin_Iva']=$armados[$b]['prec_redond'];
                            $arm['Total']=$armados[$b]['tot'];
                            $arm['Tipo']=$armados[$b]['tip'];
                            array_push($item['Arcones'],$arm);
                        }
                    array_push($data['cotizaciones'],$item);
                    }
                }   
            return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
        }else{
            return response()->json(['data'=>[],"message"=>"usuario no encontrado","code"=>404]);
        }
        }else{
            return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
        }
    }
}
