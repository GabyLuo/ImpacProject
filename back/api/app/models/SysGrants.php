<?php

use Phalcon\Mvc\Model;

class SysGrants extends Model
{

    public function initialize ()
    {
        $this->setSource('sys_grants');

        $this->belongsTo('role_id', 'SysRoles', 'id');
        $this->belongsTo('user_id', 'SysUsers', 'id');
    }
}
