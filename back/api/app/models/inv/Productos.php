<?php
use Phalcon\Mvc\Model;

class Productos extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_productos');

        $this->belongsTo('presentacion_id', 'TiposPresentaciones', 'id');

        $this->hasMany(
            'id',
            'Bom',
            'producto_id',
            [
                'foreignKey' => [
                    'message' => 'El producto aún tiene bom relacionado.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'MovimientosDetalles',
            'producto_id',
            [
                'foreignKey' => [
                    'message' => 'El producto aún tiene movimientos relacionados.',
                ]
            ]
        );
    }
}