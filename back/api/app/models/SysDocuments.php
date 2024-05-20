<?php
use Phalcon\Mvc\Model;

class SysDocuments extends Model
{
    public function initialize ()
    {
        $this->setSource('sys_documents');

        $this->belongsTo('account_id', 'SysAccounts', 'id');

        $this->hasMany(
            'id',
            'PadronRequisitos',
            'documento_id',
            [
                'foreignKey' => [
                    'message' => 'El documento aun esta relacionado con Padrón requisitos',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Proveedor',
            'formato_requisito_id',
            [
                'foreignKey' => [
                    'message' => 'El documento aun esta relacionado con el Padrón de Proveedores',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Requerimientos',
            'documento_id',
            [
                'foreignKey' => [
                    'message' => 'El documento aun esta relacionado con Requerimientos de licitaciones',
                ]
            ]
        );
    }
}