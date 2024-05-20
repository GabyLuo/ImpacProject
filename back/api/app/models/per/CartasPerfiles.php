<?php
use Phalcon\Mvc\Model;

class CartasPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_cartas');
    }
}