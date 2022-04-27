<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class cotizaciones extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = "cotizaciones";

    public function armados()
    {
        return $this->hasMany('App\Models\CotizacionArmados','cotizacion_id')->orderBy('id', 'DESC');
    }
}
