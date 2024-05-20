<?php
use Phalcon\Mvc\Model;

class Actividades extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_actividades');

        $this->belongsTo('proyecto_id', 'Proyecto', 'id');

        /* $this->hasMany(
            'id',
            'Actividades',
            'padre_id',
            [
                'foreignKey' => [
                    'message' => 'La actividad aún tiene actividades relacionadas.',
                ]
            ]
        ); */
        
        $this->hasMany(
            'id',
            'GastosSolicitudes',
            'actividad_id',
            [
                'foreignKey' => [
                    'message' => 'La actividad aún tiene Solicitudes y gastos registrados .',
                ]
            ]
        );
    }
}