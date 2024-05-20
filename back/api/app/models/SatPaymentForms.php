<?php
use Phalcon\Mvc\Model;

class SatPaymentForms extends Model
{
    public function initialize ()
    {
        $this->setSource('sat_formas_pagos');

        $this->hasMany(
            'id',
            'Remision',
            'forma_pago',
            [
                'foreignKey' => [
                    'message' => 'La forma de pago aun cuenta con remisiones relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'RemisionPagos',
            'forma_pago',
            [
                'foreignKey' => [
                    'message' => 'La forma de pago aun cuenta con pagos relacionados',
                ]
            ]
        );
    }
}