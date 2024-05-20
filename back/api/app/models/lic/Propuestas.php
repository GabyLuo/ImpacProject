<?php
use Phalcon\Mvc\Model;

class Propuestas extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_propuestas');

        $this->belongsTo('licitacion_id', 'Licitacion', 'id');
        /*$this->belongsTo('documento1', 'SysDocuments', 'id');
        $this->belongsTo('responsable1', 'SysUsers', 'id');
        $this->belongsTo('respaldo1', 'Respaldo', 'id'); */
    }
}