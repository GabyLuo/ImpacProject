<?php

use Phalcon\Mvc\Controller;

class VntDireccionController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_direccion.id,vnt_direccion.cliente_id,vnt_clientes.razon_social as cliente,
                vnt_direccion.tipo_vialidad,vnt_direccion.calle,vnt_direccion.num_ext,
                vnt_direccion.num_int,vnt_direccion.colonia, vnt_direccion.poblacion,
                vnt_direccion.municipio_id,vnt_municipio.nombre as municipio_nombre,
                vnt_direccion.estado_id, vnt_estado.nombre as estado_nombre,vnt_direccion.cp
                FROM vnt_direccion join vnt_clientes on vnt_direccion.cliente_id = vnt_clientes.id
                left join vnt_estado on vnt_estado.id = vnt_direccion.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_direccion.municipio_id
                order by vnt_direccion.id DESC";
        $this->content['direcciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCliente ($cliente_id)
    {
        $sql = "SELECT vnt_direccion.id,vnt_direccion.cliente_id,vnt_clientes.razon_social as cliente,
                vnt_direccion.tipo_vialidad,vnt_direccion.calle,vnt_direccion.num_ext,
                vnt_direccion.num_int,vnt_direccion.colonia, vnt_direccion.poblacion,
                vnt_direccion.municipio_id,vnt_municipio.nombre as municipio_nombre,
                vnt_direccion.estado_id, vnt_estado.nombre as estado_nombre,vnt_direccion.cp
                FROM vnt_direccion join vnt_clientes on vnt_direccion.cliente_id = vnt_clientes.id
                left join vnt_estado on vnt_estado.id = vnt_direccion.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_direccion.municipio_id
                WHERE vnt_direccion.cliente_id = $cliente_id
                order by vnt_direccion.id DESC";
        $this->content['direcciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            $direcciones = new VntDireccion();
            $direcciones->setTransaction($tx);
            $direcciones->cliente_id = intval($request['cliente_id']);
            $direcciones->tipo_vialidad = trim(strtoupper($request['tipo_vialidad']));
            $direcciones->calle = trim(strtoupper($request['calle']));
            $direcciones->num_ext = trim(strtoupper($request['num_ext']));
            $direcciones->num_int = trim(strtoupper($request['num_int']));
            $direcciones->colonia = trim(strtoupper($request['colonia']));
            $direcciones->poblacion = trim(strtoupper($request['poblacion']));
            if(intval($request['municipio_id'])===0){
                $direcciones->municipio_id = null;
            } else {
                $direcciones->municipio_id = intval($request['municipio_id']);
            }
            if(intval($request['estado_id']) === 0){
                $direcciones->estado_id = null;
            } else {
                $direcciones->estado_id = intval($request['estado_id']);
            }
            $direcciones->cp = trim($request['cp']);
            if ($direcciones->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la dirección del cliente.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($direcciones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la dirección del cliente.'];
                $tx->rollback();
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

            $direcciones = VntDireccion::findFirst($request['id']);
            if ($direcciones) {
                $direcciones->setTransaction($tx);
                $direcciones->cliente_id = intval($request['cliente_id']);
                $direcciones->tipo_vialidad = trim(strtoupper($request['tipo_vialidad']));
                $direcciones->calle = trim(strtoupper($request['calle']));
                $direcciones->num_ext = trim(strtoupper($request['num_ext']));
                $direcciones->num_int = trim(strtoupper($request['num_int']));
                $direcciones->colonia = trim(strtoupper($request['colonia']));
                $direcciones->poblacion = trim(strtoupper($request['poblacion']));
                if(intval($request['municipio_id'])===0){
                    $direcciones->municipio_id = null;
                } else {
                    $direcciones->municipio_id = intval($request['municipio_id']);
                }
                if(intval($request['estado_id']) === 0){
                    $direcciones->estado_id = null;
                } else {
                    $direcciones->estado_id = intval($request['estado_id']);
                }
                $direcciones->cp = trim($request['cp']);
                if ($direcciones->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la dirección del cliente.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($direcciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la dirección del cliente.'];
                    $tx->rollback();
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
                    $direcciones = VntDireccion::findFirst($request['id']);
                    $direcciones->setTransaction($tx);

                    if ($direcciones->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($direcciones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la dirección del cliente.'];
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