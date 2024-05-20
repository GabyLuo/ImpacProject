<?php
use Phalcon\Mvc\Model;

class contratosFacturas extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_contratos_facturas');
        $this->belongsTo('contrato_id', 'Contrato', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');

        $this->hasMany(
            'id',
            'AbonosFacturas',
            'factura_id',
            [
                'foreignKey' => [
                    'message' => 'La factura aún cuenta con abonos relacionados',
                ]
            ]
        );
    }
}