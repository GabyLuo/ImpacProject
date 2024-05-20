<?php
use Phalcon\Mvc\Model;

class RemisionesFacturas extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_remisiones_facturas');
        $this->hasMany(
            'id',
            'AbonosRemisiones',
            'factura_id',
            [
                'foreignKey' => [
                    'message' => 'La factura aún cuenta con abonos relacionados',
                ]
            ]
        );
    }
}