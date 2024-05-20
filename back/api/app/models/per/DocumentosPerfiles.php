<?php
use Phalcon\Mvc\Model;

class DocumentosPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_documentos');
    }
}