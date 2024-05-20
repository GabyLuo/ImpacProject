<?php

use Phalcon\Mvc\Model;

class Menus extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_menus');

        /* $this->belongsTo('created_by', 'SysUsers', 'id');
        $this->belongsTo('modified_by', 'SysUsers', 'id'); */

        $this->hasMany(
            'id',
            'MenuItems',
            'menu_id',
            [
                'foreignKey' => [
                    'message' => 'Aún existen ítems relacionados a este menú.',
                ]
            ]
        );
    }
}