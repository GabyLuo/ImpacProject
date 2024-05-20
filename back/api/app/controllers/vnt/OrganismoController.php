<?php

use Phalcon\Mvc\Controller;

class OrganismoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM vnt_organismo order by id";

        $this->content['organismos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto ($id)
    {
        $sql = "SELECT * 
            FROM vnt_organismo where recurso_id = $id order by id";

        $this->content['organismos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

                $organismo = new Organismo();
                $organismo->setTransaction($tx);
                $organismo->nombre = trim(strtoupper($request['nombre']));
                $organismo->recurso_id = intval($request['recurso_id']);
                $organismo->year = $request['year'];
                
                if ($organismo->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el organismo de apoyo.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($organismo);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el organismo de apoyo.'];
                    $tx->rollback();
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

                $organismo = Organismo::findFirst($request['id']);
                if ($organismo) {
                    $organismo->setTransaction($tx);
                    $organismo->nombre = trim(strtoupper($request['nombre']));
                    
                    if ($organismo->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el organismo de apoyo'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($organismo);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el organismo de apoyo'];
                        $tx->rollback();
                    }
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $organismo = Organismo::findFirst($request['id']);
                    $organismo->setTransaction($tx);

                    if ($organismo->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($organismo);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el organismo de apoyo.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}