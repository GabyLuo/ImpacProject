<?php

use Phalcon\Mvc\Controller;

class SaldosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, cuenta_id, fecha, monto, created, created_by, updated, updated_by
                FROM vnt_cuentas_saldos";
        
        $this->content['saldos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCuenta ($cuenta_id)
    {
        $sql = "SELECT id, cuenta_id, fecha, to_char(fecha,'DD-MM-YYYY') as fecha_formato, monto, created, created_by, updated, updated_by
                FROM vnt_cuentas_saldos where cuenta_id = $cuenta_id";
        
        $this->content['saldos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
                $cuenta = new Saldos();
                $cuenta->setTransaction($tx);
                $cuenta->monto = $request['monto'];
                $cuenta->fecha = date("Y/m/d H:i:s", strtotime($request['fecha']));
                $cuenta->cuenta_id = intval($request['cuenta_id']);
                if ($cuenta->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la cuenta.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cuenta);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la cuenta.'];
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
                $cuenta = Saldos::findFirst($request['id']);
                if ($cuenta) {
                    $cuenta->setTransaction($tx);
                    $cuenta->monto = $request['monto'];
                    $cuenta->fecha = date("Y/m/d H:i:s", strtotime($request['fecha']));
                    $cuenta->cuenta_id = intval($request['cuenta_id']);
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
                    $cuenta = Saldos::findFirst($request['id']);
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