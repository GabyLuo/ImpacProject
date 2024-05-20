<?php
use Phalcon\Mvc\Model;

class Oportunidades extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_oportunidades');
    }
}