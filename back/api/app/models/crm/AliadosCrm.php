<?php
use Phalcon\Mvc\Model;

class AliadosCrm extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_aliados');
    }
}