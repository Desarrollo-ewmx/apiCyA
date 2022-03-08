<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use Illuminate\Http\Request;
use App\Traits\Verifytoken;

class FacturasController extends Controller
{
    use Verifytoken;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return Facturas::all();
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

    public function fact(Request $request){
        try {
            //code...
            $validated = $request->validate([
                'token'=>'required'
            ]);
            if($this->verifica($request->token)){
                $fac = Facturas::where('id', '!=', 0)->get();
                if($fac){
                    $data['facturas']=[];
                    for ($i=0; $i< count($fac); $i++) {
                        $datos['id']=$fac[$i]->id;
                        $datos['status']=$fac[$i]->est_fact;
                        $datos['id_user']=$fac[$i]->user_id ;
                        $datos['pdf']=$fac[$i]->fact_pdf_rut . $fac[$i]->fact_pdf_nom;
                        $datos['xml']=$fac[$i]->fact_xlm_rut . $fac[$i]->fact_xlm_nom;
                        $datos['correo']=$fac[$i]->corr;
                        array_push($data['facturas'],$datos);
                    }
                    return response(['data'=>$data,"message"=>"success","code"=>200]);
                }else{
                        return response()->json(['data'=>[],"message"=>"facturas no encontrado","code"=>404]);
                    }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th]);
        }
    }

    protected function factone(Request $request){
        try {
            //code...
            $validated = $request->validate([
                'email' => 'required|email',
                'token'=>'required'
            ]);
            $email = Facturas::where('corr', '=', $request->email)->first();
            if($this->verifica($request->token)){
                $fac = Facturas::where('corr', '=', $request->email)->get();
                if($email->corr==$request->email){
                    $data['facturas']=[];
                    for ($i=0; $i< count($fac); $i++) {
                        $datos['id']=$fac[$i]->id;
                        $datos['status']=$fac[$i]->est_fact;
                        $datos['id_user']=$fac[$i]->user_id ;
                        $datos['pdf']=$fac[$i]->fact_pdf_rut . $fac[$i]->fact_pdf_nom;
                        $datos['xml']=$fac[$i]->fact_xlm_rut . $fac[$i]->fact_xlm_nom;
                        $datos['correo']=$fac[$i]->corr;
                        array_push($data['facturas'],$datos);
                    }
                    return response(['data'=>$data,"message"=>"success","code"=>200]);
                }else{
                        return response()->json(['data'=>[],"message"=>"facturas no encontrado","code"=>404]);
                    }
        }
        } catch (\Throwable $th) {
            //throw $th;
            return response(["message"=>"error", 'error'=>$th,"code"=>404]);
        }
    }
}
