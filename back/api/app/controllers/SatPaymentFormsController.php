<?php

use Phalcon\Mvc\Controller;

class SatPaymentFormsController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => 'Error!', 'content' => 'Error del servidor.']];


    public function getSelectOptions () {
        $sql = "
        SELECT
        	id as value,
        	concat('(', clave, ') ', descripcion) as label
       	FROM sat_formas_pagos
       	ORDER BY clave ASC;";

        $invoices = $this->db->query($sql)->fetchAll();
        
        $this->content['paymentsForms'] = $invoices;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

}