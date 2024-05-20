<?php

use Phalcon\Mvc\Controller;

class OportunidadesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT * 
            FROM crm_oportunidades";
        $this->content['oportunidades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEjecutivo ($id) {
        $sql = "SELECT * from crm_oportunidades where ejecutivo_id = $id";
        $this->content['oportunidades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getGanadas ($carril_id, $year) {
        
        $validUser = Auth::getUserData($this->config);
        $id_user = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$id_user")->SysRoles->name;
        
        
        if (strtoupper($role) === strtoupper('ROOT') || strtoupper($role) === strtoupper('ADMIN') || strtoupper($role) === strtoupper('DIRECTOR')) {
            $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
            from crm_oportunidades as o
            inner join crm_prospectos as p on o.prospecto_id = p.id 
            inner join sys_users on p.created_by = sys_users.id
            inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and (o.status = 'LOGRADA')
            where exists
            (select id from crm_logs where oportunidad_id = o.id and date_part('year',crm_logs.created) = '$year') order by id";
        } else {
            $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
            from crm_oportunidades as o
            inner join crm_prospectos as p on o.prospecto_id = p.id
            inner join sys_users on p.created_by = sys_users.id
            inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and o.ejecutivo_id = $id_user and o.status = 'LOGRADA' 
            and exists
            (select id from crm_logs where oportunidad_id = o.id and date_part('year',crm_logs.created) = '$year') order by id";
        }
        $prospectos = $this->db->query($sql_oportunidades)->fetchAll();
        $array_prospectos = [];
        foreach ($prospectos as $prospecto) {
            $prospect = (object)array();
            $prospect->id = $prospecto['id'];
            $prospect->nombre_contacto = $prospecto['nombre_contacto'];
            $prospect->prospecto_id = $prospecto['prospecto_id'];
            $prospect->nombre = $prospecto['nombre'];
            $prospect->valor = $prospecto['valor'];
            $prospect->valor_final = $prospecto['valor_final'];
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
            array_push($array_prospectos, $prospect);
        }
        $this->content['oportunidades'] = $array_prospectos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getNoLogradas ($carril_id, $year) {
        
        $validUser = Auth::getUserData($this->config);
        $id_user = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$id_user")->SysRoles->name;
        
        if (strtoupper($role) === strtoupper('ROOT') || strtoupper($role) === strtoupper('ADMIN') || strtoupper($role) === strtoupper('DIRECTOR')) {
            $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
            from crm_oportunidades as o
            inner join crm_prospectos as p on o.prospecto_id = p.id
            inner join sys_users on p.created_by = sys_users.id
            inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and o.status = 'NO LOGRADA' and date_part('year',o.created) = '$year' order by id";
        } else {
            $sql_oportunidades = "SELECT o.id, o.prospecto_id, o.nombre, o.valor, o.valor_final, o.etapa_id, o.ejecutivo_id, o.status, o.carril_id, o.sector_id, o.subsector_id, p.nombre_comercial, p.nombre_contacto, p.created_by, sys_users.nickname as creador, a.nickname as asignado, to_char(o.created, 'DD/MM/YYYY') as oportunidad_creada, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = o.sector_id) as sector, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = o.subsector_id) as subsector, o.tipo_prospecto, o.tipo, o.empresa_id, o.orden, o.ente, o.adjudicacion, o.estado_id, o.municipio_id, o.folio, o.procedimiento, o.monto_licitacion, a.nickname as nombre_ejecutivo, o.area
            from crm_oportunidades as o
            inner join crm_prospectos as p on o.prospecto_id = p.id
            inner join sys_users on p.created_by = sys_users.id
            inner join sys_users as a on o.ejecutivo_id = a.id and o.carril_id = $carril_id and o.ejecutivo_id = $id_user and o.status = 'NO LOGRADA' and date_part('year',o.created) = '$year' order by id";
        }
        
        $prospectos = $this->db->query($sql_oportunidades)->fetchAll();
        $this->content['oportunidades'] = $prospectos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getHistorial ($id) {
        $op_id = $id;
        $sql = "SELECT crm_oportunidades.id as oportunidad_id, 'CREACIÓN' as tipo, crm_oportunidades.created, crm_oportunidades.created_by, 'La oportunidad fue creada por: ' || sys_users.nickname as descripcion, sys_users.nickname, 0 as orden, 'save' as icon, 'green' as color
            from crm_oportunidades
            inner join sys_users on sys_users.id = crm_oportunidades.created_by
            and crm_oportunidades.id = $id
            union
    
            select crm_logs.oportunidad_id, crm_logs.tipo, crm_logs.created,
            crm_logs.created_by, case when crm_logs.tipo = 'AVANCE' then crm_logs.descripcion || ' ' || crm_etapas.nombre
            when crm_logs.tipo='RETROCESO' then crm_logs.descripcion || ', etapa actual: ' || crm_etapas.nombre when crm_logs.tipo='REASIGNACIÓN' then crm_logs.descripcion || sys_users.nickname end as descripcion,
            sys_users.nickname, crm_logs.oportunidad_id as orden, case when crm_logs.tipo = 'AVANCE' then 'fa-arrow-down' when crm_logs.tipo='RETROCESO' then 'fa-arrow-up' end as icon, case when crm_logs.tipo = 'AVANCE' then 'blue' when crm_logs.tipo='RETROCESO' then 'red' end as color
            from crm_logs
            inner join sys_users on sys_users.id = crm_logs.created_by
            inner join crm_etapas on crm_etapas.id = crm_logs.etapa_id
            and crm_logs.oportunidad_id = $id
            union
            
            select crm_logs.oportunidad_id, crm_logs.tipo, crm_logs.created,
            crm_logs.created_by, case when crm_logs.tipo = 'REASIGNACIÓN' then crm_logs.descripcion || ' ' || sys_users.nickname end as descripcion,
            sys_users.nickname, crm_logs.oportunidad_id as orden, 'fas fa-user' as icon, 'amber' as color from crm_logs
            inner join sys_users on sys_users.id = crm_logs.user_id and crm_logs.oportunidad_id = $id
            union 

            select crm_logs.oportunidad_id, crm_logs.tipo, crm_logs.created,
            crm_logs.created_by, case when crm_logs.tipo = 'LOGRO' then crm_logs.descripcion end as descripcion,
            sys_users.nickname, crm_logs.oportunidad_id as orden, 'done_all' as icon, 'green' as color from crm_logs
            inner join sys_users on sys_users.id = crm_logs.created_by and crm_logs.oportunidad_id = $id and crm_logs.tipo = 'LOGRO'
            order by oportunidad_id, orden, created";

        $historial = $this->db->query($sql)->fetchAll();
        $fecha_1 = '';
        $contador = 0;
        $side = 'left';
        $contador_fin = count($historial);
        foreach ($historial as $keyH => $hist) {
            $fecha_1 = $historial[$keyH]['created'];
            $and = " and crm_comentarios.created > '$fecha_1'";
            if ($contador < ($contador_fin-1)) {
                $contador++;
                $fecha_2 = $historial[$contador]['created'];
                $and = " and crm_comentarios.created BETWEEN '$fecha_1' and '$fecha_2'";
            }
            
            $sqlComment = "SELECT crm_comentarios.id, crm_comentarios.tarea_id, crm_comentarios.created_by, crm_comentarios.comentario, crm_comentarios.created, sys_users.nickname 
                FROM crm_comentarios 
                INNER JOIN sys_users on sys_users.id = crm_comentarios.created_by and crm_comentarios.oportunidad_id = $op_id {$and}
                ORDER BY crm_comentarios.created asc";
            $comentarios = $this->db->query($sqlComment)->fetchAll();
            foreach ($comentarios as $key => $cliente) {
                $id = $comentarios[$key]['id'];
                $comentario = $comentarios[$key]['comentario'];
                $all_comentarios = explode("\n", $comentario);
                $nuevo = [];
                for ($i=0; $i<count($all_comentarios); $i++) {
                    $comment=(object)array();
                    $comment->id = $i;
                    $comment->comment = $all_comentarios[$i];
                    array_push($nuevo, $comment);
                }
                $sql_documentos="SELECT * from crm_archivos_comentarios where comentario_id = $id";
                $documentos = $this->db->query($sql_documentos)->fetchAll();

                $nuevo_doc = [];
                foreach ($documentos as $elemento){
                    $documento=(object)array();
                    $documento->id = $elemento['id'];
                    $documento->comentario_id = $elemento['comentario_id'];
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
                    array_push($nuevo_doc,$documento);
                }
                
                $comentarios[$key]['archivos'] = $nuevo_doc;
                $comentarios[$key]['comentario'] = $nuevo;
            }
            $historial[$keyH]['hist'] = $comentarios;
            $historial[$keyH]['side'] = $side;
            if ($side == 'left') {
                $side = 'right';
            } else {
                $side = 'left';
            }
        }
        $this->content['history'] = $historial;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getResumen ($year) {
        $sql = "SELECT (select sum(sys_users.meta_anual) from sys_users inner join sys_grants on sys_grants.user_id = sys_users.id
            inner join sys_roles on sys_roles.id = sys_grants.role_id and (sys_roles.name = 'Ventas' or sys_roles.name = 'Coordinador GDP' or sys_roles.name = 'Director')) as meta_anual, count(*) as oportunidades_registradas, coalesce(sum(crm_oportunidades.valor_final)) as o_monto_registradas, (select count(*) from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.status = 'PENDIENTE' and date_part('year', crm_oportunidades.created) = '$year') as oportunidades_cierre, (select count(*) from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.status = 'LOGRADA' and date_part('year', crm_oportunidades.created) = '$year') as oportunidades_logradas, (select coalesce(sum(crm_oportunidades.valor_final),0) from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.status = 'PENDIENTE' and date_part('year', crm_oportunidades.created) = '$year') as o_monto_cierre, (select coalesce(sum(crm_oportunidades.valor_final),0) from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.status = 'LOGRADA' and date_part('year', crm_oportunidades.created) = '$year') as o_monto_logradas,
            (select count(*) from crm_oportunidades where crm_oportunidades.status = 'NO LOGRADA' and date_part('year', crm_oportunidades.created) = '$year') as oportunidades_no_logradas,
            (select coalesce(sum(crm_oportunidades.valor_final),0) from crm_oportunidades where crm_oportunidades.status = 'NO LOGRADA' and date_part('year', crm_oportunidades.created) = '$year') as o_monto_no_logradas
         from crm_oportunidades where status != 'ELIMINADA' and date_part('year', crm_oportunidades.created) = '$year'";
        $sql_vendedores = "SELECT sys_users.nickname, sys_users.meta_anual, (select count(crm_oportunidades.id) as o_registradas from crm_oportunidades where ejecutivo_id = sys_users.id and crm_oportunidades.status != 'ELIMINADA' and crm_oportunidades.status != 'NO LOGRADA' and date_part('year', crm_oportunidades.created) = '$year'), (select count(crm_oportunidades.id) as o_cierre from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.ejecutivo_id = sys_users.id and crm_oportunidades.status = 'PENDIENTE' and date_part('year', crm_oportunidades.created) = '$year'), (select coalesce(sum(crm_oportunidades.valor_final), 0) as o_monto_registradas from crm_oportunidades where ejecutivo_id = sys_users.id and crm_oportunidades.status != 'ELIMINADA' and crm_oportunidades.status != 'NO LOGRADA' and date_part('year', crm_oportunidades.created) = '$year'), (select coalesce(sum(crm_oportunidades.valor_final),0) as o_monto_cierre from crm_oportunidades inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id and crm_etapas.cierre = true and crm_oportunidades.ejecutivo_id = sys_users.id and crm_oportunidades.status = 'PENDIENTE' and date_part('year', crm_oportunidades.created) = '$year'), (select count(crm_oportunidades.id) as o_ganadas from crm_oportunidades where ejecutivo_id = sys_users.id and crm_oportunidades.status = 'LOGRADA' and date_part('year', crm_oportunidades.created) = '$year'), (select coalesce(sum(crm_oportunidades.valor_final), 0) as o_monto_ganadas from crm_oportunidades where ejecutivo_id = sys_users.id and crm_oportunidades.status = 'LOGRADA' and date_part('year', crm_oportunidades.created) = '$year')
            from sys_users inner join sys_grants on sys_grants.user_id = sys_users.id -- and sys_grants.role_id = 19 
            inner join sys_roles on sys_roles.id = sys_grants.role_id and (sys_roles.name = 'Ventas' or sys_roles.name = 'Coordinador GDP' or sys_roles.name = 'Director')";
        $data = $this->db->query($sql)->fetchAll();
        $vendedores = $this->db->query($sql_vendedores)->fetchAll();
        $meta_anual = $data[0]['meta_anual'] > 0 ? $data[0]['meta_anual'] : 0;
        $meta_anual_k = $data[0]['meta_anual'];
        $o_monto_registradas = $data[0]['o_monto_registradas'] > 0 ? $data[0]['o_monto_registradas'] : 0;
        $o_monto_registradas_k = $data[0]['o_monto_registradas'];
        $o_monto_cierre = $data[0]['o_monto_cierre'] > 0 ? $data[0]['o_monto_cierre'] : 0;
        $o_monto_cierre_k = $data[0]['o_monto_cierre'];
        $o_monto_logradas = $data[0]['o_monto_logradas'] > 0 ? $data[0]['o_monto_logradas'] : 0;
        $porcentaje_logrado = $data[0]['o_monto_logradas'] > 0 ? ($data[0]['o_monto_logradas'] * 100)/$data[0]['meta_anual'] : 0;
        if ($porcentaje_logrado >= 0 && $porcentaje_logrado <= 60) {
            $color = 'kpi-card bg-red text-white';
            $color_icon = 'kpi-icon text-red-2 col-4';
            $color_subtitle = 'text-subtitle2 text-center text-red-2 margint';
        } else if ($porcentaje_logrado >= 61 && $porcentaje_logrado <= 80) {
            $color = 'kpi-card bg-amber text-white';
            $color_icon = 'kpi-icon text-amber-2 col-4';
            $color_subtitle = 'text-subtitle2 text-center text-amber-2 margint';
        } else if ($porcentaje_logrado >= 81 && ($porcentaje_logrado <= 100 || $porcentaje_logrado >= 100)) {
            $color = 'kpi-card bg-green text-white';
            $color_icon = 'kpi-icon text-green-2 col-4';
            $color_subtitle = 'text-subtitle2 text-center text-green-2 margint';
        }
        $this->content['resumen'] = $data;
        $this->content['resumen'][0]['meta_anual'] = number_format($meta_anual,2,'.',',');
        $this->content['resumen'][0]['o_monto_registradas'] = number_format($o_monto_registradas,2,'.',',');
        $this->content['resumen'][0]['o_monto_cierre'] = number_format($o_monto_cierre,2,'.',',');
        $this->content['resumen'][0]['o_monto_logradas'] = number_format($o_monto_logradas,2,'.',',');
        $this->content['resumen'][0]['porcentaje'] = round($porcentaje_logrado,2);
        $this->content['resumen'][0]['color'] = $color;
        $this->content['resumen'][0]['color_icon'] = $color_icon;
        $this->content['resumen'][0]['color_subtitle'] = $color_subtitle;
        $tam_vendedores = count($vendedores);
        $row_end=(object)array();
        $row_end->nickname = 'Total';
        $row_end->meta_anual = $meta_anual_k;
        $row_end->o_registradas = '';
        $row_end->o_cierre = '';
        $row_end->o_ganadas = $data[0]['oportunidades_logradas'] > 0 ? $data[0]['oportunidades_logradas'] : 0;;
        $row_end->o_monto_registradas = $o_monto_registradas_k;
        $row_end->o_monto_cierre = $o_monto_cierre_k;
        $row_end->o_monto_ganadas = $o_monto_logradas;
        array_push($vendedores, $row_end);
        $this->content['vendedores'] = $vendedores;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $validUser = Auth::getUserData($this->config);
            $id = $validUser->user_id;

            $oportunidades = Oportunidades::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre'])
                    ]
                ]
            );
            if ($oportunidades) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esta oportunidad'];
            } else {
                $carril = Crm::findFirst(intval($request['carril_id']))->id;
                $sql_etapa = "SELECT * from crm_etapas where carril_id = $carril order by nivel asc";
                $data_etapas = $this->db->query($sql_etapa)->fetchAll(); // ver si el carril tiene etapas
                if ($data_etapas) {
                    $sql_actividades = "SELECT * from crm_actividades where etapa_id =".$data_etapas[0]['id'];
                    $data_actividades = $this->db->query($sql_actividades)->fetchAll();
                    if ($data_actividades) {
                        if ($request['tipo_prospecto'] === 'NUEVO') {
                            $prospectos = Prospectos::findFirst(
                                [
                                    'nombre_comercial = :nombre_comercial:',
                                    'bind' => [
                                        'nombre_comercial' => trim(strtoupper($request['prospecto']))
                                    ]
                                ]
                            );
                            if ($prospectos) {
                                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este prospecto.'];
                            } else {
                                $prospectos = new Prospectos();
                                $prospectos->setTransaction($tx);
                                $prospectos->nombre_comercial = trim($request['prospecto']);
                                $prospectos->giro_comercial = intval($request['giro_comercial']);
                                $prospectos->nombre_contacto = trim($request['nombre_contacto']);
                                $prospectos->email = trim($request['email']);
                                $prospectos->telefono = trim($request['telefono']);
                                if ($prospectos->create()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($prospectos);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                    $tx->rollback();
                                }
                            }
                        }
                        $oportunidades = new Oportunidades();
                        $oportunidades->setTransaction($tx);
                        if ($request['tipo_prospecto'] === 'NUEVO') {
                            $oportunidades->prospecto_id = $prospectos->id;
                        } else {
                            $oportunidades->prospecto_id = intval($request['prospecto_id']);
                        }
                        $oportunidades->nombre = trim($request['nombre']);
                        $oportunidades->valor = floatval($request['monto']);
                        $oportunidades->valor_final = floatval($request['monto']);
                        $oportunidades->etapa_id = $data_etapas[0]['id'];
                        $oportunidades->ejecutivo_id = intval($request['ejecutivo_id']);
                        $oportunidades->status = 'PENDIENTE';
                        $oportunidades->carril_id = intval($request['carril_id']);
                        $oportunidades->sector_id = intval($request['sector_id']);
                        $oportunidades->subsector_id = intval($request['subsector_id']);
                        $oportunidades->tipo = $request['tipo'];
                        $oportunidades->tipo_prospecto = 'EXISTENTE';
                        $oportunidades->empresa_id = intval($request['empresa_id']);
                        $oportunidades->orden = $request['orden'];
                        $oportunidades->ente = $request['ente'];
                        $oportunidades->adjudicacion = $request['adjudicacion'];
                        $oportunidades->estado_id = intval($request['estado_id']);
                        $oportunidades->municipio_id = intval($request['municipio_id']);
                        $oportunidades->area = $request['area'];
                        if ($oportunidades->create()) {
                            $logs = new CrmLogs();
                            $logs->setTransaction($tx);
                            $logs->oportunidad_id = $oportunidades->id;
                            $logs->tipo = 'AVANCE';
                            $logs->descripcion = 'La oportunidad avanzó a la etapa de ';
                            $logs->etapa_id = $data_etapas[0]['id'];
                            if ($logs->create()) {
                                foreach ($data_actividades as $actividad) {
                                    $tarea = new Tareas();
                                    $tarea->setTransaction($tx);
                                    $tarea->status = 'PENDIENTE';
                                    // $tarea->fecha_limite = date("Y-m-d H:i:s", strtotime($request['fecha_limite']));
                                    $tarea->asignado = intval($request['ejecutivo_id']);
                                    $tarea->completado = false;
                                    $tarea->oportunidad_id = $oportunidades->id;
                                    $tarea->actividad_id = $actividad['id'];
                                    if ($tarea->create()) {
                                        // $this->content['result'] = 'success';
                                        if ($id !== intval($request['ejecutivo_id'])) {
                                            $logs = new CrmLogs();
                                            $logs->setTransaction($tx);
                                            $logs->oportunidad_id = $oportunidades->id;
                                            $logs->tipo = 'REASIGNACIÓN';
                                            $logs->descripcion = 'La oportunidad fue reasignada al usuario: ';
                                            $logs->user_id = intval($request['ejecutivo_id']);
                                            if ($logs->create()) {
                                                $this->content['result'] = 'success';
                                            } else {
                                                $this->content['result'] = 'error';
                                                $this->content['error'] = Helpers::getErrors($logs);
                                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se guardará la oportunidad.'];
                                                $tx->rollback();
                                            }
                                        } else {
                                            $this->content['result'] = 'success';
                                        }
                                    } else {
                                        $this->content['result'] = 'error';
                                        $this->content['error'] = Helpers::getErrors($tarea);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                        $tx->rollback();
                                    }
                                }
                                if ($this->content['result'] === 'success') {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la oportunidad.'];
                                    $this->content['id_user'] = intval($request['ejecutivo_id']);
                                    $this->content['oportunidad_id'] = $oportunidades->id;
                                    $tx->commit();
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($logs);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['error'] = Helpers::getErrors($oportunidades);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No hay tareas en el primer paso, no se creará la oportunidad'];
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este carril no tiene etapas'];
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function nextStep () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $validUser = Auth::getUserData($this->config);
            $id = $validUser->user_id;

            $carril = intval($request['carril_id']);
            $etapa = intval($request['etapa_id']);
            $paso = Etapas::findFirst($etapa)->nivel;
            $sql_niveles = "SELECT * from crm_etapas where carril_id = $carril and nivel > $paso order by nivel asc limit 1";
            $data_niveles = $this->db->query($sql_niveles)->fetchAll();
            if ($data_niveles) {
                $sql_actividades = "SELECT * from crm_actividades where etapa_id =".$data_niveles[0]['id'];
                $data_actividades = $this->db->query($sql_actividades)->fetchAll();
                if ($data_actividades) {
                    $nuevo_nivel = $data_niveles[0]['id'];
                    $oportunidades = Oportunidades::findFirst($request['id']);
                    if ($oportunidades) {
                        $oportunidades->setTransaction($tx);
                        $oportunidades->etapa_id = $nuevo_nivel;
                        if ($oportunidades->update()) {
                            $logs = new CrmLogs();
                            $logs->setTransaction($tx);
                            $logs->oportunidad_id = $oportunidades->id;
                            $logs->tipo = 'AVANCE';
                            $logs->descripcion = 'La oportunidad avanzó a la etapa de ';
                            $logs->etapa_id = $data_niveles[0]['id'];
                            if ($logs->create()) {
                                $sql_tareas = "SELECT t.oportunidad_id, t.actividad_id from crm_tareas as t inner join crm_actividades as a on t.actividad_id = a.id and a.etapa_id = $nuevo_nivel and t.oportunidad_id =".$oportunidades->id;
                                $data_tareas = $this->db->query($sql_tareas)->fetchAll();
                                if ($data_tareas) {
                                    if (count($data_tareas) == count($data_actividades)) {
                                        $this->content['result'] = 'success';
                                    } else {
                                        foreach ($data_actividades as $act) {
                                            $existe_tarea = false;
                                            foreach ($data_tareas as $tar) {
                                                if ($act['id'] == $tar['actividad_id']) {
                                                    $existe_tarea = true;
                                                    break;
                                                }
                                            }
                                            if (!$existe_tarea) {
                                                $tarea = new Tareas();
                                                $tarea->setTransaction($tx);
                                                $tarea->status = 'PENDIENTE';
                                                $tarea->asignado = $id;
                                                $tarea->completado = false;
                                                $tarea->oportunidad_id = $oportunidades->id;
                                                $tarea->actividad_id = $act['id'];
                                                if ($tarea->create()) {
                                                    $this->content['result'] = 'success';
                                                } else {
                                                    $this->content['result'] = 'error';
                                                    $this->content['error'] = Helpers::getErrors($tarea);
                                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                                    $tx->rollback();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($data_actividades as $actividad) {
                                        $tarea = new Tareas();
                                        $tarea->setTransaction($tx);
                                        $tarea->status = 'PENDIENTE';
                                        $tarea->asignado = $id;
                                        $tarea->completado = false;
                                        $tarea->oportunidad_id = $oportunidades->id;
                                        $tarea->actividad_id = $actividad['id'];
                                        if ($tarea->create()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['result'] = 'error';
                                            $this->content['error'] = Helpers::getErrors($tarea);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                            $tx->rollback();
                                        }
                                    }
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($logs);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                $tx->rollback();
                            }
                            if ($this->content['result'] === 'success') {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'La oportunidad ha avanzado al siguiente paso.'];
                                $tx->commit();
                            }
                        } else {
                            $this->content['result'] = 'error';
                            $this->content['error'] = Helpers::getErrors($tarea);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se actualizar la oportunidad.'];
                            $tx->rollback();
                        }
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No hay tareas en el siguiente paso, no se actualizará la oportunidad'];
                }
            } else {
                $oportunidades = Oportunidades::findFirst($request['id']);
                $codigo = substr(''.$oportunidades->nombre, 0, 3);
                $cliente = VntClientes::findFirst(
                    [
                        'codigo = :codigo: AND nombre_corto = :nombre_corto:',
                        'bind' => [
                            'codigo' => $codigo,
                            'nombre_corto' => $oportunidades->nombre
                        ]
                    ]
                );
                $oportunidades = Oportunidades::findFirst($request['id']);
                if ($oportunidades) {
                    $oportunidades->setTransaction($tx);
                    $oportunidades->status = 'LOGRADA';
                    if ($oportunidades->update()) {
                        $logs = new CrmLogs();
                        $logs->setTransaction($tx);
                        $logs->oportunidad_id = $oportunidades->id;
                        $logs->tipo = 'LOGRO';
                        $logs->descripcion = 'La oportunidad se ha marcado como LOGRADA y pasó a ser cliente ';
                        if ($logs->create()) {
                            if ($cliente) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'La oportunidad se marcará como lograda y puede editar el cliente filtrando por código: ' . $cliente->codigo . ', nombre corto: ' . $cliente->nombre_corto];
                                $tx->commit();
                            } else {
                                $cliente = new VntClientes();
                                $cliente->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $cliente->account_id = $validUser->account_id;
                                $cliente->nombre_corto = $oportunidades->nombre;
                                $codigo = substr(''.$oportunidades->nombre, 0, 3);
                                $cliente->codigo = $codigo;
                                if ($cliente->create()) {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'La oportunidad se marcará como lograda, el cliente ha sido creado y puede editarlo filtrando por código: ' . $cliente->codigo . ', nombre corto: ' . $cliente->nombre_corto];
                                    $tx->commit();
                                } else {
                                    $this->content['result'] = 'error';
                                    $this->content['error'] = Helpers::getErrors($cliente);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el cliente.'];
                                    $tx->rollback();
                                }
                            }
                        } else {
                            $this->content['result'] = 'error';
                            $this->content['error'] = Helpers::getErrors($logs);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el cliente.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['result'] = 'error';
                        $this->content['error'] = Helpers::getErrors($oportunidades);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo marcar la oportunidad como lograda.'];
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

    public function previousStep () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $validUser = Auth::getUserData($this->config);
            $id = $validUser->user_id;

            $carril = intval($request['carril_id']);
            $etapa = intval($request['etapa_id']);
            $paso = Etapas::findFirst($etapa)->nivel;
            $sql_niveles = "SELECT * from crm_etapas where carril_id = $carril and nivel < $paso order by nivel desc limit 1";
            $data_niveles = $this->db->query($sql_niveles)->fetchAll();
            if ($data_niveles) {
                $nuevo_nivel = $data_niveles[0]['id'];
                $sql_actividades = "SELECT * from crm_actividades where etapa_id =".$data_niveles[0]['id'];
                $data_actividades = $this->db->query($sql_actividades)->fetchAll();
                if ($data_actividades) {
                    $oportunidades = Oportunidades::findFirst($request['id']);
                    if ($oportunidades) {
                        $oportunidades->setTransaction($tx);
                        $oportunidades->etapa_id = $nuevo_nivel;
                        if ($oportunidades->update()) {
                            $logs = new CrmLogs();
                            $logs->setTransaction($tx);
                            $logs->oportunidad_id = $oportunidades->id;
                            $logs->tipo = 'RETROCESO';
                            $logs->descripcion = 'La oportunidad retrocedió una etapa por el siguiente motivo: '.trim($request['descripcion']);
                            $logs->etapa_id = $data_niveles[0]['id'];
                            if ($logs->create()) {
                                //
                                $sql_tareas = "SELECT t.oportunidad_id, t.actividad_id from crm_tareas as t inner join crm_actividades as a on t.actividad_id = a.id and a.etapa_id = $nuevo_nivel and t.oportunidad_id =".$oportunidades->id;
                                $data_tareas = $this->db->query($sql_tareas)->fetchAll();
                                if ($data_tareas) {
                                    if (count($data_tareas) == count($data_actividades)) {
                                        $this->content['result'] = 'success';
                                    } else {
                                        foreach ($data_actividades as $act) {
                                            $existe_tarea = false;
                                            foreach ($data_tareas as $tar) {
                                                if ($act['id'] == $tar['actividad_id']) {
                                                    $existe_tarea = true;
                                                    break;
                                                }
                                            }
                                            if (!$existe_tarea) {
                                                $tarea = new Tareas();
                                                $tarea->setTransaction($tx);
                                                $tarea->status = 'PENDIENTE';
                                                $tarea->asignado = $id;
                                                $tarea->completado = false;
                                                $tarea->oportunidad_id = $oportunidades->id;
                                                $tarea->actividad_id = $act['id'];
                                                if ($tarea->create()) {
                                                    $this->content['result'] = 'success';
                                                } else {
                                                    $this->content['result'] = 'error';
                                                    $this->content['error'] = Helpers::getErrors($tarea);
                                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                                    $tx->rollback();
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    foreach ($data_actividades as $actividad) {
                                        $tarea = new Tareas();
                                        $tarea->setTransaction($tx);
                                        $tarea->status = 'PENDIENTE';
                                        $tarea->asignado = $id;
                                        $tarea->completado = false;
                                        $tarea->oportunidad_id = $oportunidades->id;
                                        $tarea->actividad_id = $actividad['id'];
                                        if ($tarea->create()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['result'] = 'error';
                                            $this->content['error'] = Helpers::getErrors($tarea);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la oportunidad.'];
                                            $tx->rollback();
                                        }
                                    }
                                }
                            } else {
                                $this->content['result'] = 'error';
                                $this->content['error'] = Helpers::getErrors($logs);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se actualizará la oportunidad.'];
                                $tx->rollback();
                            }
                            if ($this->content['result'] === 'success') {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'La oportunidad ha regresado a la etapa de ' . $data_niveles[0]['nombre']];
                                $tx->commit();
                            }
                        } else {
                            $this->content['result'] = 'error';
                            $this->content['error'] = Helpers::getErrors($oportunidades);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se actualizará la oportunidad.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No hay tareas en el siguiente paso, no se actualizará la oportunidad'];
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No hay tareas en el paso, no se actualizará la oportunidad'];
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function noLograda ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $oportunidades = Oportunidades::findFirst($request['id']);
            if ($oportunidades) {
                $oportunidades->setTransaction($tx);
                $oportunidades->status = 'NO LOGRADA';
                if ($oportunidades->update()) {
                    $logs = new CrmLogs();
                    $logs->setTransaction($tx);
                    $logs->oportunidad_id = $oportunidades->id;
                    $logs->tipo = 'NO LOGRADA';
                    $logs->descripcion = 'La oportunidad se ha marcado como no lograda por el siguiente motivo: '.trim($request['descripcion']);
                    if ($logs->create()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'La oportunidad ha sido marcada como no lograda'];
                        $tx->commit();
                    } else {
                        $this->content['result'] = 'error';
                        $this->content['error'] = Helpers::getErrors($logs);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se actualizará la oportunidad.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($oportunidades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la oportunidad'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $oportunidades = Oportunidades::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($oportunidades) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una oportunidad con este nombre'];
            } else {
                $oportunidades = Oportunidades::findFirst($request['id']);
                if ($oportunidades) {
                    $oportunidades->setTransaction($tx);
                    $oportunidades->prospecto_id = intval($request['prospecto_id']);
                    $oportunidades->nombre = trim($request['nombre']);
                    $oportunidades->valor = floatval($request['monto']);
                    $oportunidades->valor_final = floatval($request['monto_final']);
                    $ejecutivo_id = $oportunidades->ejecutivo_id;
                    $oportunidades->ejecutivo_id = intval($request['ejecutivo_id']);
                    $oportunidades->sector_id = intval($request['sector_id']);
                    $oportunidades->subsector_id = intval($request['subsector_id']);
                    $oportunidades->tipo = $request['tipo'];
                    $oportunidades->empresa_id = intval($request['empresa_id']);
                    $oportunidades->orden = $request['orden'];
                    $oportunidades->ente = $request['ente'];
                    $oportunidades->adjudicacion = $request['adjudicacion'];
                    $oportunidades->estado_id = intval($request['estado_id']);
                    $oportunidades->municipio_id = intval($request['municipio_id']);
                    $oportunidades->folio = trim($request['folio']);
                    $oportunidades->procedimiento = trim($request['procedimiento']);
                    $oportunidades->monto_licitacion = floatval($request['monto_licitacion']);
                    $oportunidades->area = $request['area'];
                    if ($oportunidades->update()) {
                        if ($ejecutivo_id !== intval($request['ejecutivo_id'])) {
                            $logs = new CrmLogs();
                            $logs->setTransaction($tx);
                            $logs->oportunidad_id = $oportunidades->id;
                            $logs->tipo = 'REASIGNACIÓN';
                            $logs->descripcion = 'La oportunidad fue reasignada al usuario: ';
                            $logs->user_id = intval($request['ejecutivo_id']);
                            if ($logs->create()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'La oportunidad ha sido actualizada'];
                                $tx->commit();
                            } else {
                                $this->content['result'] = 'error';
                                $this->content['error'] = Helpers::getErrors($logs);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se actualizará la oportunidad.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if ($this->content['result'] === 'success') {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la oportunidad'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($oportunidades);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la oportunidad'];
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

    public function updateEjecutivo ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $oportunidades = Oportunidades::findFirst($request['id']);
            if ($oportunidades) {
                $oportunidades->setTransaction($tx);
                $oportunidades->ejecutivo_id = $request['usuario_id'];
                if ($oportunidades->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la oportunidad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($oportunidades);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la oportunidad'];
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
                    $oportunidades = Oportunidades::findFirst($request['id']);
                    $oportunidades->setTransaction($tx);
                    if ($oportunidades->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($oportunidades);
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