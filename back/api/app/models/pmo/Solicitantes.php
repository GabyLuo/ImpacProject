<?php
use Phalcon\Mvc\Model;

class Solicitantes extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_solicitantes');
        $this->belongsTo('proyecto_id', 'Proyecto', 'id');
        $this->belongsTo('usuario_id', 'SysUsers', 'id');
    }
}