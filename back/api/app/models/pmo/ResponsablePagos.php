<?php
use Phalcon\Mvc\Model;

class ResponsablePagos extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_responsable_pagos');
        $this->belongsTo('proyecto_id', 'Proyecto', 'id');
        $this->belongsTo('usuario_id', 'SysUsers', 'id');
    }
}