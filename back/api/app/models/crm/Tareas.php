<?php
use Phalcon\Mvc\Model;

class Tareas extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_tareas');
    }
}