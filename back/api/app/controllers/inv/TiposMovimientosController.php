<?php

use Phalcon\Mvc\Controller;

class TiposMovimientosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *
            FROM inv_tipos_movimientos
            ORDER BY nombre";
        $this->content['tipos_movimientos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $tipos_movimientos = TiposMovimientos::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($tipos_movimientos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un tipo de movimiento con este nombre'];
            } else {
                $tipos_movimientos = new TiposMovimientos();
                $tipos_movimientos->setTransaction($tx);
                $tipos_movimientos->nombre = trim(strtoupper($request['nombre']));
                if ($tipos_movimientos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el tipo de movimiento.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($tipos_movimientos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el tipo de movimiento.'];
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
            $tipos_movimientos = TiposMovimientos::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($tipos_movimientos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un tipo de movimiento con este nombre'];
            } else {
                $tipos_movimientos = TiposMovimientos::findFirst($request['id']);
                if ($tipos_movimientos) {
                    $tipos_movimientos->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $tipos_movimientos->nombre = trim(strtoupper($request['nombre']));
                    if ($tipos_movimientos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el tipo de movimiento.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el tipo de movimiento'];
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
                    $tipos_movimientos = TiposMovimientos::findFirst($request['id']);
                    $tipos_movimientos->setTransaction($tx);

                    if ($tipos_movimientos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el tipo de movimiento.'];
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