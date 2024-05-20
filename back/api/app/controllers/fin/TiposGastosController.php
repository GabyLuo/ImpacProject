<?php

use Phalcon\Mvc\Controller;

class TiposGastosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM fin_tipos_gastos
            ORDER BY nombre";
        $this->content['tiposgastos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $tipos = TiposGastos::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($tipos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este tipo de gasto'];
            } else {
                $tipos = new TiposGastos();
                $tipos->setTransaction($tx);
                $tipos->nombre = strtoupper($request['nombre']);
                if ($tipos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el tipo de gasto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($tipos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el tipo de gasto.'];
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

            $tipos = TiposGastos::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($tipos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un tipo de gasto con este nombre'];
            } else {
                $tipos = TiposGastos::findFirst($request['id']);
                if ($tipos) {
                    $tipos->setTransaction($tx);
                    $tipos->nombre = strtoupper($request['nombre']);

                    if ($tipos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el tipo de gasto'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el tipo de gasto'];
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
                    $tipos = TiposGastos::findFirst($request['id']);
                    $tipos->setTransaction($tx);

                    if ($tipos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el tipo de gasto.'];
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