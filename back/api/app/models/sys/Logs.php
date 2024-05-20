<?php
use Phalcon\Mvc\Model;

class Logs extends Model
{
    public function initialize ()
    {
        $this->setSource('sys_logs');
    }
}