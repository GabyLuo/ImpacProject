<?php
use Phalcon\Mvc\Model;

class Aliados extends Model
{
    public function initialize ()
    {
        $this->setSource('com_aliados');
    }
}