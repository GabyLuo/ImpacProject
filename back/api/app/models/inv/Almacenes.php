<?php
use Phalcon\Mvc\Model;

class Almacenes extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_almacenes');

        $this->hasMany(
            'id',
            'Movimientos',
            'almacen_id',
            [
                'foreignKey' => [
                    'message' => 'El almacén aún cuenta con movimientos relacionados.',
                ]
            ]
        );
    }
}