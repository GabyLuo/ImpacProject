<?php
use Phalcon\Mvc\Model;

class PosgradosDocumentos extends Model
{
    public function initialize ()
    {
        $this->setSource('per_posgrados_documentos');
    }
}