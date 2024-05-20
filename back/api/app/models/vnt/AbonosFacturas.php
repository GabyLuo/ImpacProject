<?php
use Phalcon\Mvc\Model;

class AbonosFacturas extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_facturas_abonos');
        $this->belongsTo('factura_id', 'contratosFacturas', 'id');
    }
}