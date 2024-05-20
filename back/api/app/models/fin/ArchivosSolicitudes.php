<?php
use Phalcon\Mvc\Model;

class ArchivosSolicitudes extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_requerimientos');
        $this->belongsTo('requerimiento_id', 'Requisitos', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
        $this->belongsTo('solicitud_id', 'Solicitudes', 'id');
    }
}