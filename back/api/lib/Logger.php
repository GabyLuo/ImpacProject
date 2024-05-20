<?php
use Phalcon\Logger\Adapter\File as FileAdapter;

class Logger
{
    public static function log($message) {
        $logger = new FileAdapter("../log/app.log");
        $logger->log(print_r($message, 1));
    }

}