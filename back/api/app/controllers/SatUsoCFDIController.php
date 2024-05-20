<?php

use Phalcon\Mvc\Controller;

class SatUsoCFDIController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => 'Error!', 'content' => 'Error del servidor.']];


    public function getSelectOptions () {
        $sql = "
        SELECT
        	id as value,
        	concat('(', clave, ') ', descripcion) as label
       	FROM sat_uso_cfdi
       	ORDER BY clave ASC;";

        $invoices = $this->db->query($sql)->fetchAll();
        
        $this->content['usoCFDIs'] = $invoices;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

}