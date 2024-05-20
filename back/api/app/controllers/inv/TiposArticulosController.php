<?php

use Phalcon\Mvc\Controller;

class TiposArticulosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *
            FROM inv_tipos_articulos
            ORDER BY nombre";
        $this->content['tipos_articulos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $tipos_articulos = TiposArticulos::findFirst(
                [
                    'nombre = :nombre: AND codigo = :codigo:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($tipos_articulos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una categoría con este nombre'];
            } else {
                $tipos_articulos = new TiposArticulos();
                $tipos_articulos->setTransaction($tx);
                $tipos_articulos->nombre = trim(strtoupper($request['nombre']));
                $tipos_articulos->codigo = trim(strtoupper($request['codigo']));
                if ($tipos_articulos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la categoría.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($tipos_articulos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la categoría.'];
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
            $tipos_articulos = TiposArticulos::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (codigo = :codigo:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($tipos_articulos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una categoría con este nombre'];
            } else {
                $tipos_articulos = TiposArticulos::findFirst($request['id']);
                if ($tipos_articulos) {
                    $tipos_articulos->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $tipos_articulos->nombre = trim(strtoupper($request['nombre']));
                    $tipos_articulos->codigo = trim(strtoupper($request['codigo']));
                    if ($tipos_articulos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la categoría.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_articulos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la categoría'];
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
                    $tipos_articulos = TiposArticulos::findFirst($request['id']);
                    $tipos_articulos->setTransaction($tx);

                    if ($tipos_articulos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_articulos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la categoría.'];
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