<?php
use Phalcon\Mvc\Model;

class DocumentosEventos extends Model
{
    public function initialize ()
    {
        $this->setSource('lic_documentos_eventos');
    }
}