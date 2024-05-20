<?php
use Phalcon\Mvc\Model;

class Compras extends Model
{
    public function initialize ()
    {
        $this->setSource('cmp_compras');

        $this->hasMany(
            'id',
            'ComprasDetalles',
            'compra_id',
            [
                'foreignKey' => [
                    'message' => 'La compra aún tiene detalles, para continuar éstos deben ser eliminados.',
                ]
            ]
        );
    }
}