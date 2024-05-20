<?php
use Phalcon\Mvc\Model;

class PosgradosPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_posgrados');
    }
}