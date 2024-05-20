<?php
use Phalcon\Mvc\Model;

class Concepto extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_concepto');

        $this->hasMany(
            'id',
            'Subconcepto',
            'concepto_id',
            [
                'foreignKey' => [
                    'message' => 'El concepto aún tiene subconceptos relacionados.',
                ]
            ]
        );
    }
}