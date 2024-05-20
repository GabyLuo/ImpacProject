<?php
use Phalcon\Mvc\Model;

class DocumentosPadron extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_documentos_padron');
    }

}