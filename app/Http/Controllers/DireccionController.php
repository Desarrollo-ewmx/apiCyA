<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Direccion;
use App\Traits\Verifytoken;

class DireccionController extends Controller
{
    use Verifytoken;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $direccion = Direccion::get();
        $email = Direccion::where('user_id', '=', $request->id)->get(); 
        return $email;
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
            $nuevadir = new Direccion();
            $nuevadir->nom_ref_uno = $request->nom_ref_uno;
            $nuevadir->nom_ref_dos = $request->nom_ref_dos;
            $nuevadir->lad_fij = $request->lad_fij;
            $nuevadir->tel_fij = $request->tel_fij;
            $nuevadir->ext = $request->ext;
            $nuevadir->lad_mov = $request->lad_mov;
            $nuevadir->tel_mov = $request->tel_mov;
            $nuevadir->calle = $request->calle;
            $nuevadir->no_ext = $request->no_ext;
            $nuevadir->no_int = $request->no_int;
            $nuevadir->pais = $request->pais;
            $nuevadir->ciudad = $request->ciudad;
            $nuevadir->col = $request->col;
            $nuevadir->del_o_munic = $request->del_o_munic;
            $nuevadir->cod_post = $request->cod_post;
            $nuevadir->ref_zon_de_entreg = $request->ref_zon_de_entreg;
            $nuevadir->user_id = $request->user_id;
            $user=DB::table('users')->where("id",$request->user_id)->first();
            $nuevadir->created_at_direc = $user->email_registro;
            $nuevadir->updated_at_direc = $user->email_registro;
            $nuevadir->save();
            return response()->json(['data'=>$nuevadir,"message"=>"El registro de la dirección fue exitoso","code"=>200]);
        } catch (\Throwable $th) {
            if (isset($th)) {
            return response(["message"=>"error", 'error'=>$th->getMessage(),'code'=>404]);
            }else {
                return response(["message"=>"error",'code'=>404]);
            }
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
    public function dirporuser(Request $request){
        try {
            $validated = $request->validate([
                'token' => 'required'
            ]);
            if ($this->verifica($request->token)) {
                $direcc['direcciones'] = [];
                $direcciones = Direccion::where('user_id', '=', $request->id)->get();
                foreach ($direcciones as $direccion){
                    $dir['id'] = $direccion->id;
                    $dir['nom_ref_dos'] = $direccion->nom_ref_dos;
                    $dir['lad_fij'] = $direccion->lad_fij;
                    $dir['tel_fij'] = $direccion->tel_fij;
                    $dir['ext'] = $direccion->ext;
                    $dir['lad_mov'] = $direccion->lad_mov;
                    $dir['tel_mov'] = $direccion->tel_mov;
                    $dir['calle'] = $direccion->calle;
                    $dir['no_ext'] = $direccion->no_ext;
                    $dir['no_int'] = $direccion->no_int;
                    $dir['pais'] = $direccion->pais;
                    $dir['ciudad'] = $direccion->ciudad;
                    $dir['col'] = $direccion->col;
                    $dir['del_o_munic'] = $direccion->del_o_munic;
                    $dir['cod_post'] = $direccion->cod_post;
                    $dir['ref_zon_de_entreg'] = $direccion->ref_zon_de_entreg;
                    $dir['user_id'] = $direccion->user_id;
                    $dir['nom_ref_uno'] = $direccion->nom_ref_uno;
                    array_push($direcc['direcciones'], $dir);
                }
                return response()->json(['data'=>$direcc,"message"=>"Direcciones encontradas","code"=>200]);
            } else {
                return response()->json(['data' => [], "message" => "token invalido", "code" => 403]);
            }
        } catch (\Throwable $th) {
            return response(["message"=>"error", 'error'=>$th]);
        }
    }
}
