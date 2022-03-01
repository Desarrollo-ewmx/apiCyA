<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;

class SistemaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['info']=[];
        $info = Sistema::where('id', '=', 1)->get();
        $datos['empresa']=$info[0]->emp;
        $datos['empresa_abrev']=$info[0]->emp_abrev;
        $datos['year']=$info[0]->year_de_ini;
        $datos['tel']=$info[0]->lad_fij.$info[0]->tel_fij;
        $datos['ext']=$info[0]->ext;
        $datos['direccion']=$info[0]->direc_uno;
        $datos['correo_ventas']=$info[0]->corr_vent;
        $datos['pag']=$info[0]->pag;
        $datos['face']=$info[0]->red_fbk;
        $datos['twitter']=$info[0]->red_tw;
        $datos['insta']=$info[0]->red_ins;
        $datos['link']=$info[0]->red_link;
        $datos['youtube']=$info[0]->red_youtube;
        $datos['logo']=$info[0]->log_neg_rut.$info[0]->log_neg;
        array_push($data['info'],$datos);
        return response(['data'=>$data,"message"=>"success","code"=>200]);
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
