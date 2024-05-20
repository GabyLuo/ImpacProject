<?php
use Phalcon\Mvc\Model;

class Ordenes extends Model
{
    public function initialize ()
    {
        $this->setSource('pro_ordenes');

        $this->hasMany(
            'id',
            'Ordenes',
            'orden_id',
            [
                'foreignKey' => [
                    'message' => 'El movimiento pertenece a una orden de producción, no es posible eliminarlo.',
                ]
            ]
        );
    }
}