<?php
use Phalcon\Mvc\Model;

class LicitacionesPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_licitaciones');
    }
}