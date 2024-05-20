<?php

use Phalcon\Mvc\Controller;

class ConceptoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM vnt_concepto
            ORDER BY nombre";
        $this->content['conceptos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $conceptos = Concepto::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($conceptos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este concepto'];
            } else {
                $conceptos = new Concepto();
                $conceptos->setTransaction($tx);
                $conceptos->nombre = strtoupper($request['nombre']);
                if ($conceptos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el concepto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($conceptos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el concepto.'];
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

            $conceptos = Concepto::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($conceptos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un concepto con este nombre'];
            } else {
                $conceptos = Concepto::findFirst($request['id']);
                if ($conceptos) {
                    $conceptos->setTransaction($tx);
                    $conceptos->nombre = strtoupper($request['nombre']);

                    if ($conceptos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el concepto'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($conceptos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el concepto'];
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
                    $conceptos = Concepto::findFirst($request['id']);
                    $conceptos->setTransaction($tx);

                    if ($conceptos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($conceptos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el concepto.'];
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