<?php
use Phalcon\Mvc\Model;

class Requerimientos extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_requerimientos');
        $this->belongsTo('requerimiento_id', 'Requisitos', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
        $this->belongsTo('licitacionn_id', 'Licitacion', 'id');
    }
}