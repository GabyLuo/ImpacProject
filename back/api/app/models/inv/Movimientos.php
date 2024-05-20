<?php
use Phalcon\Mvc\Model;

class Movimientos extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_movimientos');

        $this->hasMany(
            'id',
            'MovimientosDetalles',
            'movimiento_id',
            [
                'foreignKey' => [
                    'message' => 'El movimiento aún tiene detalles, para continuar éstos deben ser eliminados.',
                ]
            ]
        );
    }
}