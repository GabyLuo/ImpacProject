<?php

use Phalcon\Mvc\Controller;

class EtapasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT * 
            FROM crm_etapas";
        $this->content['etapas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCarril ($carril_id) {
        $sql = "SELECT * from crm_etapas where carril_id = $carril_id order by nivel asc";
        $this->content['etapas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getWithData ($carril_id, $year) {
        $sql = "SELECT * from crm_etapas where carril_id = $carril_id order by nivel asc";
        $etapas = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        $validUser = Auth::getUserData($this->config);
        $id_user = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$id_user")->SysRoles->name;
        $correo = $validUser->user_name;
        $data = $this->getNum_amount($role, 0);
        $monto_total = $data['sum'];
        $last_year = intval($year) - 1;

        foreach ($etapas as $etapa) {
            $valor_etapa = 0;
            $etapa_crm=(object)array();
            $etapa_crm->id = $etapa['id'];
            $id = $etapa['id'];
            $etapa_crm->nivel = $etapa['nivel'];
            $etapa_crm->carril_id = $etapa['carril_id'];
            $etapa_crm->nombre = $etapa['nombre'];
            $etapa_crm->color = $etapa['color'];
            $etapa_crm->cierre = $etapa['cierre'];
            $etapa_crm->dias = $etapa['dias'];
            $etapa_crm->porcentaje = $this->getPorcentaje($role, $id, $monto_total);
            if (strtoupper($role) === strtoupper('ROOT') || strtoupper($role) === strtoupper('ADMIN') || strtoupper($role) === strtoupper('DIRECTOR') || strtoupper($role) === strtoupper('Coordinador GDP') || $correo == 'jacosta@impactasesores.com' || $correo == 'descobar@impactasesores.com') {
                $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, (select count(act.id) from crm_actividades as act where act.etapa_id = o.etapa_id) as tareas_por_etapa, 
                (select count(t.id) 
                from crm_tareas as t
                inner join crm_actividades as act on t.actividad_id = act.id and act.etapa_id = o.etapa_id
                and t.completado = true and t.oportunidad_id = o.id) as completadas, 
                (select count(t.id) 
                from crm_tareas as t 
                inner join crm_actividades as act on t.actividad_id = act.id and act.etapa_id = o.etapa_id and t.completado = true and act.obligatorio = true and t.oportunidad_id = o.id) as completada_obligatorio, 
                (select count(act.id) from crm_actividades as act where act.etapa_id = o.etapa_id and act.obligatorio = true) as obligatorio, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
                from crm_oportunidades as o
                inner join crm_prospectos as p on o.prospecto_id = p.id and (date_part('year',o.created) = '$year' or (date_part('year',o.created) = '$last_year' AND o.status = 'PENDIENTE'))
                inner join sys_users on p.created_by = sys_users.id
                inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and o.etapa_id = $id and (o.status = 'PENDIENTE') order by id";
            } else {
                $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, (select count(act.id) from crm_actividades as act where act.etapa_id = o.etapa_id) as tareas_por_etapa, 
                (select count(t.id) 
                from crm_tareas as t
                inner join crm_actividades as act on t.actividad_id = act.id and act.etapa_id = o.etapa_id
                and t.completado = true and t.oportunidad_id = o.id) as completadas, 
                (select count(t.id) 
                from crm_tareas as t 
                inner join crm_actividades as act on t.actividad_id = act.id and act.etapa_id = o.etapa_id and t.completado = true and act.obligatorio = true and t.oportunidad_id = o.id) as completada_obligatorio, 
                (select count(act.id) from crm_actividades as act where act.etapa_id = o.etapa_id and act.obligatorio = true) as obligatorio, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
                from crm_oportunidades as o
                inner join crm_prospectos as p on o.prospecto_id = p.id and (date_part('year',o.created) = '$year' or (date_part('year',o.created) = '$last_year' AND o.status = 'PENDIENTE'))
                inner join sys_users on p.created_by = sys_users.id
                inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and o.etapa_id = $id and o.ejecutivo_id = $id_user and o.status = 'PENDIENTE' order by id";
            }
            
            $prospectos = $this->db->query($sql_oportunidades)->fetchAll();
            $array_prospectos = [];
            foreach ($prospectos as $prospecto) {
                $valor_etapa = $valor_etapa + floatval($prospecto['valor_final']);
                $prospect = (object)array();
                $prospect->id = $prospecto['id'];
                $prospect->nombre_contacto = $prospecto['nombre_contacto'];
                $prospect->prospecto_id = $prospecto['prospecto_id'];
                $prospect->nombre = $prospecto['nombre'];
                $prospect->valor = $prospecto['valor'];
                $prospect->valor_final = $prospecto['valor_final'];
                $prospect->etapa_id = $prospecto['etapa_id'];
                $prospect->ejecutivo_id = $prospecto['ejecutivo_id'];
                $prospect->status = $prospecto['status'];
                $prospect->carril_id = $prospecto['carril_id'];
                $prospect->sector_id = $prospecto['sector_id'];
                $prospect->subsector_id = $prospecto['subsector_id'];
                $prospect->nombre_comercial = $prospecto['nombre_comercial'];
                $prospect->created_by = $prospecto['created_by'];
                $prospect->creador = $prospecto['creador'];
                $prospect->asignado = $prospecto['asignado'];
                $prospect->oportunidad_creada = $prospecto['oportunidad_creada'];
                $prospect->sector = $prospecto['sector'];
                $prospect->subsector = $prospecto['subsector'];
                $prospect->tareas_por_etapa = $prospecto['tareas_por_etapa'];
                $prospect->completadas = $prospecto['completadas'];
                $prospect->tipo = $prospecto['tipo'];
                $prospect->tipo_prospecto = $prospecto['tipo_prospecto'];
                $prospect->empresa_id = $prospecto['empresa_id'];
                $prospect->orden = $prospecto['orden'];
                $prospect->ente = $prospecto['ente'];
                $prospect->adjudicacion = $prospecto['adjudicacion'];
                $prospect->estado_id = $prospecto['estado_id'];
                $prospect->municipio_id = $prospecto['municipio_id'];
                $prospect->folio = $prospecto['folio'];
                $prospect->procedimiento = $prospecto['procedimiento'];
                $prospect->monto_licitacion = $prospecto['monto_licitacion'];
                $prospect->nombre_ejecutivo = $prospecto['nombre_ejecutivo'];
                $prospect->area = $prospecto['area'];
                $array_tareas = [];
                for ($i=0; $i<$prospecto['tareas_por_etapa']; $i++) {
                    $tarea = (object)array();
                    $tarea->num = $i;
                    if ($i < $prospecto['completadas']) {
                        $tarea->completado = true;
                    } else {
                        $tarea->completado = false;
                    }
                    array_push($array_tareas, $tarea);
                }
                $prospect->tareas = $array_tareas;
                $prospect->completada_obligatorio = $prospecto['completada_obligatorio'];
                $prospect->obligatorio = $prospecto['obligatorio'];
                array_push($array_prospectos, $prospect);
            }
            $etapa_crm->valor_prospectos = $valor_etapa;
            $etapa_crm->prospectos = $array_prospectos;
            
            array_push($nuevo, $etapa_crm);
        }
        $this->content['etapas'] = $nuevo;
        $this->content['user_id'] = $id_user;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getNum_amount ($rol, $etapa_id) {
        $sql = "SELECT count(*), sum(valor) from crm_oportunidades where status = 'PENDIENTE'";
        if (strtoupper($rol) == strtoupper('VENTAS')) {
            $validUser = Auth::getUserData($this->config);
            $id_user = $validUser->user_id;
            $sql = $sql . ' and ejecutivo_id ='.$id_user;
        }
        if ($etapa_id > 0) {
            $sql = $sql . ' and etapa_id ='.$etapa_id;
        }
        return $this->db->query($sql)->fetchAll()[0];
    }

    private function getPorcentaje ($rol, $etapa_id, $monto) {
        $data_single = $this->getNum_amount($rol, $etapa_id);
        if ($monto > 0 && $data_single['sum'] > 0) {
            return number_format((($data_single['sum'] * 100)/$monto),2);
        } else {
            return 0;
        }
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $etapas = Etapas::findFirst(
                [
                    'carril_id = :carril_id: AND nombre = :nombre:',
                    'bind' => [
                        'carril_id' => intval($request['carril_id']),
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($etapas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esta etapa'];
            } else {
                $carril = intval($request['carril_id']);
                $sql = "SELECT * from crm_etapas where carril_id = $carril order by nivel desc";
                $data = $this->db->query($sql)->fetchAll();
                if ($data) {
                    $nivel = $data[0]['nivel'] + 1;
                } else {
                    $nivel = 1;
                }

                $sumaPorcentaje = "SELECT coalesce(sum(porcentaje),0) as porcentaje from crm_etapas where carril_id = $carril";
                $dataPorcentaje = $this->db->query($sumaPorcentaje)->fetchAll();
                if (($dataPorcentaje[0]['porcentaje'] + $request['porcentaje']) > 100) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El valor total del porcentaje supera el 100%'];
                } else {
                    $etapas = new Etapas();
                    $etapas->setTransaction($tx);
                    $etapas->carril_id = intval($request['carril_id']);
                    $etapas->nombre = strtoupper($request['nombre']);
                    $etapas->color = $request['color'];
                    $etapas->nivel = $nivel;
                    $etapas->porcentaje = $request['porcentaje'];
                    $etapas->cierre = false;
                    $etapas->dias = $request['dias'];
                    if ($etapas->create()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la etapa.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($etapas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la etapa.'];
                        $tx->rollback();
                    }
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
            $etapas = Etapas::findFirst(
                [
                    'id != :id: AND (carril_id = :carril_id:) AND (nombre = :nombre:)',
                    'bind' => [
                        'carril_id' => intval($request['carril_id']),
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($etapas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un carril con este nombre'];
            } else {
                $carril_id = intval($request['carril_id']);
                $sumaPorcentaje = "SELECT coalesce(sum(porcentaje),0) as porcentaje from crm_etapas where carril_id = $carril_id and id != " . intval($request['id']);
                $dataPorcentaje = $this->db->query($sumaPorcentaje)->fetchAll();
                if (($dataPorcentaje[0]['porcentaje'] + $request['porcentaje']) > 100) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El valor total del porcentaje supera el 100%'];
                } else {
                    $etapas = Etapas::findFirst($request['id']);
                    if ($etapas) {
                        $etapas->setTransaction($tx);
                        $etapas->nombre = strtoupper($request['nombre']);
                        $etapas->color = $request['color'];
                        $etapas->porcentaje = $request['porcentaje'];
                        $etapas->dias = $request['dias'];
                        if ($etapas->update()) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la etapa'];
                            $tx->commit();
                        } else {
                            $this->content['error'] = Helpers::getErrors($etapas);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la etapa'];
                            $tx->rollback();
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateCierre ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $etapas = Etapas::findFirst($request['id']);
            //
            $carril = intval($request['carril_id']);
            $sql = "SELECT * from crm_etapas where carril_id = $carril order by nivel desc";
            $data = $this->db->query($sql)->fetchAll();
            if ($data) {
                $nivel = $data[0]['nivel'];
            } else {
                $nivel = 0;
            }
            //
            if ($etapas) {
                if ($nivel == $etapas->nivel) {
                    $etapas->setTransaction($tx);
                    if ($etapas->cierre == true) {
                        $etapas->cierre = false;
                    } else {
                        $etapas->cierre = true;
                    }
                    if ($etapas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la etapa'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($etapas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la etapa'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Esta etapa no es la última del carril, no se marcará como cierre'];
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
                    $etapas = Etapas::findFirst($request['id']);
                    $etapas->setTransaction($tx);
                    if ($etapas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($etapas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la etapa.'];
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