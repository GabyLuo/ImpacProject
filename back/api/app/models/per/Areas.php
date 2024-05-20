<?php
use Phalcon\Mvc\Model;

class Areas extends Model
{
    public function initialize ()
    {
        $this->setSource('per_areas');
    }
}