<?php

use Phalcon\Mvc\Controller;

class AutorizadoresController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * FROM pmo_autorizadores";

        $this->content['autorizadores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id)
    {
        $sql = "SELECT pmo_autorizadores.id, pmo_autorizadores.proyecto_id, pmo_autorizadores.usuario_id, pmo_autorizadores.orden, pmo_autorizadores.perfil, pmo_autorizadores.alteracion,
        (select sys_users.nickname from sys_users where pmo_autorizadores.usuario_id = sys_users.id) as nombre
                FROM pmo_autorizadores
                where pmo_autorizadores.proyecto_id = $proyecto_id
                order by orden";

        $autorizadores = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($autorizadores as $elemento){
            $g=(object)array();
            $g->id = $elemento['id'];
            $g->proyecto_id = $elemento['proyecto_id'];
            $g->usuario_id = $elemento['usuario_id'];
            $g->orden = $elemento['orden'];
            $g->perfil = $elemento['perfil'];
            $g->nombre = $elemento['nombre'];
            if(intval($elemento['alteracion']) === 0){
                $g->alteracion = 'NO';
            } else {
                $g->alteracion ='SI';
            }           
            array_push($nuevo,$g);
        }

        $this->content['autorizadores'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $autorizadores = Autorizadores::findFirst(
                [
                    '((proyecto_id = :proyecto_id:) AND (usuario_id = :usuario_id:)) OR ((orden = :orden:) AND (proyecto_id = :proyecto_id:))',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id']),
                        'orden' => intval($request['orden'])
                    ]
                ]
            );
            if ($autorizadores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El autorizador o el nivel ya existe en este proyecto'];
            } else {
                $autorizadores = new Autorizadores();
                $autorizadores->setTransaction($tx);
                $autorizadores->proyecto_id = intval($request['proyecto_id']);
                $autorizadores->usuario_id = intval($request['usuario_id']);
                $autorizadores->perfil = trim($request['perfil']);
                $autorizadores->orden = intval($request['orden']);
                if($request['alteracion'] === 'SI'){
                    $autorizadores->alteracion = true;
                } else {
                    $autorizadores->alteracion = false;
                }

                if ($autorizadores->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el autorizador.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($autorizadores);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el autorizador.'];
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
            $autorizadores = Autorizadores::findFirst(
                [
                    'id != :id: AND (((proyecto_id = :proyecto_id:) AND (usuario_id = :usuario_id:)) OR ((orden = :orden:) AND (proyecto_id = :proyecto_id:)))',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'usuario_id' => intval($request['usuario_id']),
                        'orden' => intval($request['orden']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($autorizadores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El autorizador o el nivel ya existe en este proyecto'];
            } else {
                $autorizadores = Autorizadores::findFirst($request['id']);
                if ($autorizadores) {
                    $autorizadores->setTransaction($tx);
                    $autorizadores->usuario_id = intval($request['usuario_id']);
                    $autorizadores->perfil = trim($request['perfil']);
                    $autorizadores->orden = intval($request['orden']);
                    if($request['alteracion'] === 'SI'){
                        $autorizadores->alteracion = true;
                    } else {
                        $autorizadores->alteracion = false;
                    }

                    if ($autorizadores->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el autorizador.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($autorizadores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el autorizador.'];
                        $tx->rollback();
                    }                
                } else {
                    $this->content['result'] = 'Error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'El autorizador no fue encontrado.'];
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
                    $autorizadores = Autorizadores::findFirst($request['id']);
                    $autorizadores->setTransaction($tx);

                    if ($autorizadores->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($autorizadores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el autorizador.'];
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