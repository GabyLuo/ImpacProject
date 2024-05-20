<?php
use Phalcon\Mvc\Model;

class RemisionPagos extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_remision_pagos');
    
        $this->belongsTo('remision_id', 'Remisiones', 'id');
        
        $this->belongsTo('forma_pago', 'SatPaymentForms', 'id');
    }
}