<?php
use Phalcon\Mvc\Model;

class Subconcepto extends Model
{
    public function initialize ()
    {
        $this->setSource('vnt_subconcepto');
    }
}