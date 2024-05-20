<?php
use Phalcon\Mvc\Model;

class Comisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('com_comisiones');
    }
}