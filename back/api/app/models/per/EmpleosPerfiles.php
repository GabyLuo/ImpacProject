<?php
use Phalcon\Mvc\Model;

class EmpleosPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_empleos');
    }
}