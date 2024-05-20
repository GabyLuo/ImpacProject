<?php
use Phalcon\Mvc\Model;

class Recepciones extends Model
{
    public function initialize ()
    {
        $this->setSource('cmp_recepciones');

        $this->hasMany(
            'id',
            'RecepcionesDetalles',
            'recepcion_id',
            [
                'foreignKey' => [
                    'message' => 'La recepción aún tiene detalles, para continuar éstos deben ser eliminados.',
                ]
            ]
        );
    }
}