<?php
use Phalcon\Mvc\Model;

class Licitacion extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_licitacion');

        $this->belongsTo('recurso_id', 'Recurso', 'id');
        $this->belongsTo('empresa_id', 'Empresa', 'id');

        $this->hasMany(
            'id',
            'Contrato',
            'licitacion_id',
            [
                'foreignKey' => [
                    'message' => 'La licitación aún cuenta con contratos relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Respaldo',
            'licitacion_id',
            [
                'foreignKey' => [
                    'message' => 'La licitación aún cuenta con licitaciones de respaldo relacionadas.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Rquerimientos',
            'licitacion_id',
            [
                'foreignKey' => [
                    'message' => 'La licitación aún cuenta con requerimientos relacionados.',
                ]
            ]
        );

    }
}