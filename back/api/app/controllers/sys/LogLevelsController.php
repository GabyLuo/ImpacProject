<?php

use Phalcon\Mvc\Controller;

class LogLevelsController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, name
                FROM sys_log_levels 
                order by name";

        $logs = $this->content['logs'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $logs = LogLevels::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => trim(strtoupper($request['name']))
                    ]
                ]
            );
            if ($logs) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con estos datos.'];
            } else {
                $logs = new LogLevels();
                $logs->setTransaction($tx);
                $logs->name = trim(strtoupper($request['name']));
                
                if ($logs->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el log.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($logs);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el log.'];
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

            $logs = LogLevels::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => trim(strtoupper($request['name']))
                    ]
                ]
            );
            
            if ($logs) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un log que tiene estos datos.'];
            } else {
                $logs = LogLevels::findFirst($request['id']);
                if ($logs) {
                    $logs->setTransaction($tx);
                    $logs->name = trim(strtoupper($request['name']));
                    if ($logs->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el log'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($logs);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el log'];
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
                    $logs = LogLevels::findFirst($request['id']);
                    $logs->setTransaction($tx);

                    if ($logs->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($logs);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el log.'];
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