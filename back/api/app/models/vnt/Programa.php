<?php
use Phalcon\Mvc\Model;

class Programa extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_programa');
        
        $this->hasMany(
            'id',
            'Subprograma',
            'programa_id',
            [
                'foreignKey' => [
                    'message' => 'El programa aún tiene subprogramas relacionados',
                ]
            ]
        );
    }
}