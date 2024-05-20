<?php

use Phalcon\Mvc\Model;

class SysAccounts extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_users');

        $this->hasMany(
            'id',
            'SysUsers',
            'account_id',
            [
                'foreignKey' => [
                    'message' => 'La cuenta aún cuenta con usuarios relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'VntClientes',
            'account_id',
            [
                'foreignKey' => [
                    'message' => 'La cuenta aún cuenta con clientes relacionados.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'SysDocuments',
            'account_id',
            [
                'foreignKey' => [
                    'message' => 'La cuenta aún cuenta con documentos relacionados.',
                ]
            ]
        );

    }
}