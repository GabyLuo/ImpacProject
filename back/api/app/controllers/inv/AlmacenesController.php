<?php

use Phalcon\Mvc\Controller;

class AlmacenesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT inv_almacenes.id, inv_almacenes.created, inv_almacenes.created_by, inv_almacenes.modified, inv_almacenes.modified_by, inv_almacenes.empresa_id, inv_almacenes.nombre, inv_almacenes.clave, inv_almacenes.calle, inv_almacenes.codigo_postal, inv_almacenes.municipio_id, inv_almacenes.colonia, inv_almacenes.contacto, inv_almacenes.telefono, inv_almacenes.estado_id, inv_almacenes.tipo, vnt_empresa.razon_social, case when inv_almacenes.empresa_id is not null then (vnt_empresa.razon_social || ' - ' || inv_almacenes.nombre) else inv_almacenes.nombre end as nombreopt
            FROM inv_almacenes left join vnt_empresa on vnt_empresa.id = inv_almacenes.empresa_id
            ORDER BY nombre";
        $this->content['almacenes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresa ($empresa_id)
    {
        if ($empresa_id > 0) {
            $sql = "SELECT inv_almacenes.id, inv_almacenes.created, inv_almacenes.created_by, inv_almacenes.modified, inv_almacenes.modified_by, inv_almacenes.empresa_id, inv_almacenes.nombre, inv_almacenes.clave, inv_almacenes.calle, inv_almacenes.codigo_postal, inv_almacenes.municipio_id, inv_almacenes.colonia, inv_almacenes.contacto, inv_almacenes.telefono, inv_almacenes.estado_id, inv_almacenes.tipo, vnt_empresa.razon_social
            FROM inv_almacenes inner join vnt_empresa on vnt_empresa.id = inv_almacenes.empresa_id
            where inv_almacenes.empresa_id = $empresa_id
            ORDER BY nombre";
        } else {
            $sql = "SELECT inv_almacenes.id, inv_almacenes.created, inv_almacenes.created_by, inv_almacenes.modified, inv_almacenes.modified_by, inv_almacenes.nombre, inv_almacenes.clave, inv_almacenes.calle, inv_almacenes.codigo_postal, inv_almacenes.municipio_id, inv_almacenes.colonia, inv_almacenes.contacto, inv_almacenes.telefono, inv_almacenes.estado_id, inv_almacenes.tipo
            FROM inv_almacenes where inv_almacenes.empresa_id is null
            ORDER BY nombre";
        }
        
        $almacenes = $this->db->query($sql)->fetchAll();
        $materia_prima = 0;
        $produccion = 0;
        $producto_terminado = 0;
        foreach ($almacenes as $almacen) {
            if ($almacen['tipo'] === 'MATERIA PRIMA') {
                $materia_prima = $almacen['id'];
            }
            if ($almacen['tipo'] === 'PRODUCCIÓN') {
                $produccion = $almacen['id'];
            }
            if ($almacen['tipo'] === 'PRODUCTO TERMINADO') {
                $producto_terminado = $almacen['id'];
            }
        }
        $this->content['almacenes'] = $almacenes;
        $this->content['materia_prima'] = $materia_prima;
        $this->content['produccion'] = $produccion;
        $this->content['producto_terminado'] = $producto_terminado;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $almacen = Almacenes::findFirst(
                [
                    'empresa_id = :empresa_id: AND tipo = :tipo:',
                    'bind' => [
                        'empresa_id' => intval($request['empresa_id']),
                        'tipo' => $request['tipo']
                    ]
                ]
            );
            if ($almacen) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un almacén con este nombre o tipo para esta empresa'];
            } else {
                $almacen = new Almacenes();
                $almacen->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $id = $validUser->user_id;
                $almacen->created_by = $id;
                $almacen->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
                $almacen->nombre = trim(strtoupper($request['nombre']));
                if (trim($request['clave']) === '') {
                    $almacen->clave = null;
                } else {
                    $almacen->clave = trim(strtoupper($request['clave']));
                }
                if (trim($request['calle']) === '') {
                    $almacen->calle = null;
                } else {
                    $almacen->calle = trim(strtoupper($request['calle']));
                }
                if (trim($request['codigo_postal']) === '') {
                    $almacen->codigo_postal = null;
                } else {
                    $almacen->codigo_postal = trim(strtoupper($request['codigo_postal']));
                }
                if (trim($request['colonia']) === '') {
                    $almacen->colonia = null;
                } else {
                    $almacen->colonia = trim(strtoupper($request['colonia']));
                }
                if (trim($request['contacto']) === '') {
                    $almacen->contacto = null;
                } else {
                    $almacen->contacto = trim(strtoupper($request['contacto']));
                }
                if (trim($request['telefono']) === '') {
                    $almacen->telefono = null;
                } else {
                    $almacen->telefono = trim(strtoupper($request['telefono']));
                }
                $almacen->municipio_id = intval($request['municipio_id']);
                $almacen->estado_id = intval($request['estado_id']);
                $almacen->tipo = $request['tipo'];

                if ($almacen->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el almacén.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($almacen);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el almacén.'];
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
            $almacen = Almacenes::findFirst(
                [
                    'id != :id: AND (empresa_id = :empresa_id:) AND (tipo = :tipo:)',
                    'bind' => [
                        'empresa_id' => intval($request['empresa_id']),
                        'tipo' => $request['tipo'],
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($almacen) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un almacén con este nombre o tipo para esta empresa'];
            } else {
                $almacen = Almacenes::findFirst($request['id']);
                if ($almacen) {
                    $almacen->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $id = $validUser->user_id;
                    $almacen->modified_by = $id;
                    $almacen->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
                    $almacen->nombre = trim(strtoupper($request['nombre']));
                    if (trim($request['clave']) === '') {
                        $almacen->clave = null;
                    } else {
                        $almacen->clave = trim(strtoupper($request['clave']));
                    }
                    if (trim($request['calle']) === '') {
                        $almacen->calle = null;
                    } else {
                        $almacen->calle = trim(strtoupper($request['calle']));
                    }
                    if (trim($request['codigo_postal']) === '') {
                        $almacen->codigo_postal = null;
                    } else {
                        $almacen->codigo_postal = trim(strtoupper($request['codigo_postal']));
                    }
                    if (trim($request['colonia']) === '') {
                        $almacen->colonia = null;
                    } else {
                        $almacen->colonia = trim(strtoupper($request['colonia']));
                    }
                    if (trim($request['contacto']) === '') {
                        $almacen->contacto = null;
                    } else {
                        $almacen->contacto = trim(strtoupper($request['contacto']));
                    }
                    if (trim($request['telefono']) === '') {
                        $almacen->telefono = null;
                    } else {
                        $almacen->telefono = trim(strtoupper($request['telefono']));
                    }
                    $almacen->municipio_id = intval($request['municipio_id']);
                    $almacen->estado_id = intval($request['estado_id']);
                    $almacen->tipo = $request['tipo'];

                    if ($almacen->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el almacén.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($almacen);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el almacén'];
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
                    $almacen = Almacenes::findFirst($request['id']);
                    $almacen->setTransaction($tx);

                    if ($almacen->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($almacen);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el almacén.'];
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