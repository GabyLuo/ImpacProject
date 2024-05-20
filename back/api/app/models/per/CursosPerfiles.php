<?php
use Phalcon\Mvc\Model;

class CursosPerfiles extends Model
{
    public function initialize ()
    {
        $this->setSource('per_cursos');
    }
}