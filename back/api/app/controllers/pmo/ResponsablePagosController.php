<?php

use Phalcon\Mvc\Controller;

class ResponsablePagosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * FROM pmo_responsable_pagos";

        $this->content['responsable_pagos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id)
    {
        $sql = "SELECT pmo_responsable_pagos.id,pmo_responsable_pagos.proyecto_id,pmo_responsable_pagos.usuario_id, pmo_responsable_pagos.perfil,
        (select sys_users.nickname from sys_users where pmo_responsable_pagos.usuario_id = sys_users.id) as nombre
                FROM pmo_responsable_pagos
                where pmo_responsable_pagos.proyecto_id = $proyecto_id";

        $this->content['responsable_pagos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $responsable_pagos = ResponsablePagos::findFirst(
                [
                    '(proyecto_id = :proyecto_id:) AND (usuario_id = :usuario_id:)',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id']),
                    ]
                ]
            );
            if ($responsable_pagos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El responsable de pagos ya existe en este proyecto'];
            } else {
                $responsable_pagos = new ResponsablePagos();
                $responsable_pagos->setTransaction($tx);
                $responsable_pagos->proyecto_id = intval($request['proyecto_id']);
                $responsable_pagos->usuario_id = intval($request['usuario_id']);
                $responsable_pagos->perfil = trim($request['perfil']);

                if ($responsable_pagos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el responsable de pagos.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($responsable_pagos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el responsable de pagos.'];
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
            $responsable_pagos = ResponsablePagos::findFirst(
                [
                    'id != :id: AND ((proyecto_id = :proyecto_id:) AND (usuario_id = :usuario_id:))',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id']),
                    ]
                ]
            );
            if ($responsable_pagos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El responsable de pagos ya existe en este proyecto'];
            } else {
                $responsable_pagos = ResponsablePagos::findFirst($request['id']);
                if ($responsable_pagos) {
                    $responsable_pagos->setTransaction($tx);
                    $responsable_pagos->usuario_id = intval($request['usuario_id']);
                    $responsable_pagos->perfil = trim($request['perfil']);
                        
                    if ($responsable_pagos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el responsable de pagos.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($responsable_pagos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha podido actualizar el responsable de pagos.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = 'Error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró el responsable de pagos.'];
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
                    $responsable_pagos = ResponsablePagos::findFirst($request['id']);
                    $responsable_pagos->setTransaction($tx);

                    if ($responsable_pagos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($responsable_pagos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el responsable de pagos.'];
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