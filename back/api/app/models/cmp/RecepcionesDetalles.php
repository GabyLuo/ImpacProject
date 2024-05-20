<?php
use Phalcon\Mvc\Model;

class RecepcionesDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('cmp_recepciones_detalles');
    }
}