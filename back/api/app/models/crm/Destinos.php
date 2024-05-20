<?php
use Phalcon\Mvc\Model;

class Destinos extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_destinos');

        $this->hasMany(
            'id',
            'code',
            'recibe_persona',
            [
                'foreignKey' => [
                    'message' => 'El destino aún cuenta con  relaciones.',
                ]
            ]
        );
    }
}