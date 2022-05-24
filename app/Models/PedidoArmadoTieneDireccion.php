<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoArmadoTieneDireccion extends Model
{
    use HasFactory;
    protected $table = 'pedido_armado_tiene_direcciones';
    
    public function armado()
    {
        return $this->belongsTo('App\Models\PedidoArmado', 'pedido_armado_id')->orderBy('id', 'DESC');
    }
    public function comprobantes()
    {
        return $this->hasMany('App\Models\PedidoArmadoDireccionTieneComprobante', 'direccion_id')->orderBy('id', 'DESC');
    }
}
