<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CotizacionArmadoTieneDirecciones extends Model
{
    use HasFactory;
    protected $table = 'cotizacion_armado_tiene_direcciones';
    protected $primaryKey='id';

    public function armado()
    {
        return $this->belongsTo('App\Models\CotizacionArmado')->orderBy('id', 'DESC');
    }
}
