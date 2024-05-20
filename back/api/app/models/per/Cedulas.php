<?php
use Phalcon\Mvc\Model;

class Cedulas extends Model
{
    public function initialize ()
    {
        $this->setSource('per_cedulas');
    }
}