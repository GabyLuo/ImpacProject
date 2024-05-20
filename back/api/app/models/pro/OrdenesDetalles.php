<?php
use Phalcon\Mvc\Model;

class OrdenesDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('pro_ordenes_detalles');
    }
}