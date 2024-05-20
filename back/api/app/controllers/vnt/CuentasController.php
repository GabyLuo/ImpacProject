<?php

use Phalcon\Mvc\Controller;

class CuentasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_cuentas_empresas.id,empresa_id,vnt_empresa.razon_social as empresa,
                vnt_cuentas_empresas.nombre
                from vnt_empresa 
                join vnt_cuentas_empresas on vnt_empresa.id = vnt_cuentas_empresas.empresa_id
                order by empresa";
        
        $this->content['cuentas'] = $this->db->query($sql)->fetchAll();
        $this->content['fecha'] = date('Y-m-d');
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $cuenta = Cuenta::findFirst(
                [
                    'nombre = :nombre: AND empresa_id = :empresa_id:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'empresa_id' => intval($request['empresa_id'])
                    ]
                ]
            );
            if ($cuenta) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una cuenta con estos datos'];
            } else {
                $cuenta = new Cuenta();
                $cuenta->setTransaction($tx);
                $cuenta->nombre = strtoupper($request['nombre']);
                $cuenta->empresa_id = intval($request['empresa_id']);
                if ($cuenta->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la cuenta.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cuenta);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la cuenta.'];
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

            $cuenta = Cuenta::findFirst(
                [
                    'id != :id: AND (nombre = :nombre: AND empresa_id = :empresa_id:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'empresa_id' => intval($request['empresa_id'])
                    ]
                ]
            );
            if ($cuenta) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una cuenta con estos datos'];
            } else {
                $cuenta = Cuenta::findFirst($request['id']);
                if ($cuenta) {
                    $cuenta->setTransaction($tx);
                    $cuenta->nombre = strtoupper($request['nombre']);
                    $cuenta->empresa_id = intval($request['empresa_id']);
                    if ($cuenta->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la cuenta.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($cuenta);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la cuenta'];
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
                    $cuenta = Cuenta::findFirst($request['id']);
                    $cuenta->setTransaction($tx);

                    if ($cuenta->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($cuenta);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la cuenta.'];
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