<?php

use Phalcon\Mvc\Model;

class SysUsers extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_users');

        $this->belongsTo('account_id', 'SysAccounts', 'id');

        $this->hasMany(
            'id',
            'SysGrants',
            'user_id',
            [
                'foreignKey' => [
                    'message' => 'Aún existen relaciones a roles para este usuario.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Colaboradores',
            'usuario_id',
            [
                'foreignKey' => [
                    'message' => 'Este usuario tiene perfil de colaborador, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Solicitantes',
            'usuario_id',
            [
                'foreignKey' => [
                    'message' => 'Este usuario tiene perfil de solicitante, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Autorizadores',
            'usuario_id',
            [
                'foreignKey' => [
                    'message' => 'Este usuario tiene perfil de autorizador, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'ResponsablePagos',
            'usuario_id',
            [
                'foreignKey' => [
                    'message' => 'Este usuario tiene perfil de responsable de pagos, no es posible borrarlo.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'Notas',
            'usuario_id',
            [
                'foreignKey' => [
                    'message' => 'Este usuario tiene notas guardadas, no es posible borrarlo.',
                ]
            ]
        );
    }
}