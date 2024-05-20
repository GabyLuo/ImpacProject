<?php
use Phalcon\Mvc\Model;

class MovimientosDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_movimientos_detalles');
    }
}