<?php

use Phalcon\Mvc\Controller;

class AliadosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, razon_social, nombre, rfc, coalesce(rfc, 'SIN RFC') as rfc_label, area, estado_id, municipio_id, descripcion, (select nombre as estado from vnt_estado where vnt_estado.id = com_aliados.estado_id), (select nombre as municipio from vnt_municipio where vnt_municipio.id = com_aliados.municipio_id)
            FROM com_aliados
            ORDER BY razon_social, nombre";
        $this->content['aliados'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $aliados = Aliados::findFirst(
                [
                    'razon_social = :razon_social: AND nombre = :nombre:',
                    'bind' => [
                        'razon_social' => strtoupper($request['razon_social']),
                        'nombre' => strtoupper($request['nombre']),
                    ]
                ]
            );
            if ($aliados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un aliado con estos datos'];
            } else {
                $aliados = new Aliados();
                $aliados->setTransaction($tx);
                $aliados->razon_social = trim(strtoupper($request['razon_social']));
                $aliados->nombre = trim(strtoupper($request['nombre']));
                if (trim($request['rfc']) === '') {
                    $aliados->rfc = null;
                } else {
                    $aliados->rfc = trim(strtoupper($request['rfc']));
                }
                $aliados->estado_id = intval($request['estado_id']);
                $aliados->municipio_id = intval($request['municipio_id']);
                if (trim($request['area']) == '') {
                    $aliados->area = null;
                } else {
                    $aliados->area = trim(strtoupper($request['area']));
                }
                if (trim($request['descripcion']) == '') {
                    $aliados->descripcion = null;
                } else {
                    $aliados->descripcion = trim(strtoupper($request['descripcion']));
                }
                if ($aliados->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el aliado.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($aliados);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el aliado.'];
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
            $aliados = Aliados::findFirst(
                [
                    'id != :id: AND nombre = :nombre: AND razon_social = :razon_social:',
                    'bind' => [
                        'razon_social' => strtoupper($request['razon_social']),
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($aliados) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un aliado con estos datos'];
            } else {
                $aliados = Aliados::findFirst($request['id']);
                if ($aliados) {
                    $aliados->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $aliados->razon_social = trim(strtoupper($request['razon_social']));
                    $aliados->nombre = trim(strtoupper($request['nombre']));
                    if (trim($request['rfc']) === '') {
                        $aliados->rfc = null;
                    } else {
                        $aliados->rfc = trim(strtoupper($request['rfc']));
                    }
                    $aliados->estado_id = intval($request['estado_id']);
                    $aliados->municipio_id = intval($request['municipio_id']);
                    if (trim($request['area']) == '') {
                        $aliados->area = null;
                    } else {
                        $aliados->area = trim(strtoupper($request['area']));
                    }
                    if (trim($request['descripcion']) == '') {
                        $aliados->descripcion = null;
                    } else {
                        $aliados->descripcion = trim(strtoupper($request['descripcion']));
                    }
                    if ($aliados->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el aliado.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el aliado'];
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
                    $aliados = Aliados::findFirst($request['id']);
                    $aliados->setTransaction($tx);

                    if ($aliados->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($aliados);
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