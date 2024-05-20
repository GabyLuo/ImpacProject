<?php
use Phalcon\Mvc\Model;

class VntClientes extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_clientes');

        $this->belongsTo('account_id', 'SysAccounts', 'id');
        $this->belongsTo('municipio_id', 'Municipio', 'id');
        $this->belongsTo('estado_id', 'Estado', 'id');

        $this->hasMany(
            'id',
            'Recurso',
            'cliente_id',
            [
                'foreignKey' => [
                    'message' => 'El cliente aún cuenta con recursos relacionados',
                ]
            ]
        );

        /* $this->hasMany(
            'id',
            'Proveedor',
            'cliente_id',
            [
                'foreignKey' => [
                    'message' => 'El cliente aún tiene proveedores relacionados',
                ]
            ]
        ); */

        $this->hasMany(
            'id',
            'VntDireccion',
            'cliente_id',
            [
                'foreignKey' => [
                    'message' => 'El cliente aún tiene direcciones relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'ClientesRequisitos',
            'cliente_id',
            [
                'foreignKey' => [
                    'message' => 'El cliente aún tiene requisitos relacionados',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Remisiones',
            'cliente_id',
            [
                'foreignKey' => [
                    'message' => 'El cliente aún tiene remisiones relacionados',
                ]
            ]
        );
    }
}