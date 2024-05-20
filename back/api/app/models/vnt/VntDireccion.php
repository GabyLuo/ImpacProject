<?php
use Phalcon\Mvc\Model;

class VntDireccion extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_direccion');

        $this->belongsTo('cliente_id', 'VntClientes', 'id');
    }
}