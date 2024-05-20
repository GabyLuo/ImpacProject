<?php
use Phalcon\Mvc\Model;

class CrmLogs extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_logs');
    }
}