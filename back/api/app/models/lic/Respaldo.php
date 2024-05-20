<?php
use Phalcon\Mvc\Model;

class Respaldo extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_respaldo');

        $this->belongsTo('licitacion_id', 'Licitacion', 'id');
        $this->belongsTo('empresa_id', 'Empresa', 'id');

    }
}