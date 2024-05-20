<?php

use Phalcon\Mvc\Controller;

class SolicitantesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * FROM pmo_solicitantes";

        $this->content['solicitantes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id)
    {
        $sql = "SELECT pmo_solicitantes.id,pmo_solicitantes.proyecto_id,pmo_solicitantes.usuario_id, pmo_solicitantes.perfil,
        (select sys_users.nickname from sys_users where pmo_solicitantes.usuario_id = sys_users.id) as nombre
                FROM pmo_solicitantes
                where pmo_solicitantes.proyecto_id = $proyecto_id";

        $this->content['solicitantes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

                $solicitantes = new Solicitantes();
                $solicitantes->setTransaction($tx);
                $solicitantes->proyecto_id = intval($request['proyecto_id']);
                $solicitantes->usuario_id = intval($request['usuario_id']);
                $solicitantes->perfil = trim($request['perfil']);

                if ($solicitantes->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el solicitante.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitantes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el solicitante.'];
                    $tx->rollback();
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $solicitantes = Solicitantes::findFirst($request['id']);
            if ($solicitantes) {
                $solicitantes->setTransaction($tx);
                $solicitantes->usuario_id = intval($request['usuario_id']);
                $solicitantes->perfil = trim($request['perfil']);

                if ($solicitantes->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el solicitante.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitantes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el solicitante'];
                    $tx->rollback();
                } 
            } else {
                $this->content['error'] = 'Error';
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró el solicitante.'];    
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete () {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $solicitantes = Solicitantes::findFirst($request['id']);
                    $solicitantes->setTransaction($tx);

                    if ($solicitantes->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($solicitantes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el solicitante.'];
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