<?php
use Phalcon\Mvc\Model;

class Aptitudes extends Model
{
    public function initialize ()
    {
        $this->setSource('per_aptitudes');
    }
}