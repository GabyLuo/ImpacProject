<?php
use Phalcon\Mvc\Model;

class Crm extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_carriles');
    }
}