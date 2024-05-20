<?php

use Phalcon\Mvc\Controller;

class RequisitosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *
                from exp_requisitos where padron_id is null 
                order by id DESC";
        
        $resultados = $this->db->query($sql)->fetchAll();

        $requisitos=[];
        foreach ($resultados as $elemento){
            $e=(object)array();
            $e->id = $elemento['id'];
            $e->nombre = $elemento['nombre'];
            $tipos_archivos = explode(",",$elemento['tipo']);
            $e->tipo = $tipos_archivos;

            /* $t = '';
            for($i = 0; $i<count($tipos_archivos); $i++) {
                if($i === (count($tipos_archivos)-1)){
                    $t.= $tipos_archivos[$i];
                } else {
                    $t.= $tipos_archivos[$i].', ';
                }
            } */

            $e->tipo_copia = $elemento['tipo'];
            $e->tramite = $elemento['tramite'];
            array_push($requisitos,$e);
        }

        $this->content['requisitos'] =$requisitos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByTramite ($cliente_id,$tramite) {
        $sql = "SELECT exp_requisitos.id,nombre,tipo,tramite,s1.cliente_id is not null as status,s1.id as vnt_clientes_requisitos_id
                from exp_requisitos
                left join 
                (SELECT id,cliente_id,requisito_id
                    from vnt_clientes_requisitos
                    where cliente_id = $cliente_id) as s1 on s1.requisito_id = exp_requisitos.id
                where tramite= '$tramite'
                order by exp_requisitos.nombre";

        $this->content['requisitos'] =$this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $requisito = Requisitos::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => trim(strtoupper($request['nombre']))
                    ]
                ]
            );

            if($requisito){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un requisito con este nombre.'];

            } else {
            
                $requisito = new Requisitos();
                $requisito->setTransaction($tx);
                $requisito->nombre = trim(strtoupper($request['nombre']));

                $tipos = '';
                $longitud = count($request['tipo']);
                for($i = 0; $i<$longitud; $i++) {
                    if($i === ($longitud-1)){
                        $tipos.= $request['tipo'][$i];
                    } else {
                        $tipos.= $request['tipo'][$i].',';
                    }
                }

                $requisito->tipo = $tipos;
                $requisito->tramite = strtoupper($request['tramite']);
                if ($requisito->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el trámite.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($requisito);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el trámite.'];
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

            

            $requisito = Requisitos::findFirst(
                [
                    'id != :id: AND nombre = :nombre:',
                    'bind' => [
                        'nombre' => trim(strtoupper($request['nombre'])),
                        'id' => intval($request['id'])
                    ]
                ]
            );

            if($requisito){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un requisito con este nombre.'];
            } else {
                $requisito = Requisitos::findFirst($request['id']);
                $requisito->setTransaction($tx);
                $requisito->nombre = trim(strtoupper($request['nombre']));

                $tipos = '';
                $longitud = count($request['tipo']);
                for($i = 0; $i<$longitud; $i++) {
                    if($i === ($longitud-1)){
                        $tipos.= $request['tipo'][$i];
                    } else {
                        $tipos.= $request['tipo'][$i].',';
                    }
                }

                $requisito->tipo = $tipos;

                $requisito->tramite = strtoupper($request['tramite']);
                if ($requisito->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el requisito.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($requisito);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el requisito.'];
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
                    $requisito = Requisitos::findFirst($request['id']);
                    $requisito->setTransaction($tx);

                    if ($requisito->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($requisito);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el requisito.'];
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