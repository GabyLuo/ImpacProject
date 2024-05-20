<?php

use Phalcon\Mvc\Controller;

class MunicipioController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_municipio.id,vnt_municipio.nombre,vnt_municipio.estado_id,vnt_estado.nombre as estado
                from vnt_municipio join vnt_estado on vnt_municipio.estado_id=vnt_estado.id
                order by vnt_estado.nombre,vnt_municipio.nombre";
        $this->content['municipios'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEstado($estado_id) {
        $sql = "SELECT vnt_municipio.id,vnt_municipio.nombre,vnt_municipio.estado_id,vnt_estado.nombre as estado
                from vnt_municipio join vnt_estado on vnt_municipio.estado_id=vnt_estado.id
                where vnt_municipio.estado_id=$estado_id
                order by vnt_municipio.nombre";
        $this->content['municipios'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $municipios = Municipio::findFirst(
                [
                    'nombre = :nombre: AND (estado_id = :estado_id:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'estado_id' => intval($request['estado_id'])
                    ]
                ]
            );
            if ($municipios) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este municipio en este estado'];
            } else {
                $municipios = new Municipio();
                $municipios->setTransaction($tx);
                $municipios->nombre = strtoupper($request['nombre']);
                $municipios->estado_id = intval($request['estado_id']);
                if ($municipios->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el municipio.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($municipios);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el municipio.'];
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

            $municipios = Municipio::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (estado_id = :estado_id:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'estado_id' => intval($request['estado_id'])
                    ]
                ]
            );
            if ($municipios) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este municipio en este estado'];
            } else {
                $municipios = Municipio::findFirst($request['id']);
                if ($municipios) {
                    $municipios->setTransaction($tx);
                    $municipios->nombre = strtoupper($request['nombre']);
                    $municipios->estado_id = intval($request['estado_id']);
                    if ($municipios->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el municipio.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($municipios);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el municipio.'];
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
                    $municipios = Municipio::findFirst($request['id']);
                    $municipios->setTransaction($tx);

                    if ($municipios->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($municipios);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el municipio.'];
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