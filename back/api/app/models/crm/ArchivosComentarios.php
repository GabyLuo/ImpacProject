<?php
use Phalcon\Mvc\Model;

class ArchivosComentarios extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_archivos_comentarios');
    }
}