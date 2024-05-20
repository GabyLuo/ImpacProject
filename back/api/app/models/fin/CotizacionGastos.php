<?php
use Phalcon\Mvc\Model;

class CotizacionGastos extends Model
{
    public function initialize ()
    {
        $this->setSource('fin_cotizacion_gastos');
        $this->belongsTo('gasto_id', 'GastosSolicitudes', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');
    }
}