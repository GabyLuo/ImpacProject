<?php
use Phalcon\Mvc\Model;

class Remisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_remisiones');
    
        $this->belongsTo('empresa_id', 'Empresa', 'id');
        $this->belongsTo('cliente_id', 'VntClientes', 'id');
        $this->belongsTo('forma_pago', 'SatPaymentForms', 'id');
        $this->belongsTo('uso_cfdi', 'SatUsoCFDI', 'id');

        $this->hasMany(
            'id',
            'RemisionesFacturas',
            'remision_id',
            [
                'foreignKey' => [
                    'message' => 'La remisión aún cuenta con facturas relacionadas',
                ]
            ]
        );
    }
}