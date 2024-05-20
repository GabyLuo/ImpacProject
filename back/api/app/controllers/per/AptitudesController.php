<?php

use Phalcon\Mvc\Controller;

class AptitudesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_aptitudes
            ORDER BY nombre";
        $this->content['aptitudes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $aptitudes = Aptitudes::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($aptitudes) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una aptitud con este nombre'];
            } else {
                $aptitudes = new Aptitudes();
                $aptitudes->setTransaction($tx);
                $aptitudes->nombre = trim(strtoupper($request['nombre']));
                if ($aptitudes->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido la aptitud a la lista general.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($aptitudes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir la aptitud a la lista general.'];
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

            $aptitudes = Aptitudes::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($aptitudes) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una aptitud con este nombre.'];
            } else {
                $aptitudes = Aptitudes::findFirst($request['id']);
                if ($aptitudes) {
                    $aptitudes->setTransaction($tx);
                    $aptitudes->nombre = trim(strtoupper($request['nombre']));
                    if ($aptitudes->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la aptitud.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($aptitudes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la aptitud'];
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
                    $aptitudes = Aptitudes::findFirst($request['id']);
                    $aptitudes->setTransaction($tx);

                    if ($aptitudes->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($aptitudes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la aptitud.'];
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