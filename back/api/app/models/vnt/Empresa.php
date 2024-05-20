<?php
use Phalcon\Mvc\Model;

class Empresa extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_empresa');
        
        $this->hasMany(
            'id',
            'Contrato',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene contratos relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Direccion',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene direcciones relacionadas.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Proveedor',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene proveedores relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Licitacion',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene licitaciones relacionadas.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Respaldo',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene licitaciones de respaldo relacionadas.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Remisiones',
            'empresa_id',
            [
                'foreignKey' => [
                    'message' => 'La empresa aún tiene remisiones relacionadas.',
                ]
            ]
        );
    }
}