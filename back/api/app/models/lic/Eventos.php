<?php
use Phalcon\Mvc\Model;

class Eventos extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_eventos');

        $this->belongsTo('licitacion_id', 'Licitacion', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
    }
}