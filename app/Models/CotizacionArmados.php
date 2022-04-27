<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionArmados extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_tiene_armados';
    protected $softCascade = ['productos', 'direcciones'];

    public function cotizacion()
    {
        return $this->belongsTo('App\Models\cotizaciones', 'cotizacion_id')->orderBy('id', 'DESC');
    }
    public function productos()
    {
        return $this->hasMany('App\Models\CotizacionArmadoProductos', 'armado_id')->orderBy('id', 'DESC');
    }
    public function direcciones()
    {
        return $this->hasMany('App\Models\CotizacionArmadoTieneDirecciones', 'armado_id')->orderBy('id', 'DESC');
    }
}
