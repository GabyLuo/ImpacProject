<?php

use Phalcon\Mvc\Controller;

class SubconceptoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ($id)
    {
        $and = "";
        if (intval($id) > 0) {
            $and = " and vnt_concepto.id = $id";
        }
        $sql = "SELECT vnt_subconcepto.id, vnt_subconcepto.concepto_id, vnt_subconcepto.nombre, vnt_concepto.nombre as concepto
            FROM vnt_subconcepto
            INNER JOIN vnt_concepto on vnt_concepto.id = vnt_subconcepto.concepto_id {$and}
            ORDER BY vnt_subconcepto.nombre";
        $this->content['subconceptos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByConcepto($concepto_id) {
        $s = intval($concepto_id);
        $sql = "SELECT vnt_subconcepto.id, vnt_subconcepto.nombre
            FROM vnt_subconcepto
            WHERE vnt_subconcepto.concepto_id = $s
            ORDER BY vnt_subconcepto.nombre";
        $subconceptos = $this->db->query($sql)->fetchAll();
        $subc = [];
        foreach ($subconceptos as $subconcepto) {
            $array['label'] = '' . $subconcepto['nombre'];
            $array['value'] = $subconcepto['id'];
            array_push($subc, $array);
        }
         
        $this->content['subconceptos'] = $subc;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $conceptos = Subconcepto::findFirst(
                [
                    'nombre = :nombre: AND (concepto_id = :concepto_id:)',
                    'bind' => [
                        'concepto_id' => intval($request['concepto_id']),
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($conceptos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este subconcepto asociado al concepto'];
            } else {
                $conceptos = new Subconcepto();
                $conceptos->setTransaction($tx);
                $conceptos->nombre = strtoupper($request['nombre']);
                $conceptos->concepto_id = intval($request['concepto_id']);
                if ($conceptos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el subconcepto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($conceptos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el subconcepto.'];
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

            $conceptos = Subconcepto::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (concepto_id = :concepto_id:)',
                    'bind' => [
                        'concepto_id' => intval($request['concepto_id']),
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($conceptos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un subconcepto asociado a ese concepto'];
            } else {
                $conceptos = Subconcepto::findFirst($request['id']);
                if ($conceptos) {
                    $conceptos->setTransaction($tx);
                    $conceptos->nombre = strtoupper($request['nombre']);
                    $conceptos->concepto_id = intval($request['concepto_id']);

                    if ($conceptos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el subconcepto'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($conceptos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el subconcepto'];
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
                    $conceptos = Subconcepto::findFirst($request['id']);
                    $conceptos->setTransaction($tx);

                    if ($conceptos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($conceptos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el subconcepto.'];
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