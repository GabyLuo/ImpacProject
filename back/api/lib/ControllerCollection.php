<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 27/09/17
 * Time: 11:25 AM
 */

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Micro\Collection as MicroCollection;


class ControllerCollection extends MicroCollection
{
    public function __construct(Controller $controller, string $prefix)
    {
        $this->setHandler($controller);
        $this->setPrefix($prefix);
    }
}