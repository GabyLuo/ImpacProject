<?php
use Phalcon\Mvc\Model;

class Notas extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_notas');
        $this->belongsTo('solicitud_id', 'SolicitudesO', 'id');
        $this->belongsTo('usuario_id', 'SysUsers', 'id');

    }
}