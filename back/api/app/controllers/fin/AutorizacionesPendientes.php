<?php
use Phalcon\Mvc\Controller;

class AutorizacionesPendientes extends Controller
{
    public $content = ['result' => 'error', 'message' => ''];

  public function getAll () {
    $content = $this->content;
    $validUser = Auth::getUserData($this->config);
    $id = $validUser->user_id;

    $sql = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, 
    fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado, fin_solicitudes.proyecto_id, 
    fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, 
    fin_solicitudes.total as totalcopia, fin_solicitudes.iva, pmo_autorizadores.orden, pmo_autorizadores.orden as autorizadores,pmo_autorizadores.id as autorizador_id,
    (select pmo_proyecto.nombre from pmo_proyecto where pmo_proyecto.id = fin_solicitudes.proyecto_id) as nombre_proyecto,
    (select sys_users.nickname from sys_users where sys_users.id = fin_solicitudes.creador) as responsable_solicitud
    FROM fin_solicitudes 
    inner join pmo_autorizadores on fin_solicitudes.proyecto_id = pmo_autorizadores.proyecto_id
    and pmo_autorizadores.usuario_id = $id";

    // si la autorizacion_id == orden puede autorizar

    $content['solicitudeslibres'] = $this->db->query($sql)->fetchAll();

    $solicitudeslibres = $this->db->query($sql)->fetchAll();
    $nuevo = [];
        foreach ($solicitudeslibres as $elemento){
            $s=(object)array();
            $niveles = [];
            $proyecto_id = $elemento['proyecto_id'];
            $orden = $elemento['orden'];
            $sql_niveles = "SELECT orden from pmo_autorizadores 
                        where pmo_autorizadores.proyecto_id=$proyecto_id
                        and orden < $orden
                        order by id desc";
            $resultado = $this->db->query($sql_niveles)->fetchAll();
                        foreach ($resultado as $nivel) {
                            $array['label'] = '' . $nivel['orden'];
                            $array['value'] = $nivel['orden'];
                            array_push($niveles, $array);
                        }
            $s->autorizadores = $niveles;
            $s->id = $elemento['id'];
            $s->proyecto_id = $elemento['proyecto_id'];
            $s->status = $elemento['status'];
            $s->fecha_solicitado = $elemento['fecha_solicitado'];
            $s->autorizacion_id = $elemento['autorizacion_id'];
            $s->fecha_creado = $elemento['fecha_creado'];
            $s->creador = $elemento['creador'];
            $s->nombre_proyecto = $elemento['nombre_proyecto'];
            $s->responsable_solicitud = $elemento['responsable_solicitud']; 
            $s->total = $elemento['total'];
            $s->totalcopia = $elemento['totalcopia'];
            $s->orden = $elemento['orden'];
            $s->autorizador_id = $elemento['autorizador_id'];
            if(intval($elemento['iva']) === 0){
                $s->iva = 'NO';
            } else {
                $s->iva ='SI';
            }
            if(intval($elemento['comprobado']) === 0){
                $s->comprobado = 'NO';
            } else {
                $s->comprobado ='SI';
            }           
            array_push($nuevo,$s);
        }

    $content['solicitudeslibres'] = $nuevo;
    $content['result']='success';

    $this->response->setJsonContent($content);
    $this->response->send();
}

public function save(){

    try {
        $request = $this->request->getPost();
        $tx = $this->transactions->get();

        $proyecto_id = $request['proyecto_id'];
        $sql = "SELECT pmo_autorizadores.orden, sys_users.email
                from pmo_autorizadores
                inner join sys_users on pmo_autorizadores.usuario_id = sys_users.id
                and proyecto_id = $proyecto_id
                order by orden";
        $niveles = $this->db->query($sql)->fetchAll();
        $orden = intval($request['orden']);
        $tamanio = sizeof($niveles);
        $siguiente_nivel = $this->el_siguiente($orden, $tamanio, $niveles, $request['id']);
        $autorizar = new SolicitudesAutorizador();
        $autorizar->setTransaction($tx);
        $autorizar->autorizador_id=$request['autorizador_id'];
        $autorizar->solicitud_id=$request['id'];
        if($autorizar->create()){
            //
            $solicitud = SolicitudesO::findFirst($request['id']);
                if($solicitud){
                    $solicitud->setTransaction($tx);
                    $solicitud->autorizacion_id = intval($siguiente_nivel);
                    if (intval($siguiente_nivel) === 0 || intval($siguiente_nivel) === '0') {
                        $solicitud->status = 'POR PAGAR';
                    }
                    if($solicitud->update()){
                        // aqui empieza el bloque del log para la alteracion
                        // si es no, pasa directo a autorizar, si es si, se crea el log
                        if($request['alteracion']==='NO') { //
                            $this->content['result']='success';
                            $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud']; 
                            $tx->commit();
                        } else { //
                            if($request['sobrepasa_presupuesto'] === 'SI') {
                                $logs = new Logs();
                                $logs->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $logs->account_id = $validUser->user_id;
                                $logs->level_id = 4;
                                $logs->log = 'AUTORIZÓ LA SOLICITUD #' . $request['id'] . ' CON MODIFICACIÓN DE PRESUPUESTO';
                                $logs->fecha = date("Y-m-d H:i:s");
                                if($logs->create()){
                                    $notas= new Notas();
                                    $notas->setTransaction($tx);
                                    $notas->usuario_id = $validUser->user_id;
                                    $notas->solicitud_id=$solicitud->id;
                                    $notas->nota=trim($request['nota']);
                                    $notas->fecha = date("Y-m-d H:i:s");
                                    $notas->perfil = 'AUTORIZADOR';
                                    if($notas->create()) {
                                      $this->content['result'] = 'success';
                                      $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha autorizado la solicitud'];
                                      $tx->commit();
                                    } else {
                                      $this->content['error'] = Helpers::getErrors($notas);
                                      $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo autorizar la solicitud.'];
                                      $tx->rollback();
                                    }
                                } else {
                                  $this->content['result']='success';
                                  $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud pero sin guardar el registro en logs.']; 
                                  $tx->commit();
                                }
                            } else {
                                $logs = new Logs();
                                $logs->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $logs->account_id = $validUser->user_id;
                                $logs->level_id = 4;
                                $logs->log = 'AUTORIZÓ LA SOLICITUD #' . $request['id'] . ' CON MODIFICACIÓN DE PRESUPUESTO';
                                $logs->fecha = date("Y-m-d H:i:s");
                                if($logs->create()){
                                  $this->content['result']='success';
                                  $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud.']; 
                                  $tx->commit();
                                } else {
                                  $this->content['result']='success';
                                  $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud pero sin guardar el registro en logs.']; 
                                  $tx->commit();
                                }
                            }
                        } //
                        
                    } else {
                        $this->content['result'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo autorizar la solicitud.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitud);;
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la solicitud.'];
                    $tx->rollback();
                }
            //
        }else{
            $this->content['error'] = Helpers::getErrors($autorizar);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo autorizar la solicitud'];
            $tx->rollback();
        }
    } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
    }

    $this->response->setJsonContent($this->content);
    $this->response->send();

}

public function delete () {
    $request = $this->request->getPost();
    $tx = $this->transactions->get();
    $mensajes= new Mensajes();
    $mensajes->setTransaction($tx);
    $mensajes->autorizador_id=$request['autorizador_id'];
    $mensajes->solicitud_id=$request['solicitud_id'];
    $mensajes->mensaje=$request['mensaje'];
    $mensajes->user_destinatario=$request['user_destino'];
    if ($mensajes->create()) {

        $sql="SELECT sa.*,a.orden from pmo_autorizadores a join fin_solicitudes_autorizador sa on a.id=sa.autorizador_id 
        where solicitud_id=". $request['solicitud_id']." and orden >= ".$request['orden']." order by orden";
        $datos=$this->db->query($sql)->fetchAll();
        
        if (sizeof($datos)>0) {
            foreach($datos as $dato){
                $busqueda=SolicitudesAutorizador::find([
                    'id = :id:',
                    'bind' => [
                        'id' => $dato['id']     
                        ]
                       ]
                    );
                    if($busqueda){
                        $busqueda->delete();                            
                    }  
            }
            $this->content['result']='success';
            $this->content['message']='Se ha rechazado la solicitud hasta el nivel '.$request['orden'];
            $tx->commit();
        }else{
            $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo agregar los roles al usuario.'];
            $tx->rollback();
        }

   }

    $this->response->setJsonContent($this->content);
    $this->response->send();
}

private function el_siguiente ($numero, $longitud, $arreglo, $solicitud) 
{
    for ($i=0; $i<$longitud; $i++) {
        if ($numero == $arreglo[$i]['orden']){
            if ($longitud-1 == $i) {
                return 0;
            }
            if ($i < $longitud-1){
                $this->sendEmailAuthorization($arreglo[$i+1]['email'], $solicitud);
                return $arreglo[$i+1]['orden'];
                //return $arreglo[$i+1];
            }
        }
    }
}

private function sendEmailAuthorization($userDestino, $solicitud)
{
    $mailer = new Mailer();
    $mailer->from = 'donotreply@ant.com.mx';
    $mailer->tousers = $userDestino;
    $mailer->subject = 'Autorización de solicitud';
    $mailer->template = 'autorizar.xml';
    $mailer->type = 'authorization';
    $mailer->misc = [
        'solicitud' => $solicitud,
        'logo' => 'http://api.impact.beta.antfarm.mx/public/assets/images/logo.png'
    ];

    $result_message = null;
    try {
        $result_message = $mailer->sendEmail();
    } catch (Exception $e) {}
}

  
}