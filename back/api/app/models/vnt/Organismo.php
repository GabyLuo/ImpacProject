<?php
use Phalcon\Mvc\Model;

class Organismo extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_organismo');

        $this->hasMany(
            'id',
            'Contrato',
            'organismo_id',
            [
                'foreignKey' => [
                    'message' => 'El organismo aún cuenta con contratos relacionados',
                ]
            ]
        );
    }
}