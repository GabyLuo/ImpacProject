<?php
use Phalcon\Mvc\Model;

class Titulos extends Model
{
    public function initialize ()
    {
        $this->setSource('per_titulos');
    }
}