<?php

use Phalcon\Mvc\Controller;

class ProgramaController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM vnt_programa
            ORDER BY nombre";
        $this->content['programas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $programas = Programa::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($programas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un programa con este nombre'];
            } else {
                $programas = new Programa();
                $programas->setTransaction($tx);
                $programas->nombre = strtoupper($request['nombre']);
                if ($programas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el programa.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($programas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el programa.'];
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

            $programas = Programa::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($programas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un programa con este nombre'];
            } else {
                $programas = Programa::findFirst($request['id']);
                if ($programas) {
                    $programas->setTransaction($tx);
                    $programas->nombre = strtoupper($request['nombre']);

                    if ($programas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el programa.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($programas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el programa'];
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
                    $programas = Programa::findFirst($request['id']);
                    $programas->setTransaction($tx);

                    if ($programas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($programas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el programa.'];
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