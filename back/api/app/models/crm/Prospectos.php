<?php
use Phalcon\Mvc\Model;

class Prospectos extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_prospectos');
    }
}