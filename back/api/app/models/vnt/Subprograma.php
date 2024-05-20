<?php
use Phalcon\Mvc\Model;

class Subprograma extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_subprograma');

        $this->belongsTo('programa_id', 'Programa', 'id');

        $this->hasMany(
            'id',
            'Recurso',
            'subprograma_id',
            [
                'foreignKey' => [
                    'message' => 'El subprograma aún cuenta con recursos relacionados.',
                ]
            ]
        );
    }
}