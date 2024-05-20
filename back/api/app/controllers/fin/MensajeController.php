<?php
use Phalcon\Mvc\Controller;

class MensajeController extends Controller{

  public $content = ['result' => 'error', 'message' => ''];

  public function getAll ()
    {
        $sql = "SELECT * FROM fin_mensajes_solicitudes";

        $this->content['mensajes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
  
  public function save () {

    try{
        $tx = $this->transactions->get();
        $request = $this->request->getPost();

        $mensajes= new Mensajes();
        $mensajes->setTransaction($tx);
        $validUser = Auth::getUserData($this->config);
        $mensajes->autorizador_id = $validUser->user_id;
        $mensajes->solicitud_id=$request['solicitud_id'];
        $mensajes->mensaje=$request['mensaje'];
        $mensajes->user_destinatario=$request['user_destino'];
        $mensajes->fecha_mensaje = date("Y-m-d H:i:s");
        $autor = SysUsers::findFirst($validUser->user_id);
        if ($mensajes->create()) {
            if($request['user_destino'] === '0' || $request['user_destino'] === 0) {
                $usuarios = $this->getUsers($request['solicitud_id']);
            } else {
                $destino = SysUsers::findFirst($request['user_destino']);
                $usuarios = [''.$destino->email];
            }
            $this->sendEmailComment($usuarios, $autor->nickname, $request['solicitud_id']);
        	$this->content['result']='success';
            $this->content['message']= ['title' => '¡Exito!', 'content' => 'Se ha guardado el comentario'];
            $tx->commit();
        } else {
        	$this->content['error'] = Helpers::getErrors($mensajes);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el comentario'];
            $tx->rollback();
        }
    } catch  (Throwable $e) {
        $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
    }
    $this->response->setJsonContent($this->content);
    $this->response->send();
  }

  public function getMensajesBySolicitud ($solicitud_id) 
  {
    $validUser = Auth::getUserData($this->config);
    $id = $validUser->user_id;
    $sql = "SELECT fin_mensajes_solicitudes.id, fin_mensajes_solicitudes.solicitud_id, fin_mensajes_solicitudes.autorizador_id,fin_mensajes_solicitudes.mensaje, fin_mensajes_solicitudes.fecha_mensaje, sys_users.nickname as autor
        FROM fin_mensajes_solicitudes 
        inner join sys_users 
        on fin_mensajes_solicitudes.autorizador_id = sys_users.id
        where solicitud_id = $solicitud_id and (user_destinatario = $id or user_destinatario = 0 or autorizador_id = $id)
        order by fecha_mensaje";

    $this->content['mensajes'] = $this->db->query($sql)->fetchAll();
    $this->content['result'] = 'success';
    $this->response->setJsonContent($this->content);
    $this->response->send();
  }

  private function sendEmailComment($userDestino, $autor, $solicitud)
  {
        $mailer = new Mailer();
        $mailer->from = 'donotreply@ant.com.mx';
        $mailer->tousers = $userDestino;
        $mailer->subject = 'Nuevo comentario';
        $mailer->template = 'comentario.xml';
        $mailer->type = 'comment';
        $mailer->misc = [
            'solicitud' => $solicitud,
            'autor' => $autor,
            'logo' => 'http://api.impact.beta.antfarm.mx/public/assets/images/logo.png'
        ];

        $result_message = null;
        try {
            $result_message = $mailer->sendEmail();
            return true;
        } catch (Exception $e) {
            return false;
        }
  }
  private function getUsers($solicitud) {
        $sql = "SELECT sys_users.email
                from sys_users
                where id = 29 or id = 39
                union
                select sys_users.email
                from sys_users
                inner join fin_solicitudes on fin_solicitudes.creador = sys_users.id and fin_solicitudes.id = $solicitud";
        $users = $this->db->query($sql)->fetchAll();
        $arr = [];
        foreach ($users as $user) {
            array_push($arr, $user['email']);
        }
        unset($users);
        return $arr;
   }
}