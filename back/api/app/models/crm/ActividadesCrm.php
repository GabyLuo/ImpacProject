<?php
use Phalcon\Mvc\Model;

class ActividadesCrm extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_actividades');

        $this->hasMany(
            'id',
            'Tareas',
            'actividad_id',
            [
                'foreignKey' => [
                    'message' => 'La tarea aún tiene relación con las oportunidades, no es posible eliminarla.',
                ]
            ]
        );
    }
}