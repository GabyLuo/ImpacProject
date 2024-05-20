<?php
use Phalcon\Mvc\Model;

class ComprasDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('cmp_compras_detalles');
    }
}