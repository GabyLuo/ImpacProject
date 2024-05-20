<?php
use Phalcon\Mvc\Model;

class Estado extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_estado');
        
        $this->hasMany(
            'id',
            'Municipio',
            'estado_id',
            [
                'foreignKey' => [
                    'message' => 'Este estado aún tiene municipios relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'VntClientes',
            'estado_id',
            [
                'foreignKey' => [
                    'message' => 'Este estado aún tiene clientes relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Direccion',
            'estado_id',
            [
                'foreignKey' => [
                    'message' => 'Este estado aún tiene direcciones de empresas relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'PerfilesExpertos',
            'estado_id',
            [
                'foreignKey' => [
                    'message' => 'Este estado aún tiene perfiles expertos relacionados',
                ]
            ]
        );
    }
}