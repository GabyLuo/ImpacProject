<?php
use Phalcon\Mvc\Model;

class Proyecto extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_proyecto');

        $this->belongsTo('recurso_id', 'Recurso', 'id');

        /*$this->hasMany(
            'id',
            'Carga',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún tiene relaciones con la tabla pmo_carga',
                ]
            ]
        );*/

        $this->hasMany(
            'id',
            'Actividades',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún tiene actividades relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'SolicitudesO',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'El proyecto aún tiene solicitudes relacionadas',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Colaboradores',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'Este proyecto tiene colaboradores relacionados, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Solicitantes',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'Este proyecto tiene Solicitantes, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Autorizadores',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'Este proyecto tiene Autorizadores, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'ResponsablePagos',
            'proyecto_id',
            [
                'foreignKey' => [
                    'message' => 'Este proyecto tiene Responsable de pagos, no es posible borrarlo.',
                ]
            ]
        );
    }
}