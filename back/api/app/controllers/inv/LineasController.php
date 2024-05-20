<?php

use Phalcon\Mvc\Controller;

class LineasController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT inv_lineas.id, inv_lineas.categoria_id, inv_lineas.codigo, inv_lineas.nombre, inv_tipos_articulos.nombre as categoria, inv_tipos_articulos.codigo as codigo_categoria FROM inv_lineas 
                inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_lineas.categoria_id 
                ORDER BY inv_lineas.nombre";
        $this->content['lineas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCategoria ($categoria_id)
    {
        $sql = "SELECT inv_lineas.id, inv_lineas.categoria_id, inv_lineas.codigo, inv_lineas.nombre, inv_tipos_articulos.nombre as categoria
                FROM inv_lineas
                inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_lineas.categoria_id
                and inv_lineas.categoria_id = $categoria_id 
                ORDER BY inv_lineas.nombre";
        $this->content['lineas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $lineas = Lineas::findFirst(
                [
                    'nombre = :nombre: AND categoria_id = :categoria_id: AND codigo = :codigo:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'categoria_id' => intval($request['categoria_id']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($lineas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una línea con este nombre'];
            } else {
                $lineas = new Lineas();
                $lineas->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $lineas->created_by = $validUser->user_id;
                $lineas->nombre = trim(strtoupper($request['nombre']));
                $lineas->categoria_id = intval($request['categoria_id']);
                $lineas->codigo = trim(strtoupper($request['codigo']));
                if ($lineas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la línea.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($lineas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la línea.'];
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
            $lineas = Lineas::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND categoria_id = :categoria_id: AND codigo = :codigo:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'categoria_id' => intval($request['categoria_id']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($lineas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una línea con este nombre'];
            } else {
                $lineas = Lineas::findFirst($request['id']);
                if ($lineas) {
                    $lineas->setTransaction($tx);
                    $lineas->nombre = trim(strtoupper($request['nombre']));
                    $lineas->categoria_id = intval($request['categoria_id']);
                    $lineas->codigo = trim(strtoupper($request['codigo']));
                    if ($lineas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la línea.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($lineas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la línea'];
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
                    $lineas = Lineas::findFirst($request['id']);
                    $lineas->setTransaction($tx);

                    if ($lineas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($lineas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la línea.'];
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