<?php
use Phalcon\Mvc\Model;

class Cotizaciones extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_cotizaciones');

        $this->hasMany(
            'id',
            'CotizacionesDetalles',
            'cotizacion_id',
            [
                'foreignKey' => [
                    'message' => 'La cotización aún tiene detalles, para continuar éstos deben ser eliminados.',
                ]
            ]
        );
    }
}