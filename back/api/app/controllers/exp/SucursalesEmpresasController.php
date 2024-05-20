<?php

use Phalcon\Mvc\Controller;

class SucursalesEmpresasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_sucursales.id,vnt_sucursales.empresa_id,vnt_empresa.razon_social as empresa,
                vnt_sucursales.tipo,vnt_sucursales.calle,vnt_sucursales.num_ext,
                vnt_sucursales.num_int,vnt_sucursales.colonia, vnt_sucursales.poblacion,
                vnt_sucursales.municipio_id,vnt_municipio.nombre as municipio_nombre,
                vnt_sucursales.estado_id, vnt_estado.nombre as estado_nombre,vnt_sucursales.cp, vnt_sucursales.tipo_oficina,
                vnt_sucursales.tipo_propiedad, vnt_sucursales.nombre, vnt_sucursales.encargado, vnt_sucursales.telefono, vnt_sucursales.fax
                FROM vnt_sucursales join vnt_empresa on vnt_sucursales.empresa_id = vnt_empresa.id
                left join vnt_estado on vnt_estado.id = vnt_sucursales.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_sucursales.municipio_id
                order by vnt_sucursales.id DESC";
        $this->content['sucursales'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresa ($empresa_id)
    {
        $sql = "SELECT vnt_sucursales.id,vnt_sucursales.empresa_id,vnt_empresa.razon_social as empresa,
                vnt_sucursales.tipo,vnt_sucursales.calle,vnt_sucursales.num_ext,
                vnt_sucursales.num_int,vnt_sucursales.colonia, vnt_sucursales.poblacion,
                vnt_sucursales.municipio_id,vnt_municipio.nombre as municipio_nombre,
                vnt_sucursales.estado_id, vnt_estado.nombre as estado_nombre,vnt_sucursales.cp, vnt_sucursales.tipo_oficina,
                vnt_sucursales.tipo_propiedad, vnt_sucursales.nombre, vnt_sucursales.encargado, vnt_sucursales.telefono, vnt_sucursales.fax
                FROM vnt_sucursales join vnt_empresa on vnt_sucursales.empresa_id = vnt_empresa.id
                left join vnt_estado on vnt_estado.id = vnt_sucursales.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_sucursales.municipio_id
                WHERE vnt_sucursales.empresa_id = $empresa_id
                order by vnt_sucursales.id DESC";
        $this->content['sucursales'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresaOptions ($empresa_id)
    {
        $sql = "SELECT vnt_sucursales.id,vnt_sucursales.calle || ', ' || vnt_sucursales.num_ext || ',. ' || 
                vnt_sucursales.colonia || ', ' || vnt_municipio.nombre || ' ' || vnt_estado.nombre || ' C.P. ' ||vnt_sucursales.cp as label
                FROM vnt_sucursales join vnt_empresa on vnt_sucursales.empresa_id = vnt_empresa.id
                left join vnt_estado on vnt_estado.id = vnt_sucursales.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_sucursales.municipio_id
                WHERE vnt_sucursales.empresa_id = $empresa_id
                order by vnt_sucursales.id DESC";
        $data = $this->db->query($sql)->fetchAll();
        $suc = [];
        foreach ($data as $sucursal) {
            $array['label'] = $sucursal['label'];
            $array['value'] = $sucursal['id'];
            array_push($suc, $array);
        }
        $this->content['sucursales'] = $suc;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            $sucursales = new EmpresasSucursales();
            $sucursales->setTransaction($tx);
            $sucursales->empresa_id = intval($request['empresa_id']);
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
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la dirección del empresa.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($sucursales);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la dirección del empresa.'];
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

            $sucursales = EmpresasSucursales::findFirst($request['id']);
            if ($sucursales) {
                $sucursales->setTransaction($tx);
                $sucursales->setTransaction($tx);
                $sucursales->empresa_id = intval($request['empresa_id']);
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
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la dirección del empresa.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($sucursales);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la dirección del empresa.'];
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
                    $sucursales = EmpresasSucursales::findFirst($request['id']);
                    $sucursales->setTransaction($tx);

                    if ($sucursales->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($sucursales);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la dirección del empresa.'];
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