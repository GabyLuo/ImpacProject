<?php
use Phalcon\Mvc\Model;

class AbonosRemisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_remisiones_abonos');
    }
}