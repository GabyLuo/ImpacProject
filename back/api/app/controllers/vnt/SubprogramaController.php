<?php

use Phalcon\Mvc\Controller;

class SubprogramaController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_subprograma.id,programa_id,vnt_programa.nombre as programa_nombre,
                vnt_subprograma.nombre as subprograma_nombre,
                vnt_subprograma.codigo
                from vnt_programa join vnt_subprograma on vnt_programa.id = vnt_subprograma.programa_id
                order by programa_nombre,subprograma_nombre";
        
        $this->content['subprogramas'] = $this->db->query($sql)->fetchAll();

        foreach ($this->content['subprogramas'] as $key => $subprograma) {
            $subprograma_id = $subprograma['id'];
            $totalRecursos = "SELECT count(vnt_recurso.id) as total_recursos
                            from vnt_recurso
                            where subprograma_id = $subprograma_id";
            $this->content['subprogramas'][$key]['total_recursos'] = $this->db->query($totalRecursos)->fetchAll()[0]['total_recursos'];
        }

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $subprogramas = Subprograma::findFirst(
                [
                    'codigo = :codigo: OR (nombre = :nombre: AND (programa_id = :programa_id:))',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'programa_id' => intval($request['programa_id']),
                        'codigo' => strtoupper($request ['codigo'])
                    ]
                ]
            );
            if ($subprogramas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este subprograma con estos datos'];
            } else {
                $subprogramas = new Subprograma();
                $subprogramas->setTransaction($tx);
                $subprogramas->nombre = strtoupper($request['nombre']);
                $subprogramas->programa_id = intval($request['programa_id']);
                $subprogramas->codigo = strtoupper($request['codigo']);
                if ($subprogramas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el subprograma.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($subprogramas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el subprograma.'];
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

            $subprogramas = Subprograma::findFirst(
                [
                    'id != :id: AND ((nombre = :nombre: AND programa_id = :programa_id:) OR codigo = :codigo:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'programa_id' => intval($request['programa_id']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($subprogramas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este subprograma con estos datos'];
            } else {
                $subprogramas = Subprograma::findFirst($request['id']);
                if ($subprogramas) {
                    $subprogramas->setTransaction($tx);
                    $subprogramas->nombre = strtoupper($request['nombre']);
                    $subprogramas->programa_id = intval($request['programa_id']);
                    $subprogramas->codigo = strtoupper($request['codigo']);
                    if ($subprogramas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el subprograma.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($subprogramas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el subprograma'];
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
                    $subprogramas = Subprograma::findFirst($request['id']);
                    $subprogramas->setTransaction($tx);

                    if ($subprogramas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($subprogramas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el subprograma.'];
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