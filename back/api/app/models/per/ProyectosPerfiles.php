<?php
use Phalcon\Mvc\Model;

class ProyectosPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_proyectos');
    }
}