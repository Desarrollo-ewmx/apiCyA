<?php

namespace App\Http\Controllers;

use App\Models\pedidoarmado;
use App\Models\pedido;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\Verifytoken;

class PedidosController extends Controller
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(pedido $pedido)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pedido $pedido)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(pedido $pedido)
    {
        //
    }
    public function pedido(Request $request){
        try {
            //code...
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
                            $item['numero_pedido']=$ped[$a]["num_pedido"];
                            $item['estatus_almacen']=$ped[$a]['estat_alm'];
                            $item['estatus_produccion']=$ped[$a]['estat_produc'];
                            $item['fecha_estatus_produccion']=$ped[$a]['fech_estat_produc'];   
                            $item['estatus_logistica']=$ped[$a]['estat_log'];
                            $item['arcones']=[];
                            $armados=pedidoarmado::where('pedido_id',$ped[$a]->id)->get();
                                for($b=0;$b<count($armados);$b++){//Trae los armados y los datos de cada uno de ellos
                                    $arm=[];
                                    $arm['sku']=$armados[$b]['sku'];
                                    $arm['nombre']=$armados[$b]['nom'];
                                    $arm['gama']=$armados[$b]['gama'];
                                    $arm['cantidad']=$armados[$b]['cant'];
                                    $arm['precio_unitario_sin_iva']=$armados[$b]['prec_redond'];
                                    $arm['total']=$armados[$b]['tot'];
                                    $arm['tipo']=$armados[$b]['tip'];
                                    array_push($item['arcones'],$arm);
                                }
                            array_push($data['pedidos'],$item);
                        }
                    }
                    return response()->json(['data'=>$data,"message"=>"success","code"=>200]);
                }else{
                    //Si no se encuentra un usuario regresa un 404
                    return response()->json(['data'=>$request->email,"message"=>"usuario no encontrado","code"=>404]);
                }
            }else{
                //Si no es correcto el Token regresa un 403
                return response()->json(['data'=>[],"message"=>"token invalido","code"=>403]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th->status]);
        }
    }
}
