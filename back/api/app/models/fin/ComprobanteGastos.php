<?php
use Phalcon\Mvc\Model;

class ComprobanteGastos extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_comprobante_gastos');
        $this->belongsTo('gasto_id', 'GastosSolicitudes', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
    }
}