<?php
use Phalcon\Mvc\Model;

class TiposPresentaciones extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_tipos_presentaciones');

        $this->hasMany(
            'id',
            'Productos',
            'presentacion_id',
            [
                'foreignKey' => [
                    'message' => 'La presentacion aun esta relacionado con productos',
                ]
            ]
        );
    }
}