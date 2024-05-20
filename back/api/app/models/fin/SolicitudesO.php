<?php
use Phalcon\Mvc\Model;

class SolicitudesO extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_solicitudes');

        $this->belongsTo('proyecto_id', 'Proyecto', 'id');
        $this->belongsTo('responsable', 'SysUsers', 'id');
    }
}