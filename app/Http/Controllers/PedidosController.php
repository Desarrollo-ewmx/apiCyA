<?php

namespace App\Http\Controllers;

use App\Models\pedidoarmado;
use App\Models\pedido;
use App\Models\cotizaciones;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Verifytoken;
// Notifications
use App\Notifications\NotificacionRegistrarPedido;

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
    public function pedido(Request $request)
    {
        try {
            //code...
            //return $request;
            $validated = $request->validate([
                'email' => 'required|email',
                'token' => 'required'
            ]);
            if ($this->verifica($request->token)) {
                $data = [];
                // $data[]
                //return $this->verifica("3RRZ4Czrz9KDMMG5Xo3IzaCU5WV7ZluKDYhNiw9lNZvUdRgFDnNUePyByJF8LVgIXPEE5gzJgQrzqa5RFaPu69oK893wNFWpY6xEoVLtzmNH3seFecjKBCHrjJXkTFo0DjDrR13NKF1R4uTxhxDnSw");
                $user = User::where('email', '=', $request->email)->first(); //Busca usuario con el email, regresa datos generales del usuario
                if ($user) { //
                    $data['user']['email'] = $user->email_registro;
                    $data['pedidos'] = [];
                    $ped = pedido::where('user_id', $user->id)->get();   //->whereDate('created_at',">", '2021-06-31')->get();  ////todas las cotizaciones
                    for ($a = 0; $a < count($ped); $a++) { //recolecta los datos de la cotizacion de un usuario especifico
                        if ($ped[$a]["deleted_at"] == NULL) {
                            $item = [];
                            $item['numero_pedido'] = $ped[$a]["num_pedido"];
                            $item['estatus_almacen'] = $ped[$a]['estat_alm'];
                            $item['estatus_produccion'] = $ped[$a]['estat_produc'];
                            $item['fecha_estatus_produccion'] = $ped[$a]['fech_estat_produc'];
                            $item['estatus_logistica'] = $ped[$a]['estat_log'];
                            $item['arcones'] = [];
                            $armados = pedidoarmado::where('pedido_id', $ped[$a]->id)->get();
                            for ($b = 0; $b < count($armados); $b++) { //Trae los armados y los datos de cada uno de ellos
                                $arm = [];
                                $arm['sku'] = $armados[$b]['sku'];
                                $arm['nombre'] = $armados[$b]['nom'];
                                $arm['gama'] = $armados[$b]['gama'];
                                $arm['cantidad'] = $armados[$b]['cant'];
                                $arm['precio_unitario_sin_iva'] = $armados[$b]['prec_redond'];
                                $arm['total'] = $armados[$b]['tot'];
                                $arm['tipo'] = $armados[$b]['tip'];
                                array_push($item['arcones'], $arm);
                            }
                            array_push($data['pedidos'], $item);
                        }
                    }
                    return response()->json(['data' => $data, "message" => "success", "code" => 200]);
                } else {
                    //Si no se encuentra un usuario regresa un 404
                    return response()->json(['data' => $request->email, "message" => "usuario no encontrado", "code" => 404]);
                }
            } else {
                //Si no es correcto el Token regresa un 403
                return response()->json(['data' => [], "message" => "token invalido", "code" => 403]);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message" => "error", 'error' => $th->status]);
        }
    }

    public function aprobarcotizacion(Request $request)
    {
        $cotizacion = Cotizaciones::with('armados')->where('id', $request->id)->first();
        $armados_cotizacion = $cotizacion->armados()->with('productos', 'direcciones')->get();
        $nom_tabla = (new \App\Models\Producto())->getTable(); //Literalmente solo obtiene el nombre de la tabla en el modelo
        if ($cotizacion->tot == 0) {
            return response()->json(['data' => [], "message" => "La cotización seleccionada no puede convertirce en un pedido ya que el total es de $0.00", "code" => 404]);
        }
        $pedido = new \App\Models\Pedido();
        $pedido->num_pedido       = $this->sumaUnoALaUltimaSerie();
        $pedido->serie            = substr($pedido->num_pedido, 0, 4);
        $pedido->coment_vent      = $cotizacion->coment_vent;
        $pedido->estat_alm        = 'Pendiente';
        $pedido->cot_gen          = $cotizacion->serie;
        $pedido->ult_let          = 'A';
        $pedido->user_id          = $cotizacion->user_id;
        $pedido->tot_de_arm       = $cotizacion->tot_arm;
        if ($pedido->tot_de_arm >= 11) {
            $pedido->bod              = 'Temas';
        } else {
            $pedido->bod              = 'Naucalpan';
        }
        $pedido->mont_tot_de_ped  = $cotizacion->tot;
        $pedido->gratis           = $this->elPedidoEsDeRegalo($cotizacion, $armados_cotizacion);
        $pedido->estat_vent_arm   = 'Armados cargados';
        $pedido->con_iva          = $cotizacion->con_iva;
        $pedido->asignado_ped     = $cotizacion->email_registro;
        $pedido->created_at_ped   = $cotizacion->email_registro;
        return $pedido;
        $pedido->save();

        //  $contador2              = 0;
        $contador3              = 0;
        //  $productos_armado       = NULL;
        $up_stock_productos     = NULL;
        $up_vendidos_productos  = NULL;
        $ids                    = NULL;
        $direcciones            = NULL;
        $modificado             = null;
        foreach ($armados_cotizacion as $armado_cotizacion) {
            if ($armado_cotizacion->cant != $armado_cotizacion->cant_direc_carg) {
                return response()->json(['data' => [], "message" => 'No se han registrado todas las direcciones al armado ' . $armado_cotizacion->nom, "code" => 404]);
            }

            // se guardan los datos a los que se cotizo
            $armado_cotizacion->prec_cot       = $armado_cotizacion->prec_redond;
            $armado_cotizacion->sub_tot_cot    = $armado_cotizacion->sub_total;
            $armado_cotizacion->desc_cot       = $armado_cotizacion->desc;
            $armado_cotizacion->iva_cot        = $armado_cotizacion->iva;
            $armado_cotizacion->tot_cot        = $armado_cotizacion->tot;

            $armado_cotizacion->save();
            // dd($armado_cotizacion);

            // REGISTRA LOS ARMADOS AL PEDIDO
            $armado_pedido               = new \App\Models\PedidoArmado();
            $armado_pedido->img_rut      = $armado_cotizacion->img_rut;
            $armado_pedido->img_nom      = $armado_cotizacion->img_nom;

            // DEFINE SI EL PEDIDO ES FORANEO O NO
            // if($modificado == null) {
            //   $modificado = $this->elPedidoTieneDireccionesForaneas($pedido, $armado_cotizacion, $modificado);
            //   if($modificado == true) {
            //     $armado_pedido->for_loc    = config('opcionesSelect.select_foraneo_local.Foráneo');
            //   }
            // }

            // Se define si el pedido es foráneo o no y se le asigna el valor -for_loc a los armados
            foreach ($armado_cotizacion->direcciones as $dir) {
                if ($modificado == null) {
                    $modificado = $this->elPedidoTieneDireccionesForaneas($pedido, $armado_cotizacion, $modificado);
                }
                $armado_pedido->for_loc = $dir->for_loc;
            }

            $armado_pedido->cod          = $this->sumaUnoALaUltimaLetraYArmadosCargados($pedido, $armado_cotizacion->cant);
            $armado_pedido->cant         = $armado_cotizacion->cant;
            $armado_pedido->tip          = $armado_cotizacion->tip;
            $armado_pedido->nom          = $armado_cotizacion->nom;
            $armado_pedido->sku          = $armado_cotizacion->sku;
            $armado_pedido->gama         = $armado_cotizacion->gama;
            $armado_pedido->prec         = $armado_cotizacion->prec_redond;
            $armado_pedido->tam          = $armado_cotizacion->tam;
            $armado_pedido->pes          = $armado_cotizacion->pes;
            $armado_pedido->alto         = $armado_cotizacion->alto;
            $armado_pedido->ancho        = $armado_cotizacion->ancho;
            $armado_pedido->largo        = $armado_cotizacion->largo;
            $armado_pedido->es_de_regalo = $armado_cotizacion->es_de_regalo;
            if ($armado_cotizacion->es_de_regalo == 'Si') {
                $armado_pedido->aut   = config('app.pendiente');
            }
            //        $armado_pedido->coment_vent  = $request->comentarios_ventas;
            $armado_pedido->pedido_id    = $pedido->id;
            $armado_pedido->created_at_ped_arm = $pedido->created_at_ped;
            $armado_pedido->save();

            // REGISTRA LOS PRODUCTOS AL ARMADO
            foreach ($armado_cotizacion->productos as $producto) {
                // PREPARA LA CONSULTA UPDATE MASIVA PARA DISMINUIR EL STOCK DEL PRODUCTO QUE TIENE EL ARMADO
                if ($pedido->bod == 'Naucalpan') {
                    $up_stock_productos     .= ' WHEN ' . $producto->id_producto . ' THEN stock-' . $producto->cant * $armado_pedido->cant;
                } else if ($pedido->bod == 'Temas') {
                    $up_stock_productos     .= ' WHEN ' . $producto->id_producto . ' THEN stock_temas-' . $producto->cant * $armado_pedido->cant;
                }
                // $up_stock_productos     .= ' WHEN '. $producto->id_producto. ' THEN stock-'. $producto->cant * $armado_pedido->cant;
                $up_vendidos_productos  .= ' WHEN ' . $producto->id_producto . ' THEN vend+' . $producto->cant * $armado_pedido->cant;
                $ids                    .= $producto->id_producto . ',';

                // REGISTRA LOS PRODUCTOS AL ARMADO
                $productos_armado_ped = new \App\Models\PedidoArmadoTieneProducto();
                $productos_armado_ped->id_producto      = $producto->id_producto;
                $productos_armado_ped->cant             = $producto->cant;
                $productos_armado_ped->produc           = $producto->produc;
                $productos_armado_ped->sku              = $producto->sku;
                $productos_armado_ped->pedido_armado_id = $armado_pedido->id;
                $productos_armado_ped->created_at       = date("Y-m-d h:i:s");
                $productos_armado_ped->save();
                $productos_armado_ped->productos_original()->attach($producto->id_producto);
            }

            // disminuir el stock de armados
            // DISMINUYE EL STOCK DEL PRODUCTO QUE TIENE EL ARMADO
            if ($pedido->bod == 'Naucalpan') {
                if ($up_stock_productos != NULL) {
                    $ids = substr($ids, 0, -1);
                    DB::UPDATE("UPDATE " . $nom_tabla . " SET stock = CASE id" . $up_stock_productos . " END, vend = CASE id" . $up_vendidos_productos . " END WHERE id IN (" . $ids . ")");
                }
            } else if ($pedido->bod == 'Temas') {
                if ($up_stock_productos != NULL) {
                    $ids = substr($ids, 0, -1);
                    DB::UPDATE("UPDATE " . $nom_tabla . " SET stock_temas = CASE id" . $up_stock_productos . " END, vend = CASE id" . $up_vendidos_productos . " END WHERE id IN (" . $ids . ")");
                }
            }

            $up_stock_productos     = NULL;
            $up_vendidos_productos  = NULL;
            $ids = NULL;

            // REGISTRA LAS DIRECCIONES AL ARMADO
            foreach ($armado_cotizacion->direcciones as $direccion) {
                // separar las direcciones si es que son de Tarífa única
                if ($direccion->est != "Tarifa única (Varios estados)") {
                    $direcciones[$contador3]['cod']                       = $this->sumaUnoALaUltimaLetraDireccionesCargadas($armado_pedido);
                    $direcciones[$contador3]['cant']                      = $direccion->cant;
                    $direcciones[$contador3]['tip_tarj_felic']            = 'Estandar';
                    $direcciones[$contador3]['met_de_entreg']             = $direccion->met_de_entreg;

                    // DEFINE EL NUEVO VALOR DEL ESTADO SIN LA CAMPITAL
                    $nuevo_est = null;
                    for ($i = 0; $i < strlen($direccion->est); $i++) {
                        if ($direccion->est[$i] == '(') {
                            break;
                        }
                        $nuevo_est .= $direccion->est[$i];
                    }
                    $direcciones[$contador3]['est']                       = $nuevo_est;

                    $direcciones[$contador3]['for_loc']                   = $direccion->for_loc;
                    $direcciones[$contador3]['detalles_de_la_ubicacion']  = $direccion->detalles_de_la_ubicacion;
                    $direcciones[$contador3]['tip_env']                   = $direccion->tip_env;
                    $direcciones[$contador3]['cost_por_env']              = $direccion->cost_por_env;

                    if ($direccion->cost_tam_caj > 0.00) {
                        $direcciones[$contador3]['caj']              = 'Con caja (' . $direccion->tam . ')';
                    } else {
                        $direcciones[$contador3]['caj']              = 'En canasta';
                    }

                    $direcciones[$contador3]['created_at_direc_arm']      = $cotizacion->email_registro;

                    // Si el metodo de entrega es "Entregado en bodega" se llenara la demas informacion con la de la empresa
                    if ($direccion->met_de_entreg == 'Entregado en bodega naucalpan') {
                        $direcciones[$contador3]['nom_ref_uno'] = 'Encargado de logística';
                        $direcciones[$contador3]['lad_mov']     = '1';
                        $direcciones[$contador3]['tel_mov']     = '00000000';
                        $direcciones[$contador3]['calle']       = 'Blvrd Manuel Ávila Camacho';
                        $direcciones[$contador3]['no_ext']      = '80';
                        $direcciones[$contador3]['no_int']      = '204';
                        $direcciones[$contador3]['pais']        = 'México';
                        $direcciones[$contador3]['ciudad']      = 'Estado de México';
                        $direcciones[$contador3]['col']         = 'El Parque';
                        $direcciones[$contador3]['del_o_munic'] = 'Naucalpan de Juárez';
                        $direcciones[$contador3]['cod_post']    = '53398';

                        // ACTUALIZA EL ESTATUS DE LAS DIRECCIONES DEL PEDIDO
                        $this->direccionArmadoRepo->estatusDireccionesDetalladas($direcciones[$contador3]['cant'], $armado_pedido, 'No');
                    } elseif ($direccion->met_de_entreg == 'Entregado en bodega temascalapa') {
                        $direcciones[$contador3]['nom_ref_uno'] = 'Encargado de logística';
                        $direcciones[$contador3]['lad_mov']     = '1';
                        $direcciones[$contador3]['tel_mov']     = '00000000';
                        $direcciones[$contador3]['calle']       = 'San Francisco';
                        $direcciones[$contador3]['no_ext']      = '33';
                        $direcciones[$contador3]['no_int']      = '';
                        $direcciones[$contador3]['pais']        = 'México';
                        $direcciones[$contador3]['ciudad']      = 'Estado de México';
                        $direcciones[$contador3]['col']         = 'Barrio San Miguel';
                        $direcciones[$contador3]['del_o_munic'] = 'Temascalapa';
                        $direcciones[$contador3]['cod_post']    = '55980';

                        $this->direccionArmadoRepo->estatusDireccionesDetalladas($direcciones[$contador3]['cant'], $armado_pedido, 'No');
                    } else {
                        $direcciones[$contador3]['nom_ref_uno'] = null;
                        $direcciones[$contador3]['lad_mov']     = null;
                        $direcciones[$contador3]['tel_mov']     = null;
                        $direcciones[$contador3]['calle']       = null;
                        $direcciones[$contador3]['no_ext']      = null;
                        $direcciones[$contador3]['no_int']      = null;
                        $direcciones[$contador3]['pais']        = null;
                        $direcciones[$contador3]['ciudad']      = $nuevo_est;
                        $direcciones[$contador3]['col']         = null;
                        $direcciones[$contador3]['del_o_munic'] = null;
                        $direcciones[$contador3]['cod_post']    = null;
                    }

                    $direcciones[$contador3]['pedido_armado_id']          = $armado_pedido->id;
                    $direcciones[$contador3]['created_at']                = date("Y-m-d h:i:s");
                    $contador3 += 1;
                } else {
                    // SEPRARA LAS DIRECCIONES CUANDO SEAN DE TARIFA ÚNCA
                    $nuevo_est = "Tarifa única";
                    // calcular el costo de envio para tarifa única.
                    $costoEnv = $direccion->cost_por_env / $direccion->cant;
                    $cantidadDireccion = $direccion->cant;
                    $num = 1;

                    for ($i = 0; $i < $cantidadDireccion; $i++) {
                        $direcciones[$contador3]['est']                       = $nuevo_est;
                        $direcciones[$contador3]['cod']                       = $this->sumaUnoALaUltimaLetraDireccionesCargadas($armado_pedido);
                        $direcciones[$contador3]['cant']                      = $num;
                        $direcciones[$contador3]['tip_tarj_felic']            = 'Estandar';
                        $direcciones[$contador3]['met_de_entreg']             = $direccion->met_de_entreg;
                        $direcciones[$contador3]['for_loc']                   = $direccion->for_loc;
                        $direcciones[$contador3]['detalles_de_la_ubicacion']  = $direccion->detalles_de_la_ubicacion;
                        $direcciones[$contador3]['tip_env']                   = $direccion->tip_env;
                        $direcciones[$contador3]['cost_por_env']              = $costoEnv;

                        if ($direccion->cost_tam_caj > 0.00) {
                            $direcciones[$contador3]['caj']              = 'Con caja (' . $direccion->tam . ')';
                        } else {
                            $direcciones[$contador3]['caj']              = 'En canasta';
                        }

                        $direcciones[$contador3]['created_at_direc_arm']      = $cotizacion->email_registro;
                        $direcciones[$contador3]['nom_ref_uno'] = null;
                        $direcciones[$contador3]['lad_mov']     = null;
                        $direcciones[$contador3]['tel_mov']     = null;
                        $direcciones[$contador3]['calle']       = null;
                        $direcciones[$contador3]['no_ext']      = null;
                        $direcciones[$contador3]['no_int']      = null;
                        $direcciones[$contador3]['pais']        = null;
                        $direcciones[$contador3]['ciudad']      = $nuevo_est;
                        $direcciones[$contador3]['col']         = null;
                        $direcciones[$contador3]['del_o_munic'] = null;
                        $direcciones[$contador3]['cod_post']    = null;
                        $direcciones[$contador3]['pedido_armado_id']          = $armado_pedido->id;
                        $direcciones[$contador3]['created_at']                = date("Y-m-d h:i:s");
                        $contador3 += 1;
                    }
                }
            }
        }
        if ($direcciones != null) {
            \App\Models\PedidoArmadoTieneDireccion::insert($direcciones);
        }

        // CORREO ALTA DE PEDIDO
        $cliente    = $this->usuarioRepo->getUsuarioFindOrFail($pedido->user_id, []);
        $plantilla  = $this->plantillaRepo->plantillaFindOrFailById($this->sistemaRepo->datos('plant_vent_reg_ped'));
        // Envió de correo electrónico
        $cliente->notify(new NotificacionRegistrarPedido($cliente, $pedido, $plantilla));

        // CIERRA LA COTIZACIÓN
        $cotizacion->estat = config('app.aprobada');
        $cotizacion->num_pedido_gen = $pedido->num_pedido;
        $cotizacion->save();

        // SI CUMPLE CON LA CONFICION SE MODIFICA EL ESTATUS DE PRODUCCIÓN Y ALMACEN PARA QUE LO PUEDAN VISUALIZAR
        if ($pedido->mont_tot_de_ped <= 25000) {
            $this->pagoRepo->modificarEstatusProduccionYAlmacen($pedido);
        }

        DB::commit();
        return (object) [
            'cotizacion'  => $cotizacion,
            'pedido'      => $pedido
        ];
    }

    public function sumaUnoALaUltimaSerie()
    {
        $serie = Serie::where('input', 'Pedidos (Serie)')->where('vista', 'CYA-')->first();
        if ($serie == null) {
            return response()->json(['data' => [], "message" => "No se ha definido una serie por default", "code" => 404]);
        }
        $serie->ult_ser += 1;
        $serie->save();
        return $serie->vista . $serie->ult_ser;
    }
    public function elPedidoEsDeRegalo($cotizacion, $armados_cotizacion)
    {
        if ($armados_cotizacion->where('es_de_regalo', 'Si')->sum('cant')  ==  $cotizacion->tot_arm) {
            return 'Si';
        } else {
            return 'No';
        }
    }
    public function elPedidoTieneDireccionesForaneas($pedido, $armado_cotizacion, $modificado)
    {
        if ($armado_cotizacion->direcciones->where('for_loc', 'Foráneo')->count() > 0) {
            // config('opcionesSelect.select_foraneo_local.Foráneo')
            $pedido->foraneo = 'Si';
            $pedido->save();
            $modificado = true;
        }
        return $modificado;
    }
    public function sumaUnoALaUltimaLetraYArmadosCargados($pedido, $cantidad) {
        $pedido->ult_let  = ++ $pedido->ult_let;
        $pedido->arm_carg += $cantidad;
        $pedido->save();
        return $pedido->num_pedido.'-'.$pedido->ult_let;
    }
    
}
