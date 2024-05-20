<?php
use Phalcon\Mvc\Model;

class Giros extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_giros');
    }
}