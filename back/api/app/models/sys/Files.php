<?php
use Phalcon\Mvc\Model;

class Files extends Model
{
    public function initialize ()
    {
        $this->setSource('sys_files');
    }
}