<?php
use Phalcon\Mvc\Model;

class CargaDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_carga_detalles');

        $this->belongsTo('carga_id', 'Carga', 'id');
    }
}