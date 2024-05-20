<?php
use Phalcon\Mvc\Model;

class TiposMovimientos extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_tipos_movimientos');
    }
}