<?php
use Phalcon\Mvc\Model;

class Proveedor extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_proveedor');

        $this->belongsTo('cliente_id', 'VntClientes', 'id');
        $this->belongsTo('empresa_id', 'Empresa', 'id');
        $this->belongsTo('formato_requisito_id', 'SysDocuments', 'id');

        $this->hasMany(
            'id',
            'ListaRequisitos',
            'padron_id',
            [
                'foreignKey' => [
                    'message' => 'El proveedor aún tiene requisitos relacionados, para continuar debe eliminarlos',
                ]
            ]
        );
    }
}