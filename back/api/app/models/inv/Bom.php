<?php
use Phalcon\Mvc\Model;

class Bom extends Model
{
    public function initialize ()
    {
        $this->setSource('inv_bom');
    }
}