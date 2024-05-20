<?php
use Phalcon\Mvc\Model;

class Direccion extends Model
{
    public function initialize ()
    {
        $this->setSource('exp_direccion');

        $this->belongsTo('empresa_id', 'Empresa', 'id');
        $this->belongsTo('estado_id', 'Estado', 'id');
        $this->belongsTo('municipio_id', 'Municipio', 'id');
    }
}