<?php
use Phalcon\Mvc\Model;

class Etapas extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_etapas');

        $this->hasMany(
            'id',
            'Oportunidades',
            'etapa_id',
            [
                'foreignKey' => [
                    'message' => 'La etapa tiene oportunidades relacionadas, no es posible su eliminación.',
                ]
            ]
        );
    }
}