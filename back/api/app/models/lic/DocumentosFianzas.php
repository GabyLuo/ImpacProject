<?php
use Phalcon\Mvc\Model;

class DocumentosFianzas extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_documentos_fianzas');

        $this->belongsTo('fianza_id', 'Fianzas', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
    }
}