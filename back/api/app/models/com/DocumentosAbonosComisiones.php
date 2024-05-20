<?php
use Phalcon\Mvc\Model;

class DocumentosAbonosComisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('com_abonos_files');
    }
}