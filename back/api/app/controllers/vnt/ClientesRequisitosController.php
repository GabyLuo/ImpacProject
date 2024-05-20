<?php

use Phalcon\Mvc\Controller;

class ClientesRequisitosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            if(intval($request['vnt_clientes_requisitos_id'])>0 && $request['status']==='false'){
                $cliente_requisito_id=ClientesRequisitos::findFirst($request['vnt_clientes_requisitos_id']);
                $cliente_requisito_id->setTransaction($tx);

                if ($cliente_requisito_id->delete()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha quitado este requisito del cliente.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cliente_requisito_id);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }

            } else if (intval($request['vnt_clientes_requisitos_id'])===0 && $request['status']==='true'){
                $cliente_requisito = new ClientesRequisitos();
                $cliente_requisito->setTransaction($tx);
                $cliente_requisito->cliente_id=$request['cliente_id'];
                $cliente_requisito->requisito_id=$request['id'];

                if($cliente_requisito->create()){
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado este requisito al cliente.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cliente_requisito);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }

            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' =>'Ha ocurrido un error, vuelva a intentarlo por favor.'];
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}