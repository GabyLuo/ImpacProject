<?php
use Phalcon\Mvc\Model;

class Recurso extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_recurso');

        $this->belongsTo('cliente_id', 'VntClientes', 'id');
        $this->belongsTo('subprograma_id', 'Subprograma', 'id');

        $this->hasMany(
            'id',
            'Proyecto',
            'recurso_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún cuenta con projects relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Contrato',
            'recurso_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún cuenta con contratos relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Licitacion',
            'recurso_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún cuenta con licitaciones relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Organismo',
            'recurso_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún cuenta con organismos de apoyo relacionados',
                ]
            ]
        );
    }
}