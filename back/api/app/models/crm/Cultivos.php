<?php
use Phalcon\Mvc\Model;

class Cultivos extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_cultivos');
    }
}