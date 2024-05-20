<?php

use Phalcon\Mvc\Controller;

class ActividadesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT * 
            FROM crm_actividades";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEtapa ($id) {
        $sql = "SELECT * from crm_actividades where etapa_id = $id order by nombre";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $actividades = ActividadesCrm::findFirst(
                [
                    'nombre = :nombre: AND etapa_id = :etapa_id:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'etapa_id' => intval($request['etapa_id'])
                    ]
                ]
            );
            if ($actividades) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esta actividad'];
            } else {
                $actividades = new ActividadesCrm();
                $actividades->setTransaction($tx);
                $actividades->etapa_id = intval($request['etapa_id']);
                $actividades->nombre = trim($request['nombre']);
                $actividades->tipo = $request['tipo'];
                $actividades->tiempo_respuesta = intval($request['tiempo_respuesta']);
                $actividades->tiempo_solucion = intval($request['tiempo_solucion']);
                $actividades->obligatorio = false;
                if ($actividades->create()) {
                    $oportunidades = Oportunidades::find('etapa_id='.intval($request['etapa_id']));
                    if (count($oportunidades) > 0) {
                        foreach ($oportunidades as $oportunidad) {
                            $tarea = new Tareas();
                            $tarea->setTransaction($tx);
                            $tarea->status = 'PENDIENTE';
                            $tarea->completado = false;
                            $tarea->oportunidad_id = $oportunidad->id;
                            $tarea->actividad_id = $actividades->id;
                            if ($tarea->create()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['result'] = 'error';
                                $this->content['error'] = Helpers::getErrors($tarea);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Se ha creado la actividad.'];
                                $tx->rollback();
                            }
                        }
                        if ($this->content['result'] === 'success') {
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la actividad.'];
                                $tx->commit();
                        }
                    } else {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la actividad.'];
                        $tx->commit();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($actividades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la actividad.'];
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
            $actividades = ActividadesCrm::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (etapa_id = :etapa_id:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'etapa_id' => intval($request['etapa_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($actividades) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una actividad con este nombre'];
            } else {
                $actividades = ActividadesCrm::findFirst($request['id']);
                if ($actividades) {
                    $actividades->setTransaction($tx);
                    $actividades->etapa_id = intval($request['etapa_id']);
                    $actividades->nombre = trim($request['nombre']);
                    $actividades->tipo = $request['tipo'];
                    $actividades->tiempo_respuesta = intval($request['tiempo_respuesta']);
                    $actividades->tiempo_solucion = intval($request['tiempo_solucion']);
                    if ($actividades->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la actividad'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($actividades);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
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

    public function updateObligatorio ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $actividades = ActividadesCrm::findFirst($request['id']);
            if ($actividades) {
                $actividades->setTransaction($tx);
                if ($actividades->obligatorio == true) {
                    $actividades->obligatorio = false;
                } else {
                    $actividades->obligatorio = true;
                }
                if ($actividades->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();   
    }

    public function updateLicitacion ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $actividades = ActividadesCrm::findFirst($request['id']);
            if ($actividades) {
                $actividades->setTransaction($tx);
                if ($actividades->licitacion == true) {
                    $actividades->licitacion = false;
                } else {
                    $actividades->licitacion = true;
                }
                if ($actividades->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();   
    }

    public function updateProyecto()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $actividades = ActividadesCrm::findFirst($request['id']);
            if ($actividades) {
                $actividades->setTransaction($tx);
                if ($actividades->proyecto == true) {
                    $actividades->proyecto = false;
                } else {
                    $actividades->proyecto = true;
                }
                if ($actividades->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
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
                    $actividades = ActividadesCrm::findFirst($request['id']);
                    $actividades->setTransaction($tx);
                    if ($actividades->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($actividades);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la actividad.'];
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