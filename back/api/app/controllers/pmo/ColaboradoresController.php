<?php

use Phalcon\Mvc\Controller;

class ColaboradoresController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * FROM pmo_colaboradores";

        $this->content['colaboradores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id)
    {
        $sql = "SELECT pmo_colaboradores.id,pmo_colaboradores.proyecto_id,pmo_colaboradores.usuario_id, pmo_colaboradores.perfil,
        (select sys_users.nickname from sys_users where pmo_colaboradores.usuario_id = sys_users.id) as nombre
                FROM pmo_colaboradores
                where pmo_colaboradores.proyecto_id = $proyecto_id";

        $this->content['colaboradores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $colaboradores = Colaboradores::findFirst(
                [
                    'proyecto_id = :proyecto_id: AND (usuario_id = :usuario_id:)',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id'])
                    ]
                ]
            );
            if ($colaboradores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este colaborador ya está registrado en este proyecto'];
            } else {
                $colaboradores = new Colaboradores();
                $colaboradores->setTransaction($tx);
                $colaboradores->proyecto_id = intval($request['proyecto_id']);
                $colaboradores->usuario_id = intval($request['usuario_id']);
                $colaboradores->perfil = trim($request['perfil']);

                if ($colaboradores->create()) {
                    $solicitantes = new Solicitantes();
                    $solicitantes->setTransaction($tx);
                    $solicitantes->proyecto_id = intval($request['proyecto_id']);
                    $solicitantes->usuario_id = intval($request['usuario_id']);
                    $solicitantes->perfil = trim($request['perfil']);

                    if ($solicitantes->create()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el colaborador.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($solicitantes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el colaborador.'];
                        $tx->rollback();
                    }
                    /* $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el solicitante.'];
                    $tx->commit(); */
                } else {
                    $this->content['error'] = Helpers::getErrors($colaboradores);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el solicitante.'];
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

    public function update () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $colaboradores = Colaboradores::findFirst(
                [
                    'id != :id: AND (proyecto_id = :proyecto_id:) AND (usuario_id = :usuario_id:)',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($colaboradores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este colaborador ya está registrado en este proyecto'];
            } else {
                $colaboradores = Colaboradores::findFirst($request['id']);
                if ($colaboradores) {
                    $colaboradores->setTransaction($tx);
                    $colaboradores->usuario_id = intval($request['usuario_id']);
                    $colaboradores->perfil = trim($request['perfil']);

                    if ($colaboradores->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el colaborador.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($colaboradores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el colaborador.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = 'Error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró el colaborador.'];
                }
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
                    $colaboradores = Colaboradores::findFirst($request['id']);
                    $colaboradores->setTransaction($tx);

                    if ($colaboradores->delete()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el archivo.'];

                    } else {
                        $this->content['error'] = Helpers::getErrors($colaboradores);
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