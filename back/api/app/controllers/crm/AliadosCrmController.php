<?php

use Phalcon\Mvc\Controller;

class AliadosCrmController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM crm_aliados
            ORDER BY id";
        $this->content['aliados'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByOportunidad ($oportunidad_id) {
        $sql = "SELECT crm_aliados.id, crm_aliados.oportunidad_id, crm_aliados.aliado_id, com_aliados.nombre as aliado FROM crm_aliados inner join com_aliados on crm_aliados.aliado_id = com_aliados.id and crm_aliados.oportunidad_id = $oportunidad_id and crm_aliados.oportunidad_id > 0";
        $this->content['aliados'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $aliados = AliadosCrm::findFirst(
                [
                    'oportunidad_id = :oportunidad_id: AND aliado_id = :aliado_id:',
                    'bind' => [
                        'oportunidad_id' =>intval($request['oportunidad_id']),
                        'aliado_id' => intval($request['aliado_id'])
                    ]
                ]
            );
            if ($aliados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El aliado ya está registrado en esta oportunidad'];
            } else {
                $aliados = new AliadosCrm();
                $aliados->setTransaction($tx);
                $aliados->oportunidad_id = intval($request['oportunidad_id']);
                $aliados->aliado_id = intval($request['aliado_id']);
                if ($aliados->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido el aliado a la oportunidad.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($aliados);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir el aliado a la oportunidad.'];
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

            $aliados = AliadosCrm::findFirst(
                [
                    'id != :id: AND (oportunidad_id = :oportunidad_id:) AND (aliado_id = :aliado_id:)',
                    'bind' => [
                        'oportunidad_id' => intval($request['oportunidad_id']),
                        'aliado_id' => intval($request['aliado_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($aliados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El aliado ya está registrado en esta oportunidad.'];
            } else {
                $aliados = AliadosCrm::findFirst($request['id']);
                if ($aliados) {
                    $aliados->setTransaction($tx);
                    $aliados->oportunidad_id = intval($request['oportunidad_id']);
                    $aliados->aliado_id = intval($request['aliado_id']);
                    if ($aliados->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el aliado.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($aliados);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el aliado'];
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
                    $aliados = AliadosCrm::findFirst($request['id']);
                    $aliados->setTransaction($tx);

                    if ($aliados->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($aliados);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el aliado.'];
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