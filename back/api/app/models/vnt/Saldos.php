<?php
use Phalcon\Mvc\Model;

class Saldos extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_cuentas_saldos');
    }
}
