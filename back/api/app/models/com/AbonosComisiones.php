<?php
use Phalcon\Mvc\Model;

class AbonosComisiones extends Model
{
    public function initialize ()
    {
        $this->setSource('com_comisiones_abono');
    }
}