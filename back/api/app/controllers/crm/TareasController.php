<?php

use Phalcon\Mvc\Controller;

class TareasController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM crm_tareas
            ORDER BY nombre";
        $this->content['tareas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy ($id)
    {
        $sql = "SELECT id, tipo, status, fecha_limite, asignado, correo, asunto, descripcion, telefono, lugar, fecha_cita, created, created_by, modified, modified_by, completado, fecha_completado, oportunidad_id, actividad_id FROM crm_tareas
            where id = $id";
        $this->content['tareas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByOportunidad ($oportunidad_id) {
        $oportunidad = Oportunidades::findFirst($oportunidad_id);
        $etapa = $oportunidad->etapa_id;
        $sql = "SELECT a.id, a.status, c.ejecutivo_id, a.completado, a.oportunidad_id, a.actividad_id, b.nombre, b.obligatorio, b.tipo, c.nombre as oportunidad, d.nombre_comercial, b.licitacion, b.proyecto
            from crm_tareas as a
            inner join crm_actividades as b on a.actividad_id = b.id
            inner join crm_oportunidades as c on a.oportunidad_id = c.id
            inner join crm_prospectos as d on c.prospecto_id = d.id and c.id = $oportunidad_id AND  b.etapa_id = $etapa order by a.actividad_id";
        $tareas = $this->db->query($sql)->fetchAll();

        foreach ($tareas as $key => $tarea) {
            $tarea_id = $tarea['id'];
            $sql_documentos="SELECT * from crm_archivos where tarea_id = $tarea_id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();

            $nuevo = [];
            foreach ($documentos as $elemento){
                $documento=(object)array();
                $documento->id = $elemento['id'];
                $documento->tarea_id = $elemento['tarea_id'];
                $documento->extension = $elemento['extension'];
                $documento->nombre = $elemento['nombre'];
                if ($elemento['extension'] === 'docx') {
                    $documento->color = 'blue-9';
                    $documento->icon = 'fas fa-file-word';
                } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                    $documento->color = 'red-10';
                    $documento->icon = 'fas fa-file-pdf';
                } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                    $documento->color = 'light-green';
                    $documento->icon = 'fas fa-file-code';
                } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                    $documento->color = 'amber';
                    $documento->icon = 'fas fa-file-image';
                }
                array_push($nuevo,$documento);
            }
            
            $tareas[$key]['archivos'] = $nuevo;
        }

        $this->content['tareas'] = $tareas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByAllOportunidad($oportunidad_id){
        $sql = "SELECT a.id, a.status, c.ejecutivo_id, a.completado, a.oportunidad_id, a.actividad_id, b.nombre, b.obligatorio, b.tipo, c.nombre as oportunidad, d.nombre_comercial, b.licitacion, b.proyecto
            from crm_tareas as a
            inner join crm_actividades as b on a.actividad_id = b.id
            inner join crm_oportunidades as c on a.oportunidad_id = c.id
            inner join crm_prospectos as d on c.prospecto_id = d.id and c.id = $oportunidad_id order by a.actividad_id";
        $tareas = $this->db->query($sql)->fetchAll();

        foreach ($tareas as $key => $tarea) {
            $tarea_id = $tarea['id'];
            $sql_documentos="SELECT * from crm_archivos where tarea_id = $tarea_id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();

            $nuevo = [];
            foreach ($documentos as $elemento){
                $documento=(object)array();
                $documento->id = $elemento['id'];
                $documento->tarea_id = $elemento['tarea_id'];
                $documento->extension = $elemento['extension'];
                $documento->nombre = $elemento['nombre'];
                if ($elemento['extension'] === 'docx') {
                    $documento->color = 'blue-9';
                    $documento->icon = 'fas fa-file-word';
                } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                    $documento->color = 'red-10';
                    $documento->icon = 'fas fa-file-pdf';
                } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                    $documento->color = 'light-green';
                    $documento->icon = 'fas fa-file-code';
                } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                    $documento->color = 'amber';
                    $documento->icon = 'fas fa-file-image';
                }
                array_push($nuevo,$documento);
            }
            
            $tareas[$key]['archivos'] = $nuevo;
        }

        $this->content['tareas'] = $tareas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEjecutivo () {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $sql = "SELECT crm_tareas.id, crm_tareas.status, crm_tareas.completado, crm_tareas.fecha_completado, crm_tareas.oportunidad_id, crm_tareas.actividad_id, crm_oportunidades.nombre as oportunidad, crm_prospectos.nombre_comercial as prospecto, crm_actividades.tipo, crm_actividades.nombre as actividad, crm_tareas.created,crm_actividades.tiempo_respuesta, crm_actividades.tiempo_solucion, crm_actividades.licitacion, 'PARTICULAR' as task_type
            FROM crm_tareas
            inner join crm_actividades on crm_actividades.id = crm_tareas.actividad_id
            inner join crm_oportunidades on crm_oportunidades.id = crm_tareas.oportunidad_id
            inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id
            inner join crm_prospectos on crm_prospectos.id = crm_oportunidades.prospecto_id
            and crm_oportunidades.ejecutivo_id = $id and crm_tareas.completado = false and crm_oportunidades.status = 'PENDIENTE' and crm_actividades.etapa_id = crm_oportunidades.etapa_id

            union

            SELECT crm_tareas.id, crm_tareas.status, crm_tareas.completado, crm_tareas.fecha_completado, crm_tareas.oportunidad_id, 0 as actividad_id, (select crm_oportunidades.nombre as oportunidad from crm_oportunidades where crm_oportunidades.id = crm_tareas.oportunidad_id), '' as prospecto, crm_tareas.tipo, SUBSTRING(crm_tareas.descripcion, 0, 20) || '...' as actividad, crm_tareas.created, 0 as tiempo_respuesta, 0 as tiempo_solucion, false as licitacion, 'GENERAL' as task_type
            from crm_tareas where (crm_tareas.asignado = $id or crm_tareas.created_by = $id) and crm_tareas.completado = false and crm_tareas.actividad_id is null";
        $tareas = $this->db->query($sql)->fetchAll();
        foreach ($tareas as $key => $tarea) {
            $id = $tareas[$key]['id'];
            $tiempo_total = $tareas[$key]['tiempo_respuesta'] + $tareas[$key]['tiempo_solucion'];
            $sql_tiempo = "SELECT to_char(crm_tareas.created + interval '{$tiempo_total} day', 'YYYY-MM-DD HH24:MI') as end_time, to_char(crm_tareas.created + interval '{$tiempo_total} day', 'YYYY-MM-DD') as end_time_day FROM crm_tareas where crm_tareas.id = $id";
            $tiempo = $this->db->query($sql_tiempo)->fetchAll();
            $tareas[$key]['end_time'] = $tiempo[0]['end_time'];
            $tareas[$key]['end_time_day'] = $tiempo[0]['end_time_day'];
            $hoy = date('Y-m-d');
            $maniana = strtotime('1 day', strtotime($hoy));
            $tareas[$key]['hoy'] = $hoy;
            $tareas[$key]['maniana'] = date('Y-m-d', $maniana);
            if (date($tareas[$key]['end_time']) > date('Y-m-d', $maniana)) {
                $tareas[$key]['color'] = 'green';
            }
            if ($tareas[$key]['end_time_day'] == $tareas[$key]['hoy']) {
                $tareas[$key]['color'] = 'red';
            }
            if ($tareas[$key]['end_time_day'] == $tareas[$key]['maniana']) {
                $tareas[$key]['color'] = 'amber';
            }
            if (date($tareas[$key]['end_time']) < $hoy) {
                $tareas[$key]['color'] = 'red';
            }
            if ($tareas[$key]['task_type'] == 'GENERAL') {
                $tareas[$key]['color'] = 'lime-10';
            }
            // if ()
        }
        $this->content['tareas'] = $tareas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $tipo = trim(strtoupper($request['tipo']));
            $asignado = intval($request['asignado']);
            $correo = trim($request['correo']);
            $asunto = trim(strtoupper($request['asunto']));
            $descripcion = trim(strtoupper($request['descripcion']));
            $telefono = trim($request['telefono']);
            $lugar = trim($request['lugar']);
            $fecha_cita = $request['fecha_cita'];
            $fecha_limite = $request['fecha_limite'];
            $oportunidad = $request['oportunidad_id'];

            $tareas = new Tareas();
            $tareas->setTransaction($tx);
            $tareas->tipo = $tipo;
            $tareas->status = 'PENDIENTE';
            $tareas->fecha_limite = ($fecha_limite ==  '') ? null : date("Y/m/d", strtotime($fecha_limite));
            $tareas->asignado = $asignado;
            $tareas->correo = ($correo !=  '') ? $correo : null;
            $tareas->asunto = ($asunto !=  '') ? $asunto : null;
            $tareas->descripcion = ($descripcion ==  '') ? null : $descripcion;
            $tareas->telefono = ($telefono !=  '') ? $telefono : null;
            $tareas->lugar = ($lugar !=  '') ? $lugar : null;
            $tareas->fecha_cita = ($fecha_cita ==  '') ? null : date("Y/m/d", strtotime($fecha_cita));
            $tareas->completado = false;
            $tareas->oportunidad = $oportunidad;
            if ($tareas->create()) {
                $this->content['id'] = $tareas->id;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido la tarea.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($tareas);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir la tarea.'];
                $tx->rollback();
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

            $tareas = Tareas::findFirst($request['id']);
            if ($tareas) {
                $tareas->setTransaction($tx);
                $tipo = trim(strtoupper($request['tipo']));
                $asignado = intval($request['asignado']);
                $correo = trim($request['correo']);
                $asunto = trim(strtoupper($request['asunto']));
                $descripcion = trim(strtoupper($request['descripcion']));
                $telefono = trim($request['telefono']);
                $lugar = trim($request['lugar']);
                $fecha_cita = $request['fecha_cita'];
                $fecha_limite = $request['fecha_limite'];
                $oportunidad = $request['oportunidad_id'];

                $tareas->setTransaction($tx);
                $tareas->tipo = $tipo;
                $tareas->status = 'PENDIENTE';
                $tareas->fecha_limite = ($fecha_limite ==  '') ? null : date("Y/m/d", strtotime($fecha_limite));
                $tareas->asignado = $asignado;
                $tareas->correo = ($correo !=  '') ? $correo : null;
                $tareas->asunto = ($asunto !=  '') ? $asunto : null;
                $tareas->descripcion = ($descripcion ==  '') ? null : $descripcion;
                $tareas->telefono = ($telefono !=  '') ? $telefono : null;
                $tareas->lugar = ($lugar !=  '') ? $lugar : null;
                $tareas->fecha_cita = ($fecha_cita ==  '') ? null : date("Y/m/d", strtotime($fecha_cita));
                $tareas->completado = false;
                $tareas->oportunidad = $oportunidad;
                if ($tareas->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($tareas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la tarea.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateComplete () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $tareas = Tareas::findFirst($request['id']);
            $codigo_proyecto = "";
            if ($tareas) {
                $tareas->setTransaction($tx);
                if ($tareas->completado) {
                    $tareas->completado = false;
                    $tareas->fecha_completado = null;
                    $tareas->status = 'PENDIENTE';
                } else {
                    $tareas->completado = true;
                    $tareas->fecha_completado = date("Y-m-d H:i:s");
                    $tareas->status = 'COMPLETADA';
                }
                if ($tareas->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea.'];
                    $actividad = ActividadesCrm::findFirst($tareas->actividad_id);
                    if ($actividad) {
                        $this->content['result'] = 'success';
                        if ($actividad->licitacion) {
                            $oportunidad = Oportunidades::findFirst($tareas->oportunidad_id);
                            if ($oportunidad) {
                                if ($oportunidad->licitacion_id == null && $tareas->completado == true) {
                                    $licitaciones = new Licitacion();
                                    $licitaciones->setTransaction($tx);
                                    $licitaciones->empresa_id = $oportunidad->empresa_id;
                                    $licitaciones->folio = $oportunidad->folio;
                                    $licitaciones->titulo = $oportunidad->nombre;
                                    $licitaciones->status = 'CREADA';
                                    $licitaciones->entidad_id = $oportunidad->estado_id;
                                    $licitaciones->descripcion = $oportunidad->procedimiento;
                                    $licitaciones->monto_inicial = $oportunidad->monto_licitacion;
                                    $licitaciones->year = date('Y');
                                    if ($licitaciones->create()) {
                                        $oportunidad->setTransaction($tx);
                                        $oportunidad->licitacion_id = $licitaciones->id;
                                        if ($oportunidad->update()) {
                                            $this->content['result'] = 'success';
                                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea y se ha creado la licitación.'];
                                            // $tx->commit(); */
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($oportunidad);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la tarea.'];
                                            $tx->rollback();
                                        }
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($licitaciones);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la tarea.'];
                                        $tx->rollback();
                                    }
                                } else {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea.'];
                                    // $tx->commit();
                                } 
                            }
                        }
                        if ($actividad->proyecto) {
                            $oportunidad = Oportunidades::findFirst($tareas->oportunidad_id);
                            if ($oportunidad) {
                                // var_dump('si hay oportunidad');
                                if ($tareas->completado == true) {
                                    // var_dump('si hay tarea');
                                        if ($oportunidad->proyecto_id === null) {
                                            $recurso = false;
                                        } else {
                                            $recurso = Recurso::findFirst($oportunidad->proyecto_id);
                                        }
                                        // $recurso = false;
                                        if ($recurso) {
                                            $this->content['result'] = 'success';
                                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea.'];
                                            // var_dump('1');
                                        } else {
                                            // var_dump('2');
                                            $cliente = VntClientes::findFirst("razon_social = 'DEFAULT'");
                                            $subprograma = Subprograma::findFirst($oportunidad->subsector_id);

                                            if($cliente->codigo === "") {
                                                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El cliente no tiene un código, por lo que no podemos crear el código del proyecto'];
                                            } else if($subprograma->codigo === "") {
                                                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El subprograma no tiene un código, por lo que no podemos crear el código del proyecto'];
                                            } else if($cliente->codigo !== "" && $subprograma->codigo !== "") {
                                                $year = date('Y');
                                                $codigo = $this->generarCodigo($cliente,$subprograma,$year);

                                                $recursos = new Recurso();
                                                $recursos->setTransaction($tx);
                                                //campo para separa por anio
                                                $recursos->year = $year;
                                                $recursos->codigo = strtoupper($codigo);
                                                $recursos->cliente_id = $cliente->id;
                                                $recursos->subprograma_id = $subprograma->id;
                                                $recursos->fuente_financiamiento = 'DEFAULT';
                                                $recursos->monto = $oportunidad->valor_final;
                                                $recursos->nombre = $oportunidad->nombre;
                                                $recursos->adjudicacion = $oportunidad->adjudicacion;
                                                $codigo_proyecto = $recursos->codigo;
                                                if ($recursos->create()) {
                                                    $logs = new Logs();
                                                    $logs->setTransaction($tx);
                                                    $validUser = Auth::getUserData($this->config);
                                                    $logs->account_id = $validUser->user_id;
                                                    $logs->level_id = 10;
                                                    $logs->log = 'CREÓ EL PROYECT0 MEDIANTE UNA OPORTUNIDAD: ' . $codigo;
                                                    $logs->fecha = date("Y-m-d H:i:s");
                                                    if ($logs->create()){
                                                        $oportunidad->setTransaction($tx);
                                                        $oportunidad->proyecto_id = $recursos->id;
                                                        if ($oportunidad->update()) {
                                                            $this->content['result']='success';
                                                            $this->content['id'] = $recursos->id;
                                                            $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha creado el proyecto: ' . $codigo_proyecto];
                                                            // $tx->commit();
                                                        } else {
                                                            $this->content['result']='error';
                                                            $this->content['message']=['title' => '¡Éxito!', 'content' => 'No se ha podido actualizar la licitación.']; 
                                                            $tx->rollback();
                                                        }
                                                    } else {
                                                      $this->content['result']='error';
                                                      $this->content['message']=['title' => '¡Éxito!', 'content' => 'No se ha podido crear el proyecto.']; 
                                                      $tx->rollback();
                                                    }
                                                } else {
                                                    $this->content['error'] = Helpers::getErrors($recursos);
                                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el recurso.'];
                                                    $tx->rollback();
                                                }
                                            }
                                        }
                                    // }
                                } else {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea' . $codigo_proyecto];
                                    // $tx->commit();
                                } 
                            }
                        } 
                        if ($this->content['result'] === 'success') {
                            $this->content['result'] = 'success';
                            $tx->commit();
                        }
                    } else {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la tarea.'];
                        $tx->commit();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($tareas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la tarea.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function recursosAll ($recursos,$anio) {

        $consecutivos = array();

        foreach ($recursos as $recurso) {
            $codigo=explode("-",$recurso->codigo);
            if(intval($codigo[1])===intval($anio)){
                array_push($consecutivos, intval($codigo[2]));
            }
        } // aqui van los cambios
        if (sizeof($consecutivos) > 0) {
            return max($consecutivos) + 1; // este ya estaba
        } else {
            return 1;
        }
    }

    private function generarCodigo2($cliente,$subprograma,$recurso_id,$year){
        $anio = date('y',strtotime($year.'-01-01'));
        $cliente_id = $cliente->id;

        $recursos = Recurso::find(
            [
                'id != :id: AND cliente_id = :cliente_id: ',
                'bind' => [
                    'cliente_id' => intval($cliente_id),
                    'id' => intval($recurso_id)
                ]
            ]
        );
        
        if(count($recursos)>0){
            $consecutivo = $this->recursosAll($recursos,$anio);
        } else {
            $consecutivo = 1;
        }

        if($consecutivo<10){
            $codigo = $cliente->codigo . '-' . $anio . '-0' . $consecutivo . '-'.$subprograma->codigo;
        } else {
            $codigo = $cliente->codigo . '-' . $anio . '-' . $consecutivo . '-' .$subprograma->codigo;
        }

        return $codigo;
    }

    private function generarCodigo($cliente,$subprograma,$year){
        $anio = date('y',strtotime($year.'-01-01'));
        $cliente_id = $cliente->id;

        $recursos = Recurso::find(
            [
                'cliente_id = :cliente_id: ',
                'bind' => [
                    'cliente_id' => intval($cliente_id)
                ]
            ]
        );
        
        if(count($recursos)>0){
            $consecutivo = $this->recursosAll($recursos,$anio);
        } else {
            $consecutivo = 1;
        }

        if($consecutivo<10){
            $codigo = $cliente->codigo . '-' . $anio . '-0' . $consecutivo . '-'.$subprograma->codigo;
        } else {
            $codigo = $cliente->codigo . '-' . $anio . '-' . $consecutivo . '-' .$subprograma->codigo;
        }

        return $codigo;
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $tareas = Tareas::findFirst($request['id']);
                    $tareas->setTransaction($tx);

                    if ($tareas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($tareas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el giro.'];
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