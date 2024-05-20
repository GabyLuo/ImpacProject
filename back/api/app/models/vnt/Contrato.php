<?php
use Phalcon\Mvc\Model;

class Contrato extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_contrato');

        $this->belongsTo('recurso_id', 'Recurso', 'id');
        $this->belongsTo('empresa_id', 'Empresa', 'id');
        $this->belongsTo('licitacion_id', 'Licitacion', 'id');

        $this->hasMany(
            'id',
            'contratosFacturas',
            'contrato_id',
            [
                'foreignKey' => [
                    'message' => 'El contrato aún cuenta con facturas relacionadas',
                ]
            ]
        );
    }
}