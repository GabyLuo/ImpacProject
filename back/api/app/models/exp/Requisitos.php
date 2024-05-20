<?php
use Phalcon\Mvc\Model;

class Requisitos extends Model
{
    public function initialize ()
    {
        $this->setSource('exp_requisitos');

        $this->hasMany(
            'id',
            'ClientesRequisitos',
            'requisito_id',
            [
                'foreignKey' => [
                    'message' => 'El requisito aún esta relacionado con clientes',
                ]
            ]
        );

        /* $this->hasMany(
            'id',
            'Requerimientos',
            'requerimiento_id',
            [
                'foreignKey' => [
                    'message' => 'El requisito aún tiene relaciones con la tabla requerimientos de licitaciones',
                ]
            ]
        ); */

        $this->hasMany(
            'id',
            'DocumentosPadron',
            'id_lista',
            [
                'foreignKey' => [
                    'message' => 'El requisito aún tiene documentos relacionados, para continuar debe eliminar todos los archivos.',
                ]
            ]
        );
    }
}