<?php

use Phalcon\Mvc\Controller;

class AreasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_areas
            ORDER BY nombre";
        $this->content['areas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $areas = Areas::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($areas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esta área'];
            } else {
                $areas = new Areas();
                $areas->setTransaction($tx);
                $areas->nombre = strtoupper($request['nombre']);
                if ($areas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la área.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($areas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la área.'];
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

            $areas = Areas::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($areas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un área con este nombre'];
            } else {
                $areas = Areas::findFirst($request['id']);
                if ($areas) {
                    $areas->setTransaction($tx);
                    $areas->nombre = strtoupper($request['nombre']);

                    if ($areas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el área'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($areas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el área'];
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
                    $areas = Areas::findFirst($request['id']);
                    $areas->setTransaction($tx);

                    if ($areas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($areas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el área.'];
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