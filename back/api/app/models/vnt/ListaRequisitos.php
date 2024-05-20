<?php
use Phalcon\Mvc\Model;

class ListaRequisitos extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_lista_requisitos');
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