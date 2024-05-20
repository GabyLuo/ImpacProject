<?php
use Phalcon\Mvc\Model;

class DocumentosComisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('com_comisiones_files');
    }
}