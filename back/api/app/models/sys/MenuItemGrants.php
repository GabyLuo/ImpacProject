<?php

use Phalcon\Mvc\Model;

class MenuItemGrants extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_menuitem_grants');

        $this->belongsTo('created_by', 'SysUsers', 'id');
        $this->belongsTo('modified_by', 'SysUsers', 'id');
        
        $this->belongsTo('role_id', 'SysRoles', 'id');
        $this->belongsTo('item_id', 'MenuItems', 'id');
    }
}