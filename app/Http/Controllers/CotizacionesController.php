<?php

namespace App\Http\Controllers;

use App\Models\pedido;
use App\Models\pedidoarmado;
use App\Models\cotizaciones;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\Verifytoken;
use App\Models\carmados;
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,'.$request->email,
            'nombre'=>'required',
            'apellido'=>'required',
            'tel_mov'=>'required',
            'token'=>'required'
        ]);
      //  return $request;
        if($this->verifica($request->token)){
           // return "si es valido";
            $claveinicial="CanastasYArcones";
            $user= new User();
            $user->nom=$request->nombre;
            $user->email=$request->email;
            $user->apell=$request->apellido;
            $user->acceso=2;
            $user->email_registro=$request->email;
            $user->tel_mov=$request->tel_mov;
            $user->password = bcrypt($claveinicial);
            $user->asignado_us="API";
            $user->created_at= date('Y-m-d H:i:s');
            $user->created_at_us= date('Y-m-d H:i:s');
            $user->save();
            DB::table('model_has_roles')->insert([
                'role_id' => '2',
                'model_type' => 'App\User',
                'model_id'=> $user->id
            ]);
            return response()->json(['data'=>[],"message"=>"usuario regristrado con éxito","code"=>201]);
        }else{
            return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
        }
    }
    public function pedido(Request $request){
        //return $request;
        $validated = $request->validate([
            'email' => 'required|email',
            'token'=>'required'
        ]);
        if($this->verifica($request->token)){
            $data=[];
            // $data[]
            //return $this->verifica("3RRZ4Czrz9KDMMG5Xo3IzaCU5WV7ZluKDYhNiw9lNZvUdRgFDnNUePyByJF8LVgIXPEE5gzJgQrzqa5RFaPu69oK893wNFWpY6xEoVLtzmNH3seFecjKBCHrjJXkTFo0DjDrR13NKF1R4uTxhxDnSw");
            $user= User::where('email', '=', $request->email)->first();//Busca usuario con el email, regresa datos generales del usuario
            if($user){//
                $data['user']['email']=$user->email_registro;
                $data['pedidos']=[];
                $ped=pedido::where('user_id',$user->id)->get();   //->whereDate('created_at',">", '2021-06-31')->get();  ////todas las cotizaciones
                for($a=0;$a<count($ped);$a++){//recolecta los datos de la cotizacion de un usuario especifico
                    if($ped[$a]["deleted_at"]==NULL){
                        $item=[];
                        $item['Numero_Pedido']=$ped[$a]["num_pedido"];
                        $item['Estatus_Almacen']=$ped[$a]['estat_alm'];
                        $item['Estatus_Produccion']=$ped[$a]['estat_produc'];
                        $item['Fecha_Estatus_Produccion']=$ped[$a]['fech_estat_produc'];   
                        $item['Estatus_Logistica']=$ped[$a]['estat_log'];
                        $item['Arcones']=[];
                        $armados=pedidoarmado::where('pedido_id',$ped[$a]->id)->get();
                            for($b=0;$b<count($armados);$b++){//Trae los armados y los datos de cada uno de ellos
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
                        array_push($data['pedidos'],$item);
                    }
                }
                return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
            }else{
                //Si no se encuentra un usuario regresa un 404
                return response()->json(['data'=>[],"message"=>"usuario no encontrado","code"=>404]);
            }
        }else{
            //Si no es correcto el Token regresa un 403
            return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
        }
    }
}
