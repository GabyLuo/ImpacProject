<?php

use Phalcon\Mvc\Controller;

class GirosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM crm_giros
            ORDER BY nombre";
        $this->content['giros'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $giros = Giros::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($giros) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un giro comercial con este nombre'];
            } else {
                $giros = new Giros();
                $giros->setTransaction($tx);
                $giros->nombre = trim(strtoupper($request['nombre']));
                if ($giros->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido el giro a la lista general.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($giros);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir el giro a la lista general.'];
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

            $giros = Giros::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($giros) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un giro comercial con este nombre.'];
            } else {
                $giros = Giros::findFirst($request['id']);
                if ($giros) {
                    $giros->setTransaction($tx);
                    $giros->nombre = trim(strtoupper($request['nombre']));
                    if ($giros->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el giro.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($giros);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el giro'];
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
                    $giros = Giros::findFirst($request['id']);
                    $giros->setTransaction($tx);

                    if ($giros->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($giros);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el giro.'];
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