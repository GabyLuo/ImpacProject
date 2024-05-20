<?php
use Phalcon\Mvc\Model;

class Autorizadores extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_autorizadores');
        $this->belongsTo('proyecto_id', 'Proyecto', 'id');
        $this->belongsTo('usuario_id', 'SysUsers', 'id');
    }
}