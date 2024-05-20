<?php
use Phalcon\Mvc\Model;

class PerfilesExpertos extends Model
{
    public function initialize ()
    {
        $this->setSource('per_perfiles_expertos');
    }
}