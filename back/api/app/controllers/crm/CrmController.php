<?php

use Phalcon\Mvc\Controller;

class CrmController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM crm_carriles
            ORDER BY id";
        $this->content['crm'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $crm = Crm::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($crm) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este carril'];
            } else {
                $crm = new Crm();
                $crm->setTransaction($tx);
                $crm->nombre = strtoupper($request['nombre']);
                if ($crm->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el carril.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($crm);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el carril.'];
                    $tx->rollback();
                }
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

            $crm = Crm::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($crm) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un carril con este nombre'];
            } else {
                $crm = Crm::findFirst($request['id']);
                if ($crm) {
                    $crm->setTransaction($tx);
                    $crm->nombre = strtoupper($request['nombre']);

                    if ($crm->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el carril'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($crm);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el carril'];
                        $tx->rollback();
                    }
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
                    $crm = Crm::findFirst($request['id']);
                    $crm->setTransaction($tx);

                    if ($crm->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($crm);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el carril.'];
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