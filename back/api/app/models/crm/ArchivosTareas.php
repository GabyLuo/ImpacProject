<?php
use Phalcon\Mvc\Model;

class ArchivosTareas extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_archivos');
    }
}