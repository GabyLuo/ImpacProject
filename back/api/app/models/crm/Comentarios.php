<?php
use Phalcon\Mvc\Model;

class Comentarios extends Model
{
    public function initialize ()
    {
        $this->setSource('crm_comentarios');
    }
}