<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\NotificacionPasswordCambiado;
use App\Notifications\InvoicePaid;

class PruebasCorreos extends Controller
{
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
    public function envio($id){
        $plantilla=DB::table('plantillas')->where("id",3)->first();
        // return $plantilla[0]->dis_de_la_plant;
        $invitado = User::findOrFail($id);
        // $invitado->email = $invitado->email;
        // $invitado->nom = $invitado->nom;
        // $invitado->apell = $invitado->apell;
        $invitado->notify(new NotificacionPasswordCambiado($plantilla));
        // $invitado->notify(new InvoicePaid());
    }
}
