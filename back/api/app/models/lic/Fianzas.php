<?php
use Phalcon\Mvc\Model;

class Fianzas extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_fianzas');

        $this->belongsTo('empresa', 'Empresa', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');

        $this->hasMany(
            'id',
            'DocumentosFianzas',
            'fianza_id',
            [
                'foreignKey' => [
                    'message' => 'La fianza aún tiene documentos relacionados, para continuar debe eliminar todos los archivos.',
                ]
            ]
        );
    }
}