<?php
use Phalcon\Mvc\Model;

class Mensajes extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_mensajes_solicitudes');
        $this->belongsTo('solicitud_id', 'SolicitudesO', 'id');
        $this->belongsTo('autorizador_id', 'Autorizadores', 'id');

    }
}