<?php

use Phalcon\Mvc\Controller;

class RespaldoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT lic_respaldo.id, lic_respaldo.licitacion_id,
                        (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
                        lic_respaldo.empresa_id, vnt_empresa.razon_social as empresa
                from lic_respaldo join lic_licitacion on lic_respaldo.licitacion_id = lic_licitacion.id
                    join vnt_empresa on vnt_empresa.id = lic_respaldo.empresa_id
                order by lic_respaldo.id DESC";
        $this->content['licitaciones_respaldo'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByLicitacion ($licitacion_id)
    {
        $sql = "SELECT lic_respaldo.id, lic_respaldo.licitacion_id,
                        (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
                        lic_respaldo.empresa_id, lic_respaldo.empresa
                from lic_respaldo join lic_licitacion on lic_respaldo.licitacion_id = lic_licitacion.id
                where lic_respaldo.licitacion_id = $licitacion_id
                order by lic_respaldo.id ASC";
        $this->content['licitaciones_respaldo'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $licitaciones_respaldo = new Respaldo();
            $licitaciones_respaldo->setTransaction($tx);
            $licitaciones_respaldo->licitacion_id = intval($request['licitacion_id']);
            $licitaciones_respaldo->empresa_id = intval($request['empresa_id']);
            
            if ($licitaciones_respaldo->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado la empresa de respaldo.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($licitaciones_respaldo);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar la empresa de respaldo'];
                $tx->rollback();
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
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
                    $licitaciones_respaldo = Respaldo::findFirst($request['id']);
                    $licitaciones_respaldo->setTransaction($tx);

                    if ($licitaciones_respaldo->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($licitaciones_respaldo);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la empresa de respaldo.'];
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