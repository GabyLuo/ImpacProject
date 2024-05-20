<?php
use Phalcon\Mvc\Model;

class Carga extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_carga');

        $this->belongsTo('proyecto_id', 'Proyecto', 'id');

        $this->hasMany(
            'id',
            'CargaDetalles',
            'carga_id',
            [
                'foreignKey' => [
                    'message' => 'La tabla pmo_carga aún tiene detalles relacionados.',
                ]
            ]
        );
    }
}