<?php
use Phalcon\Mvc\Model;

class AptitudesPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_aptitudes_perfiles');
    }
}