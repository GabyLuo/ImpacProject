<?php
use Phalcon\Mvc\Model;

class RemisionDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_remision_detalles');
    
        $this->belongsTo('remision_id', 'Remisiones', 'id');
        $this->belongsTo('producto_id', 'Productos', 'id');
    }
}