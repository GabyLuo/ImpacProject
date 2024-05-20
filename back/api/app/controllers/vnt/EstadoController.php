<?php

use Phalcon\Mvc\Controller;

class EstadoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM vnt_estado
            ORDER BY nombre";
        $this->content['estados'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $estados = Estado::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($estados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este estado'];
            } else {
                $estados = new Estado();
                $estados->setTransaction($tx);
                $estados->nombre = strtoupper($request['nombre']);
                if ($estados->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el estado.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($estados);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el estado.'];
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

            $estados = Estado::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($estados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un estado con este nombre'];
            } else {
                $estados = Estado::findFirst($request['id']);
                if ($estados) {
                    $estados->setTransaction($tx);
                    $estados->nombre = strtoupper($request['nombre']);

                    if ($estados->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el estado'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($estados);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el estado'];
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
                    $estados = Estado::findFirst($request['id']);
                    $estados->setTransaction($tx);

                    if ($estados->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($estados);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el estado.'];
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