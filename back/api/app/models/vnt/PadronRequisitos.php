<?php
use Phalcon\Mvc\Model;

class PadronRequisitos extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_padron_requisitos');

        $this->belongsTo('padron_id', 'Proveedor', 'id');
        $this->belongsTo('requisito_cliente_id', 'ClientesRequisitos', 'id');
        $this->belongsTo('documento_id', 'SysDocuments', 'id');

    }
}