<?php

use Phalcon\Mvc\Model;

class SysRoles extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_roles');

        $this->belongsTo('parent_id', 'SysRoles', 'id', ['alias' => 'parentRole',]);

        $this->hasMany(
            'id',
            'SysRoles',
            'parent_id',
            [
                'foreignKey' => [
                    'message' => 'Aún existen roles que dependen del rol seleccionado.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'SysGrants',
            'role_id',
            [
                'foreignKey' => [
                    'message' => 'Aun existen usuarios con este rol.',
                ]
            ]
        );

        $this->hasMany(
            'id',
            'MenuItemGrants',
            'role_id',
            [
                'foreignKey' => [
                    'message' => 'El rol seleccionado aún cuenta con submenus registrados.',
                ]
            ]
        );
    }
}