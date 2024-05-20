<?php
use Phalcon\Mvc\Model;

class DocumentosActividades extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_actividades_documents');
    }
}