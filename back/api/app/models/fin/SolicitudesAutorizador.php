<?php
use Phalcon\Mvc\Model;

class SolicitudesAutorizador extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_solicitudes_autorizador');
    }
}