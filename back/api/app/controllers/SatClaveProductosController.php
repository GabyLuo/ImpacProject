<?php

use Phalcon\Mvc\Controller;

class SatClaveProductosController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => 'Error!', 'content' => 'Error del servidor.']];


    public function getSelectOptions ($search) {
        $sql = "
        SELECT
        	id as value,
        	clave,
            concat('(', clave, ') ', descripcion) as label
       	FROM sat_clave_productos
        WHERE (clave ILIKE '%".$search."%' or descripcion ILIKE '%".$search."%')
       	ORDER BY clave ASC;";

        $invoices = $this->db->query($sql)->fetchAll();
        
        $this->content['claveProductos'] = $invoices;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

}