<?php
use Phalcon\Mvc\Model;

class Municipio extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_municipio');

        $this->belongsTo('estado_id', 'Estado', 'id');

        $this->hasMany(
            'id',
            'VntClientes',
            'municipio_id',
            [
                'foreignKey' => [
                    'message' => 'El municipio aún cuenta con clientes relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Direccion',
            'municipio_id',
            [
                'foreignKey' => [
                    'message' => 'El municipio aún cuenta con direcciones de empresas relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'PerfilesExertos',
            'municipio_id',
            [
                'foreignKey' => [
                    'message' => 'El municipio aún cuenta con perfiles expertos relacionados',
                ]
            ]
        );
    }
}