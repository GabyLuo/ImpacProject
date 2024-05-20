<?php
use Phalcon\Mvc\Model;

class PmoSucursales extends Model
{
    public function initialize ()
    {
        $this->setSource('pmo_sucursales');
    }
}