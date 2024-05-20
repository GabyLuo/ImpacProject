<?php
use Phalcon\Mvc\Model;

class FacturaGastos extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_factura_gastos');
        $this->belongsTo('gasto_id', 'GastosSolicitudes', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
    }
}