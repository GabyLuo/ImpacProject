<?php
use Phalcon\Mvc\Model;

class Colaboradores extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_colaboradores');
        $this->belongsTo('proyecto_id', 'Proyecto', 'id');
        $this->belongsTo('usuario_id', 'SysUsers', 'id');
    }
}