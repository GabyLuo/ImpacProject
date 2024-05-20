<?php
use Phalcon\Mvc\Model;

class CursosDocumentos extends Model
{
    public function initialize ()
    {
        $this->setSource('per_cursos_documentos');
    }
}