<?php
use Phalcon\Mvc\Model;

class EmpresasSucursales extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_sucursales');

        $this->belongsTo('empresa_id', 'Empresa', 'id');
    }
}