<?php
use Phalcon\Mvc\Model;

class LogLevels extends Model
{
    public function initialize ()
    {
        $this->setSource('sys_log_levels');
    }
}