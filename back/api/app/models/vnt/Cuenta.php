<?php
use Phalcon\Mvc\Model;

class Cuenta extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_cuentas_empresas');

        $this->belongsTo('empresa_id', 'Empresa', 'id');
    }
}