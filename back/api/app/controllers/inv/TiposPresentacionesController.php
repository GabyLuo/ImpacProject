<?php

use Phalcon\Mvc\Controller;

class TiposPresentacionesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *
            FROM inv_tipos_presentaciones
            ORDER BY nombre";
        $this->content['tipos_presentaciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $tipos_presentaciones = TiposPresentaciones::findFirst(
                [
                    'nombre = :nombre: AND codigo = :codigo:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($tipos_presentaciones) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un tipo de unidad con este nombre'];
            } else {
                $tipos_presentaciones = new TiposPresentaciones();
                $tipos_presentaciones->setTransaction($tx);
                $tipos_presentaciones->nombre = trim(strtoupper($request['nombre']));
                $tipos_presentaciones->codigo = trim(strtoupper($request['codigo']));
                $tipos_presentaciones->codigo_sat = trim(strtoupper($request['codigo_sat']));
                if ($tipos_presentaciones->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la unidad.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($tipos_presentaciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la unidad.'];
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
            $tipos_presentaciones = TiposPresentaciones::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (codigo = :codigo:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'codigo' => strtoupper($request['codigo'])
                    ]
                ]
            );
            if ($tipos_presentaciones) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una unidad con este nombre'];
            } else {
                $tipos_presentaciones = TiposPresentaciones::findFirst($request['id']);
                if ($tipos_presentaciones) {
                    $tipos_presentaciones->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $tipos_presentaciones->nombre = trim(strtoupper($request['nombre']));
                    $tipos_presentaciones->codigo = trim(strtoupper($request['codigo']));
                    $tipos_presentaciones->codigo_sat = trim(strtoupper($request['codigo_sat']));
                    if ($tipos_presentaciones->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la unidad.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_presentaciones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la unidad'];
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
                    $tipos_presentaciones = TiposPresentaciones::findFirst($request['id']);
                    $tipos_presentaciones->setTransaction($tx);

                    if ($tipos_presentaciones->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_presentaciones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la unidad.'];
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