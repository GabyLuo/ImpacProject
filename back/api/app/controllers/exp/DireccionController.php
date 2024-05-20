<?php

use Phalcon\Mvc\Controller;

class DireccionController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT exp_direccion.id,exp_direccion.empresa_id,vnt_empresa.razon_social as empresa,
                exp_direccion.tipo,exp_direccion.calle,exp_direccion.num_ext,
                exp_direccion.num_int,exp_direccion.colonia, exp_direccion.poblacion,
                exp_direccion.municipio_id,vnt_municipio.nombre as municipio_nombre,
                exp_direccion.estado_id, vnt_estado.nombre as estado_nombre,exp_direccion.cp, exp_direccion.tipo_oficina,
                exp_direccion.tipo_propiedad
                FROM exp_direccion join vnt_empresa on exp_direccion.empresa_id = vnt_empresa.id
                left join vnt_estado on vnt_estado.id = exp_direccion.estado_id
                left join vnt_municipio on vnt_municipio.id = exp_direccion.municipio_id
                order by exp_direccion.id DESC";
        $this->content['direcciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresa ($empresa_id)
    {
        $sql = "SELECT exp_direccion.id,exp_direccion.empresa_id,vnt_empresa.razon_social as empresa,
                exp_direccion.tipo,exp_direccion.calle,exp_direccion.num_ext,
                exp_direccion.num_int,exp_direccion.colonia, exp_direccion.poblacion,
                exp_direccion.municipio_id,vnt_municipio.nombre as municipio_nombre,
                exp_direccion.estado_id, vnt_estado.nombre as estado_nombre,exp_direccion.cp, exp_direccion.tipo_oficina,
                exp_direccion.tipo_propiedad
                FROM exp_direccion join vnt_empresa on exp_direccion.empresa_id = vnt_empresa.id
                left join vnt_estado on vnt_estado.id = exp_direccion.estado_id
                left join vnt_municipio on vnt_municipio.id = exp_direccion.municipio_id
                WHERE exp_direccion.empresa_id = $empresa_id
                order by exp_direccion.id DESC";
        $this->content['direcciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresaOptions ($empresa_id)
    {
        $sql = "SELECT exp_direccion.id, exp_direccion.calle || ', ' || exp_direccion.num_ext || ',. ' || exp_direccion.colonia || ', ' || vnt_municipio.nombre || ' ' || vnt_estado.nombre || ' C.P. ' || exp_direccion.cp as label
            FROM exp_direccion join vnt_empresa on exp_direccion.empresa_id = vnt_empresa.id
            left join vnt_estado on vnt_estado.id = exp_direccion.estado_id
            left join vnt_municipio on vnt_municipio.id = exp_direccion.municipio_id
            WHERE exp_direccion.empresa_id = $empresa_id
            order by exp_direccion.id DESC";
        $data = $this->db->query($sql)->fetchAll();
        $dir = [];
        foreach ($data as $direccion) {
            $array['label'] = $direccion['label'];
            $array['value'] = $direccion['id'];
            array_push($dir, $array);
        }
        $this->content['direcciones'] = $dir;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            $direcciones = new Direccion();
            $direcciones->setTransaction($tx);
            $direcciones->empresa_id = intval($request['empresa_id']);
            $direcciones->tipo = strtoupper($request['tipo']);
            $direcciones->tipo_oficina = strtoupper($request['tipo_oficina']);
            $direcciones->tipo_propiedad = strtoupper($request['tipo_propiedad']);
            $direcciones->calle = strtoupper($request['calle']);
            $direcciones->num_ext = strtoupper($request['num_ext']);
            $direcciones->num_int = strtoupper($request['num_int']);
            $direcciones->colonia = strtoupper($request['colonia']);
            $direcciones->poblacion = strtoupper($request['poblacion']);
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
            $direcciones->cp = $request['cp'];
            if ($direcciones->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la dirección.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($direcciones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la dirección.'];
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

            $direcciones = Direccion::findFirst($request['id']);
            if ($direcciones) {
                $direcciones->setTransaction($tx);
                $direcciones->empresa_id = intval($request['empresa_id']);
                $direcciones->tipo = strtoupper($request['tipo']);
                $direcciones->tipo_oficina = strtoupper($request['tipo_oficina']);
                $direcciones->tipo_propiedad = strtoupper($request['tipo_propiedad']);
                $direcciones->calle = strtoupper($request['calle']);
                $direcciones->num_ext = strtoupper($request['num_ext']);
                $direcciones->num_int = strtoupper($request['num_int']);
                $direcciones->colonia = strtoupper($request['colonia']);
                $direcciones->poblacion = strtoupper($request['poblacion']);
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
                $direcciones->cp = $request['cp'];
                if ($direcciones->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la dirección'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($direcciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la dirección'];
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
                    $direcciones = Direccion::findFirst($request['id']);
                    $direcciones->setTransaction($tx);

                    if ($direcciones->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($direcciones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la dirección.'];
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