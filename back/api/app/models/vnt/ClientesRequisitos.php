<?php
use Phalcon\Mvc\Model;

class ClientesRequisitos extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_clientes_requisitos');

        $this->belongsTo('cliente_id', 'VntClientes', 'id');
        $this->belongsTo('requisito_id', 'Requisitos', 'id');

        $this->hasMany(
            'id',
            'PadronRequisitos',
            'requisito_cliente_id',
            [
                'foreignKey' => [
                    'message' => 'Este cliente aún esta relacionado con Padrón de requisitos',
                ]
            ]
        );

    }
}