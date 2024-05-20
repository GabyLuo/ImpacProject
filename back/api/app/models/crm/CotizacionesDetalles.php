<?php
use Phalcon\Mvc\Model;

class CotizacionesDetalles extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_cotizaciones_detalles');
    }
}