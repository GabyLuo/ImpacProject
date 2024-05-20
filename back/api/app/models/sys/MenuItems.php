<?php

use Phalcon\Mvc\Model;

class MenuItems extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_menu_items');

        /* $this->belongsTo('created_by', 'SysUsers', 'id');
        $this->belongsTo('modified_by', 'SysUsers', 'id'); */

        $this->belongsTo('menu_id', 'Menus', 'id');

        $this->hasMany(
            'id',
            'MenuItemGrants',
            'item_id',
            [
                'foreignKey' => [
                    'message' => 'Aún existen relaciones con este item',
                ]
            ]
        );
    }
}