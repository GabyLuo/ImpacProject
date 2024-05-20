<?php
use Phalcon\Mvc\Model;

class Lineas extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_lineas');

        $this->hasMany(
            'id',
            'Productos',
            'linea_id',
            [
                'foreignKey' => [
                    'message' => 'La línea aún cuenta con productos relacionados.',
                ]
            ]
        );
    }
}