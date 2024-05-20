<?php

use Phalcon\Mvc\Controller;

class DestinosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, code, domicilio,recibe_persona, nombre_destino, created FROM crm_destinos 
                ORDER BY crm_destinos.code desc";
        $this->content['destinos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getById ($id)
    {
        $sql = "SELECT id, code, domicilio,recibe_persona, nombre_destino, created_by, created FROM crm_destinos 
                where crm_destinos.id = $id 
                ORDER BY inv_lineas.nombre";
        $this->content['destinos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $destinos = Destinos::findFirst(
                [
                    'code = :code:',
                    'bind' => [
                        'code' => strtoupper($request['code'])
                    ]
                ]
            );
            if ($destinos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un destino con este codigo'];
            } else {
                $destinos = new Destinos();
                $destinos->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $destinos->created_by = $validUser->user_id;
                
                $destinos->domicilio =  trim(strtoupper($request['domicilio']));
                $destinos->code = trim(strtoupper($request['code']));
                $destinos->recibe_persona = trim(strtoupper($request['recibe_persona']));
                $destinos->nombre_destino = trim(strtoupper($request['nombre_destino']));
                if ($destinos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el destino.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($destinos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el destino.'];
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
            $destinos = Destinos::findFirst(
                [
                    'id != :id:  AND code = :code:',
                    'bind' => [
                        'id' => intval($request['id']),
                        'code' => strtoupper($request['code'])
                    ]
                ]
            );
            if ($destinos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un destino  con esta información'];
            } else {
                $destinos = Destinos::findFirst($request['id']);
                if ($destinos) {
                    $destinos->setTransaction($tx);
                    
                    $destinos->domicilio =  trim(strtoupper($request['domicilio']));
                    $destinos->code = trim(strtoupper($request['code']));
                    $destinos->recibe_persona = trim(strtoupper($request['recibe_persona']));
                    $destinos->nombre_destino = trim(strtoupper($request['nombre_destino']));
                    if ($destinos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el destino.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($destinos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el destino'];
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
                    $destino = Destinos::findFirst($request['id']);
                    $destino->setTransaction($tx);

                    if ($destino->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($destino);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el destino.'];
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