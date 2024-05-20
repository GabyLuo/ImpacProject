<?php
use Phalcon\Mvc\Controller;

class NotasController extends Controller {

  public $content = ['result' => 'error', 'message' => ''];

  public function getAll ()
    {
        $sql = "SELECT fin_notas.id, fin_notas.solicitud_id, fin_notas.usuario_id, fin_notas.nota, fin_notas.fecha, sys_users.nickname FROM fin_notas INNER JOIN sys_users on sys_users.id = fin_notas.usuario_id";
        $this->content['notas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
  
  public function save () {

    try{
        $tx = $this->transactions->get();
        $request = $this->request->getPost();

        $notas= new Notas();
        $notas->setTransaction($tx);
        $validUser = Auth::getUserData($this->config);
        $notas->usuario_id = $validUser->user_id;
        $notas->solicitud_id=$request['solicitud_id'];
        $notas->nota=trim($request['nota']);
        $notas->fecha_mensaje = date("Y-m-d H:i:s");
        if ($notas->create()) {
        	$this->content['result']='success';
            $this->content['message']= ['title' => '¡Exito!', 'content' => 'Se ha guardado la nota'];
            $tx->commit();
        } else {
        	$this->content['error'] = Helpers::getErrors($notas);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar la nota'];
            $tx->rollback();
        }
    } catch  (Throwable $e) {
        $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
    }
    $this->response->setJsonContent($this->content);
    $this->response->send();
  }

  public function getNotasBySolicitud ($solicitud_id) 
  {
    $sql = "SELECT fin_notas.id, fin_notas.solicitud_id, fin_notas.usuario_id, fin_notas.nota, fin_notas.fecha, fin_notas.perfil, sys_users.nickname FROM fin_notas INNER JOIN sys_users on sys_users.id = fin_notas.usuario_id and fin_notas.solicitud_id = $solicitud_id";

    $this->content['notas'] = $this->db->query($sql)->fetchAll();
    $this->content['result'] = 'success';
    $this->response->setJsonContent($this->content);
    $this->response->send();
  }
}