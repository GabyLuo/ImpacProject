<?php

use Phalcon\Mvc\Controller;

class PmoSucursalesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT pmo_sucursales.id,
                pmo_sucursales.tipo,pmo_sucursales.calle,pmo_sucursales.num_ext,
                pmo_sucursales.num_int,pmo_sucursales.colonia, pmo_sucursales.poblacion,
                pmo_sucursales.municipio_id,vnt_municipio.nombre as municipio_nombre,
                pmo_sucursales.estado_id, vnt_estado.nombre as estado_nombre,pmo_sucursales.cp, pmo_sucursales.tipo_oficina,
                pmo_sucursales.tipo_propiedad, pmo_sucursales.nombre, pmo_sucursales.encargado, pmo_sucursales.telefono, pmo_sucursales.fax
                FROM pmo_sucursales
                left join vnt_estado on vnt_estado.id = pmo_sucursales.estado_id
                left join vnt_municipio on vnt_municipio.id = pmo_sucursales.municipio_id
                order by pmo_sucursales.id DESC";
        $this->content['sucursales'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresa ($empresa_id)
    {
        $sql = "SELECT pmo_sucursales.id,
                pmo_sucursales.tipo,pmo_sucursales.calle,pmo_sucursales.num_ext,
                pmo_sucursales.num_int,pmo_sucursales.colonia, pmo_sucursales.poblacion,
                pmo_sucursales.municipio_id,vnt_municipio.nombre as municipio_nombre,
                pmo_sucursales.estado_id, vnt_estado.nombre as estado_nombre,pmo_sucursales.cp, pmo_sucursales.tipo_oficina,
                pmo_sucursales.tipo_propiedad, pmo_sucursales.nombre, pmo_sucursales.encargado, pmo_sucursales.telefono, pmo_sucursales.fax
                FROM pmo_sucursales
                left join vnt_estado on vnt_estado.id = pmo_sucursales.estado_id
                left join vnt_municipio on vnt_municipio.id = pmo_sucursales.municipio_id
                WHERE pmo_sucursales.empresa_id = $empresa_id
                order by pmo_sucursales.id DESC";
        $this->content['sucursales'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            $sucursales = new PmoSucursales();
            $sucursales->setTransaction($tx);
            $sucursales->tipo = strtoupper($request['tipo']);
            $sucursales->tipo_oficina = strtoupper($request['tipo_oficina']);
            $sucursales->tipo_propiedad = strtoupper($request['tipo_propiedad']);
            $sucursales->calle = strtoupper($request['calle']);
            $sucursales->num_ext = strtoupper($request['num_ext']);
            $sucursales->num_int = strtoupper($request['num_int']);
            $sucursales->colonia = strtoupper($request['colonia']);
            $sucursales->poblacion = strtoupper($request['poblacion']);
            $sucursales->nombre = trim(strtoupper($request['nombre'])) == '' ? null: trim(strtoupper($request['nombre']));
            $sucursales->encargado = trim(strtoupper($request['encargado'])) == '' ? null: trim(strtoupper($request['encargado']));
            $sucursales->telefono = trim(strtoupper($request['telefono'])) == '' ? null: trim(strtoupper($request['telefono']));
            $sucursales->fax = trim(strtoupper($request['fax'])) == '' ? null: trim(strtoupper($request['fax']));
            if(intval($request['municipio_id'])===0){
                $sucursales->municipio_id = null;
            } else {
                $sucursales->municipio_id = intval($request['municipio_id']);
            }
            if(intval($request['estado_id']) === 0){
                $sucursales->estado_id = null;
            } else {
                $sucursales->estado_id = intval($request['estado_id']);
            }
            $sucursales->cp = $request['cp'];
            if ($sucursales->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la sucursal.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($sucursales);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la sucursal.'];
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

            $sucursales = PmoSucursales::findFirst($request['id']);
            if ($sucursales) {
                $sucursales->setTransaction($tx);
                $sucursales->tipo = strtoupper($request['tipo']);
                $sucursales->tipo_oficina = strtoupper($request['tipo_oficina']);
                $sucursales->tipo_propiedad = strtoupper($request['tipo_propiedad']);
                $sucursales->calle = strtoupper($request['calle']);
                $sucursales->num_ext = strtoupper($request['num_ext']);
                $sucursales->num_int = strtoupper($request['num_int']);
                $sucursales->colonia = strtoupper($request['colonia']);
                $sucursales->poblacion = strtoupper($request['poblacion']);
                $sucursales->nombre = trim(strtoupper($request['nombre'])) == '' ? null: trim(strtoupper($request['nombre']));
                $sucursales->encargado = trim(strtoupper($request['encargado'])) == '' ? null: trim(strtoupper($request['encargado']));
                $sucursales->telefono = trim(strtoupper($request['telefono'])) == '' ? null: trim(strtoupper($request['telefono']));
                $sucursales->fax = trim(strtoupper($request['fax'])) == '' ? null: trim(strtoupper($request['fax']));
                if(intval($request['municipio_id'])===0){
                    $sucursales->municipio_id = null;
                } else {
                    $sucursales->municipio_id = intval($request['municipio_id']);
                }
                if(intval($request['estado_id']) === 0){
                    $sucursales->estado_id = null;
                } else {
                    $sucursales->estado_id = intval($request['estado_id']);
                }
                $sucursales->cp = $request['cp'];
                if ($sucursales->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la sucursal.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($sucursales);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la sucursal.'];
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
                    $sucursales = PmoSucursales::findFirst($request['id']);
                    $sucursales->setTransaction($tx);

                    if ($sucursales->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($sucursales);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la sucursal.'];
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