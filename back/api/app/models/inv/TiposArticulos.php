<?php
use Phalcon\Mvc\Model;

class TiposArticulos extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_tipos_articulos');

        $this->hasMany(
            'id',
            'Lineas',
            'categoria_id',
            [
                'foreignKey' => [
                    'message' => 'La categoría aún cuenta con líneas relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Productos',
            'tipo_id',
            [
                'foreignKey' => [
                    'message' => 'La categoría aún cuenta con productos relacionados.',
                ]
            ]
        );
    }
}