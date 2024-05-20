<?php

use Phalcon\Mvc\Controller;

class DashboardController extends Controller
{
    public $content = ['result' => 'error', 'message' => ''];

    public function getDataLicitaciones ()
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $cuenta = $validUser->account_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;

        $sql = "SELECT status, count(*) as cantidad from lic_licitacion group by status order by status asc";
        $licitaciones = $this->db->query($sql)->fetchAll();
        $labels = ['CREADA', 'EN PROCESO', 'PRESENTADA', 'ADJUDICADA', 'NO ADJUDICADA', 'CANCELADA'];
        $series = [0,0,0,0,0,0];
        for ($i=0; $i<sizeof($labels); $i++) {
            foreach ($licitaciones as $elemento) {
                if ($elemento['status'] == $labels[$i]) {
                    $labels[$i] = $labels[$i] . ': ' . $elemento['cantidad'];
                    $series[$i] = $elemento['cantidad'];
                }
            }
        }

        if(strtoupper($role)===strtoupper('root')){
            $sql_solicitudes_colaborador = "SELECT count(*)
            FROM fin_solicitudes 
            where fin_solicitudes.status = 'CREADO'";

            $sql_solicitudes_solicitante = "SELECT count(*)
            FROM fin_solicitudes 
            where fin_solicitudes.status = 'CREADO'";
            
            $sql_solicitudes_pagos = "SELECT count(*)
            FROM fin_solicitudes 
            where (fin_solicitudes.status = 'POR PAGAR' or fin_solicitudes.status = 'PAGO PARCIAL')";
            
            $sql_solicitudes_autorizador = "SELECT count(*)
            FROM fin_solicitudes 
            where fin_solicitudes.status = 'POR AUTORIZAR'";
        } else {
            $sql_solicitudes_colaborador = "SELECT count(*)
            FROM fin_solicitudes 
            inner join pmo_colaboradores on fin_solicitudes.proyecto_id = pmo_colaboradores.proyecto_id
            and pmo_colaboradores.usuario_id = $id and fin_solicitudes.status = 'CREADO'";

            $sql_solicitudes_solicitante = "SELECT count(*)
            FROM fin_solicitudes 
            inner join pmo_solicitantes on fin_solicitudes.proyecto_id = pmo_solicitantes.proyecto_id
            and pmo_solicitantes.usuario_id = $id and fin_solicitudes.status = 'CREADO'";
            
            $sql_solicitudes_pagos = "SELECT count(*)
            FROM fin_solicitudes 
            inner join pmo_responsable_pagos on fin_solicitudes.proyecto_id = pmo_responsable_pagos.proyecto_id
            and pmo_responsable_pagos.usuario_id = $id and (fin_solicitudes.status = 'POR PAGAR' or fin_solicitudes.status = 'PAGO PARCIAL')";
            
            $sql_solicitudes_autorizador = "SELECT count(*)
            FROM fin_solicitudes 
            inner join pmo_autorizadores on fin_solicitudes.proyecto_id = pmo_autorizadores.proyecto_id
            and pmo_autorizadores.usuario_id = $id and fin_solicitudes.status = 'POR AUTORIZAR' 
            and fin_solicitudes.autorizacion_id=pmo_autorizadores.orden";
        }

        $solicitudes_solicitante = $this->db->query($sql_solicitudes_solicitante)->fetchAll();
        $solicitudes_colaborador = $this->db->query($sql_solicitudes_colaborador)->fetchAll();
        $solicitudes_autorizador = $this->db->query($sql_solicitudes_autorizador)->fetchAll();
        $solicitudes_pagos = $this->db->query($sql_solicitudes_pagos)->fetchAll();
        $labels2 = ['CREADA', 'POR SOLICITAR', 'POR AUTORIZAR', 'POR PAGAR'];
        $series2 = [0,0,0,0];

        if (sizeof($solicitudes_colaborador) > 0) {
            $labels2[0] = $labels2[0] . ': ' . $solicitudes_colaborador[0]['count'];
            $series2[0] = $solicitudes_colaborador[0]['count'];
        }
        
        if (sizeof($solicitudes_solicitante) > 0) {
            $labels2[1] = $labels2[1] . ': ' . $solicitudes_solicitante[0]['count'];
            $series2[1] = $solicitudes_solicitante[0]['count'];
        }

        if (sizeof($solicitudes_autorizador) > 0) {
            $labels2[2] = $labels2[2] . ': ' . $solicitudes_autorizador[0]['count'];
            $series2[2] = $solicitudes_autorizador[0]['count'];
        }

        if (sizeof($solicitudes_pagos) > 0) {
            $labels2[3] = $labels2[3] . ': ' . $solicitudes_pagos[0]['count'];
            $series2[3] = $solicitudes_pagos[0]['count'];
        }

        $this->content['labels_licitaciones'] = $labels;
        $this->content['series_licitaciones'] = $series;
        $this->content['labels_solicitudes'] = $labels2;
        $this->content['series_solicitudes'] = $series2;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getDataProjects ($id, $rol, $lider_id, $empresa_id, $estado_id, $municipio_id, $year, $tipo, $finalizado, $activo) {
        $user_id = intval($id);
        $fecha_hoy = date('Y-m-d');
        $sqlTipo = '';
        $and = "";
        $and_dos = "";
        // $fecha_hoy = '2020/06/07';
        if ($finalizado == 'true') {
            $and = " and pmo_proyecto.finalizado = true";
        } 
        if ($finalizado == 'false') {
            $and = " and pmo_proyecto.finalizado = false";
        }
        if ($activo == 'ACTIVO') {
            $and = $and . " and pmo_proyecto.status = 'ACTIVO'";
        } 
        if ($activo == 'BLOQUEADO') {
            $and = $and . " and pmo_proyecto.status = 'BLOQUEADO'";
        }
        if(intval($municipio_id) !== 0) {
            $id_municipio = intval($municipio_id);
            $municipio = " and vnt_clientes.municipio_id <> null and vnt_municipio.id = $id_municipio";
        } else {
            $municipio = "";
        }
        if(intval($estado_id) !== 0) {
            $id_estado = intval($estado_id);
            $estado = " and vnt_estado.id = $id_estado";
        } else {
            $estado = "";
        }
        if(intval($empresa_id) !== 0) {
            $id_empresa = intval($empresa_id);
            $empresa = " and pmo_proyecto.empresa_id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }
        if ($tipo == 'ADMINISTRATIVOS') {
            $sqlTipo = " inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
                        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id and vnt_programa.id = 12";
        } if ($tipo == 'OPERATIVOS') {
            $sqlTipo = " inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
                        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id and vnt_programa.id != 12";
        }

        if (intval($user_id) === 1 || strtoupper($rol) === strtoupper('Admin') || strtoupper($rol) === strtoupper('Root') || strtoupper($rol) === strtoupper('gerente') || strtoupper($rol) === strtoupper('operación') || strtoupper($rol) === strtoupper('planeación')) {

            $not = "";
            if (strtoupper($rol) === strtoupper('planeación')) {
                $not = " and pmo_proyecto.nombre not like '%COMISIONES%'";
            }
            $sql_projects = "SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma
                from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, fin_solicitudes.status from fin_solicitudes, fin_gastos where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) -- + comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) -- + comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, fin_solicitudes.status from fin_solicitudes, fin_gastos where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto 
                inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.year = '$year' $and {$not} $sqlTipo
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto ".$lider."
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio." order by sys_users.nickname, vnt_recurso.codigo, pmo_proyecto.nombre";

        } else {
            $sql_projects = "SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total 
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id and pmo_proyecto.year = '$year'
                inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id and pmo_colaboradores.usuario_id = $user_id
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." $and
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio."

                union

                SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id and pmo_proyecto.year = '$year'
                inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id and pmo_autorizadores.usuario_id = $user_id
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." $and
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio."

                union

                SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id and pmo_proyecto.year = '$year'
                inner join pmo_responsable_pagos on pmo_proyecto.id = pmo_responsable_pagos.proyecto_id and pmo_responsable_pagos.usuario_id = $user_id
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." $and
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio."

                union 

                SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname, pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16)) --+ comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total) --+ comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, 
                sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, 
                fin_solicitudes.status from fin_solicitudes, fin_gastos 
                where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id and pmo_proyecto.year = '$year' 
                and pmo_proyecto.lider_proyecto = $user_id
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." $and
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio." order by nickname, codigo, nombre";
        }

        /* if ($user_id === 36) {
            $sql_projects = "SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$fecha_hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$fecha_hoy'),
                (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1),
                (select sum(costo) as costo_real from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select coalesce(sum(cambios.suma), 0) as solicitado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16) + comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total + comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma
                from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, fin_solicitudes.status from fin_solicitudes, fin_gastos where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and (status = 'PAGADO' or status = 'POR PAGAR' or status = 'POR AUTORIZAR' or status = 'PAGO PARCIAL')) as cambios),
                (select coalesce(sum(cambios.suma), 0) as pagado from
                (select case 
                when iva = true and subempresa_id is not null then (total + (total * .16) + comision_calculada)
                when iva = true and subempresa_id is null then (total + (total * .16))
                when iva = false and subempresa_id is not null then (total + comision_calculada)
                when iva = false and subempresa_id is null then total
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, fin_solicitudes.status from fin_solicitudes, fin_gastos where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios)
                from pmo_proyecto 
                inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.year = '$year'
                join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
                join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id and vnt_programa.id <> 12 and vnt_programa.id <> 9 and vnt_programa.id <> 5 and vnt_programa.id <> 13
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider."
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado.$empresa."
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio." order by sys_users.nickname, vnt_recurso.codigo, pmo_proyecto.nombre";
        } */
        // print_r($sql_projects);

        $projects = $this->db->query($sql_projects)->fetchAll();
        $nuevo = [];
        $colores = ['text-white bg-cyan', 'text-white bg-teal', 'text-white bg-green', 'text-white bg-orange-10', 'text-white bg-brown', 'text-white bg-black', 'text-white bg-red-10', 'text-white bg-pink-10'];
        $contador = 0;
        if (count($projects) > 0) {
            $lider_actual = $projects[0]['lider_proyecto'];
            foreach ($projects as $project){
                    $pro=(object)array();
                    if ($lider_actual != $project['lider_proyecto']){
                        $contador++;
                        $lider_actual = $project['lider_proyecto'];
                    }
                    if ($contador == 8) {
                        $contador = 0;
                    }
                    $pro->color = $colores[$contador];
                    $pro->id = $project['id'];
                    $pro->semanas = round(($project['dias']/5),0);
                    $pro->nickname = $project['nickname'];
                    $pro->lider_proyecto = $project['lider_proyecto'];
                    $proyecto_id = $project['id'];
                    $pro->codigo = $project['codigo'];
                    $pro->nombre = $project['nombre'];
                    $pro->fecha_limite = $project['fecha_limite'];
                    $sql_colaborador = "SELECT pmo_proyecto.id,pmo_proyecto.nombre from pmo_proyecto inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id
                        and pmo_colaboradores.usuario_id = $user_id and pmo_proyecto.id = $proyecto_id";
                    $es_colaborador = $this->db->query($sql_colaborador)->fetchAll();
                    if (count($es_colaborador) > 0) {
                        $pro->colaborador = true;
                    } else {
                        $pro->colaborador = false;
                    }
                    $pro->costo_estimado = number_format(floatval($project['costo_estimado']),2,'.',',');
                    $pro->costo_real = number_format(floatval($project['costo_real']),2,'.',',');
                    $pro->presupuesto = number_format(floatval($project['presupuesto']),2,'.',',');
                    if (intval($project['actividades']) > 0 && intval($project['actividades_vencidas'])) {
                        $pro->avance_estimado = round((intval($project['actividades_vencidas']) * 100) / intval($project['actividades']),0);
                        $pro->avance_real = round((intval($project['avance_real']) * 100) / (intval($project['actividades_level_1']) * 100), 0);
                        $pro->semanas_estimadas = round(((($project['dias'] * $pro->avance_estimado) / 100) / 5), 0);
                        $pro->semanas_reales = round(((($project['dias'] * $pro->avance_real) / 100) / 5), 0);
                    } else {
                        $pro->avance_estimado = 0;
                        $pro->avance_real = 0;
                        $pro->semanas_estimadas = 0;
                        $pro->semanas_reales = 0;
                    }
                    if ($project['solicitado'] > 0) {
                        $pro->solicitado = round((($project['solicitado'] * 100) / $project['presupuesto']), 2);
                    } else {
                        $pro->solicitado = 0;
                        $pro->porcentaje_estimado = 0;
                    }
                    $pro->costo_solicitado = number_format(floatval($project['solicitado']),2,'.',',');
                    if ($project['costo_estimado'] > 0) {
                        $pro->porcentaje_costo_estimado = round((($project['costo_estimado'] * 100) / $project['presupuesto']), 0);
                    } else {
                        $pro->porcentaje_costo_estimado = 0;
                    }
                    $pro->costo_pagado = number_format(floatval($project['pagado']),2,'.',',');
                    if ($project['pagado'] > 0) {
                        $pro->pagado = round((($project['pagado'] * 100) / $project['presupuesto']), 2);
                    } else {
                        $pro->pagado = 0;
                    }
                array_push($nuevo,$pro);
            }
        }

        $this->content['projects'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getPresupuestoByProyecto ($id_proyecto) {
        $sql = "SELECT pmo_proyecto.id, pmo_proyecto.presupuesto, (pmo_proyecto.presupuesto - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id 
            inner join pmo_proyecto on fin_solicitudes.proyecto_id = pmo_proyecto.id and fin_solicitudes.proyecto_id = $id_proyecto
            and fin_solicitudes.iva = true and (fin_solicitudes.status = 'POR AUTORIZAR' or fin_solicitudes.status = 'POR PAGAR' or 
            fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma
            FROM fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id 
            inner join pmo_proyecto on fin_solicitudes.proyecto_id = pmo_proyecto.id and fin_solicitudes.proyecto_id = $id_proyecto
            and fin_solicitudes.iva = false and (fin_solicitudes.status = 'POR AUTORIZAR' or fin_solicitudes.status = 'POR PAGAR' or 
            fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as disponible
            from pmo_proyecto where pmo_proyecto.id = $id_proyecto";
        $presupuesto = $this->db->query($sql)->fetchAll();
        $this->content['labels_proyecto'] = ['Cantidad disponible: $'.number_format(floatval($presupuesto[0]['disponible']),2,'.',','), 'Cantidad comprometida: $'.number_format(floatval($presupuesto[0]['presupuesto'] - $presupuesto[0]['disponible']),2,'.',',')];
        $this->content['series_proyecto'] = [floatval($presupuesto[0]['disponible']), $presupuesto[0]['presupuesto'] - $presupuesto[0]['disponible']];
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();

    }

    public function getPresupuestoByActividad ($id_actividad) {
        $sql = "SELECT pmo_actividades.costo as presupuesto, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
            FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = true and 
            (fin_solicitudes.status = 'POR AUTORIZAR' or fin_solicitudes.status = 'POR PAGAR' or 
            fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + 
            (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and 
            fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = false and (fin_solicitudes.status = 'POR AUTORIZAR' or fin_solicitudes.status = 'POR PAGAR' or 
            fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as disponible from pmo_actividades where id = $id_actividad";
        $presupuesto = $this->db->query($sql)->fetchAll();
        $this->content['labels_actividad'] = ['Cantidad disponible: $'.number_format(floatval($presupuesto[0]['disponible']),2,'.',','), 'Cantidad comprometida: $'.number_format(floatval($presupuesto[0]['presupuesto'] - $presupuesto[0]['disponible']),2,'.',',')];
        $this->content['series_actividad'] = [floatval($presupuesto[0]['disponible']), $presupuesto[0]['presupuesto'] - $presupuesto[0]['disponible']];
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();

    }

    public function getEventos () {
        $fecha_actual = date("Y-m-d H:i:s");
        $fecha_act = date("Y-m-d");
        $fecha_siguiente = strtotime ( '30 days' , strtotime ($fecha_actual));
        $fecha_siguiente = date ( 'Y-m-j H:i:s' , $fecha_siguiente);
        $fecha_atras = strtotime ( '-30 days' , strtotime ($fecha_actual));
        $fecha_atras = date ( 'Y-m-j H:i:s' , $fecha_atras);
        $sql = "SELECT lic_eventos.id, lic_eventos.evento, lic_licitacion.titulo, 
                TO_CHAR(lic_eventos.fecha_evento, 'dd/mm/yyyy HH:MI:SS') AS fecha_evento, lic_eventos.documento_id, lic_eventos.tipo, sys_users.nickname
                FROM lic_eventos
                inner join lic_licitacion on lic_eventos.licitacion_id = lic_licitacion.id
                inner join sys_users on sys_users.id = lic_eventos.created_by 
                and fecha_evento between '$fecha_atras' and '$fecha_siguiente' order by fecha_evento";
        $eventos = $this->db->query($sql)->fetchAll();
        $array_eventos = [];
        foreach ($eventos as $evt) {
            $array=(object)array();
            $array->id = $evt['id'];
            $array->evento = $evt['evento'];
            $array->titulo = $evt['titulo'];
            $array->fecha_evento = $evt['fecha_evento'];
            $array->nickname = $evt['nickname'];
            $pos = strpos($evt['fecha_evento'], $fecha_act);
            if($pos !== false) {
                $array->icon = 'alarm';
                $array->color = 'red-14';
            } else {
                $array->icon = 'event';
                $array->color = 'teal-10';
            }
            array_push($array_eventos,$array);
        }
        $this->content['eventos'] = $array_eventos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasByDays ($days, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        if ($days == 'all') {
        } else {
            if ($days == '30') {
                $month = date("Y-m-d",strtotime($hoy."- 30 days"));
            }
            if ($days == '60') {
                $hoy = date("Y-m-d",strtotime($hoy."- 30 days"));
                $month = date("Y-m-d",strtotime($hoy."- 60 days"));
            }
            if ($days == '90') {
                $hoy = date("Y-m-d",strtotime($hoy."- 60 days"));
                $month = date("Y-m-d",strtotime($hoy."- 90 days"));
            }
            $where = " and fecha_vencimiento > '$month'";
        }
        $sqlAbonado = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false where fecha_vencimiento < '$hoy' {$where} and status = 'ABONADO'";
        $sqlAbonadoCredito = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year' 
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and (sys_documents.name like '%AMORTIZA%' or vnt_contratos_facturas.cancelada = true) where fecha_vencimiento < '$hoy' {$where} and status = 'ABONADO'";
        $sqlEmitido = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas 
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false
            where fecha_vencimiento < '$hoy' {$where} and status = 'EMITIDO'";
        $sqlEmitidoCredito = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year' 
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and (sys_documents.name like '%AMORTIZA%' or vnt_contratos_facturas.cancelada = true)
            where fecha_vencimiento < '$hoy' {$where} and status = 'EMITIDO'";
        $sqlPagado = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas 
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false
            where fecha_vencimiento < '$hoy' {$where} and status = 'PAGADO'";
        $sqlPagadoCredito = "SELECT count(*), coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and (sys_documents.name like '%AMORTIZA%' or vnt_contratos_facturas.cancelada = true)
            where fecha_vencimiento < '$hoy' {$where} and status = 'PAGADO'";
        $sqlTotal = "SELECT count(*) FROM vnt_contratos_facturas";
        $total = $this->db->query($sqlTotal)->fetchAll();
        $abonadas = $this->db->query($sqlAbonado)->fetchAll();
        $emitidas = $this->db->query($sqlEmitido)->fetchAll();
        $pagadas = $this->db->query($sqlPagado)->fetchAll();
        $abonado_credito = $this->db->query($sqlAbonadoCredito)->fetchAll();
        $emitido_credito = $this->db->query($sqlEmitidoCredito)->fetchAll();
        $pagado_credito = $this->db->query($sqlPagadoCredito)->fetchAll();

        $array_facturas = [];
        $array_facturas['total_facturas'] = $total[0]['count'];
        $array_facturas['total_facturas_vencidas'] = $emitidas[0]['count'] + $abonadas[0]['count'] + $pagadas[0]['count'];
        $array_facturas['emitido'] = $emitidas[0]['count'] - $emitido_credito[0]['count'];
        $array_facturas['abonado'] = $abonadas[0]['count'] - $abonado_credito[0]['count'];
        $array_facturas['pagado'] = $pagadas[0]['count'] - $pagado_credito[0]['count'];
        $array_facturas['monto_emitido'] = number_format(floatval($emitidas[0]['monto_factura'] - $emitido_credito[0]['monto_factura']),2,'.',',');
        $array_facturas['monto_abonado'] = number_format(floatval($abonadas[0]['monto_factura'] - $abonado_credito[0]['monto_factura']),2,'.',',');
        $array_facturas['monto_pagado'] = number_format(floatval($pagadas[0]['monto_factura'] - $pagado_credito[0]['monto_factura']),2,'.',',');
        
        $this->content['facturas_vencidas'] = $array_facturas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturas ($year) {
        $hoy = date("Y-m-d");
        $sqlVencido = "SELECT coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false 
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year' where fecha_vencimiento < '$hoy'";
        $sqlCobrado = "SELECT coalesce(sum(monto),0) as sum from (
            SELECT sum(monto) as monto
            FROM vnt_facturas_abonos 
            inner join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id 
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'

            union 

            select sum(monto) as monto
            from vnt_remisiones_abonos
            inner join vnt_remisiones on vnt_remisiones_abonos.factura_id = vnt_remisiones.id
            inner join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones.id
            and date_part('year', vnt_remisiones.fecha_venta) = '$year' and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false) as x";
        $sqlTotal = "SELECT sum(num) as count, sum(monto_factura) from (
            SELECT count(*) as num, coalesce(sum(monto_factura),0) as monto_factura
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'

            union all 
            
            select count(*) as num,coalesce(sum(monto_factura),0) as monto_factura
            from vnt_remisiones_facturas
            inner join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false
            where date_part('year', vnt_remisiones.fecha_venta) = '$year') as x";
        $sqlAbonadas = "SELECT count(*), sum(monto_factura) 
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year' WHERE status = 'ABONADO'";
        $sqlEmitidas = "SELECT sum(num) as count, sum(monto_factura) from (
            SELECT count(*) as num, coalesce(sum(monto_factura),0) as monto_factura
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'

            union all
            
            select count(*) as num,coalesce(sum(monto_factura),0) as monto_factura
            from vnt_remisiones_facturas
            inner join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false
            where date_part('year', vnt_remisiones.fecha_venta) = '$year') as x";
        $sqlCredito = "SELECT count(*), sum(monto_factura) 
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and (sys_documents.name like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false)
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'";
        $sqlPagadas = "SELECT sum(num) as count, sum(monto_factura) from (
            SELECT count(*) as num, coalesce(sum(monto_factura),0) as monto_factura 
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and 
            vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year' and status = 'PAGADO'

            union all 

            select count(*) as num, coalesce(sum(monto_factura),0) as monto_factura
            from vnt_remisiones_facturas
            inner join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            where date_part('year', vnt_remisiones.fecha_venta) = '$year' and vnt_remisiones.status_cobranza = 'PAGADO') as x";
    
        $total = $this->db->query($sqlTotal)->fetchAll();
        $vencido = $this->db->query($sqlVencido)->fetchAll();
        $cobrado = $this->db->query($sqlCobrado)->fetchAll();
        $abonadas = $this->db->query($sqlAbonadas)->fetchAll();
        $emitidas = $this->db->query($sqlEmitidas)->fetchAll();
        $credito = $this->db->query($sqlCredito)->fetchAll();
        $pagadas = $this->db->query($sqlPagadas)->fetchAll();

        $credito_retail = $this->notasRETAIL(0, $year);

        $array_facturas = [];
        $array_facturas['total_facturas'] = $total[0]['count'] - $credito[0]['count'];
        $total_facturas_nc = $total[0]['sum'] - ($credito[0]['sum'] + $credito_retail[0]['notas_retail']);
        $total_facturas = $total[0]['sum'] - $credito[0]['sum'];
        $array_facturas['total_monto_facturas'] = number_format(floatval($total_facturas),2,'.',',');
        $array_facturas['total_vencido'] = number_format(floatval($vencido[0]['monto_factura'] - $credito[0]['sum']),2,'.',',');
        $por_cobrar = $cobrado[0]['sum'] - $credito[0]['sum'];
        $array_facturas['total_cobrado'] = number_format(floatval($por_cobrar),2,'.',',');
        $array_facturas['por_cobrar'] = number_format(floatval($total_facturas_nc - $por_cobrar),2,'.',',');
        $array_facturas['abonadas'] = $abonadas[0]['count'];
        $array_facturas['emitidas'] = $emitidas[0]['count'] - ($credito[0]['count'] + $credito_retail[0]['num']);
        $array_facturas['pagadas'] = $pagadas[0]['count'] - $credito[0]['count'];
        $array_facturas['monto_abonadas'] = number_format(floatval($abonadas[0]['sum']),2,'.',',');
        // $array_facturas['monto_emitidas'] = number_format(floatval($emitidas[0]['sum'] - ($credito[0]['sum'] + $credito_retail[0]['notas_retail'])),2,'.',',');
        $array_facturas['monto_emitidas'] = number_format(floatval($emitidas[0]['sum'] - $credito[0]['sum']),2,'.',',');
        // $array_facturas['monto_pagadas'] = number_format(floatval($pagadas[0]['sum'] - $credito[0]['sum']),2,'.',',');

        // $total_monto_facturas = $total[0]['sum'];
        $total_monto_facturas = $total_facturas;
        $array_facturas['monto_pagadas'] = number_format(floatval($por_cobrar),2,'.',',');
        $meta = $vencido[0]['monto_factura'];
        $cobrado = $cobrado[0]['sum'];
        $por_cobrar_m = floatval($total[0]['sum'] - $por_cobrar);

        if ($meta > 0) {
            $array_facturas['porcentaje_meta'] = round((($meta * 100) / $total_monto_facturas),2);
        } else {
            $array_facturas['porcentaje_meta'] = 0;
        }
        /* if ($cobrado > 0) {
            $array_facturas['porcentaje_cobrado'] = round((($cobrado * 100) / $total_monto_facturas),2);
        } else {
            $array_facturas['porcentaje_cobrado'] = 0;
        } */
        if ($por_cobrar > 0) {
            $array_facturas['porcentaje_cobrado'] = round((($por_cobrar * 100) / $total_monto_facturas),2);
        } else {
            $array_facturas['porcentaje_cobrado'] = 0;
        }
        if ($por_cobrar > 0) {
            $array_facturas['porcentaje_por_cobrar'] = round(((($total_facturas_nc - $por_cobrar) * 100) / $total_monto_facturas),2);
        } else {
            $array_facturas['porcentaje_por_cobrar'] = 0;
        }
        $array_facturas['ndc'] = number_format(floatval($credito_retail[0]['notas_retail']),2,'.',',');
        if ($credito_retail[0]['notas_retail'] > 0) {
            $array_facturas['porcentaje_ndc'] = round((($credito_retail[0]['notas_retail'] * 100) / $total_monto_facturas),2);
        } else {
            $array_facturas['porcentaje_ndc'] = 0;
        }
        
        $this->content['facturas_vencidas'] = $array_facturas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getGraficaDataFactura ($year) {
        $sql = "SELECT s2.mes,COALESCE(s1.abonos,0),coalesce(s1.monto_abonado, 0) as abonos from 
            (select date_part('month',DATE '2008-01-01' + (interval '1' month * generate_series(0,11)))as mes) as s2
            left join (select date_part('month',fecha) as mes,count(vnt_facturas_abonos.id) as abonos, sum(vnt_facturas_abonos.monto)
            as monto_abonado
            from vnt_facturas_abonos
            where date_part('year',fecha) = '$year'
            group by mes) as s1 on s2.mes=s1.mes";

        $sql_retail = "SELECT s2.mes,COALESCE(s1.abonos,0),coalesce(s1.monto_abonado, 0) as abonos from 
            (select date_part('month',DATE '2008-01-01' + (interval '1' month * generate_series(0,11)))as mes) as s2
            left join (select date_part('month',fecha) as mes,count(vnt_remisiones_abonos.id) as abonos, sum(vnt_remisiones_abonos.monto)
            as monto_abonado
            from vnt_remisiones_abonos
            where date_part('year',fecha) = '$year'
            group by mes) as s1 on s2.mes=s1.mes";

        $sql_facturas = "SELECT s2.mes,COALESCE(s1.facturas,0),coalesce(s1.monto_facturado, 0) as facturas from 
            (select date_part('month',DATE '2008-01-01' + (interval '1' month * generate_series(0,11)))as mes) as s2
            left join (select date_part('month',fecha_factura) as mes,count(vnt_contratos_facturas.id) as facturas, sum(vnt_contratos_facturas.monto_factura)
            as monto_facturado
            from vnt_contratos_facturas
            where date_part('year',fecha_factura) = '$year'
            group by mes) as s1 on s2.mes=s1.mes";

        $sql_retail_facturado = "SELECT s2.mes,COALESCE(s1.facturas,0),coalesce(s1.monto_facturado, 0) as facturas from 
            (select date_part('month',DATE '2008-01-01' + (interval '1' month * generate_series(0,11)))as mes) as s2
            left join (select date_part('month',vnt_remisiones_facturas.fecha_factura) as mes,count(vnt_remisiones_facturas.id) as facturas, sum(vnt_remisiones_facturas.monto_factura)
            as monto_facturado
            from vnt_remisiones_facturas
            inner join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            and vnt_remisiones_facturas.amortizacion = false and vnt_remisiones_facturas.cancelado = false
            where date_part('year',vnt_remisiones_facturas.fecha_factura) = '$year'
            group by mes) as s1 on s2.mes=s1.mes";

        $total = $this->db->query($sql)->fetchAll();
        $total_retail = $this->db->query($sql_retail)->fetchAll();
        $total_facturas = $this->db->query($sql_facturas)->fetchAll();
        $total_facturas_retail = $this->db->query($sql_retail_facturado)->fetchAll();
        $data=array();
        for($i = 0; $i < 12; $i++) {
            array_push($data,floatval($total[$i]['abonos'] + $total_retail[$i]['abonos']));
        }
        $data_total=array();
        for($i = 0; $i < 12; $i++) {
            array_push($data_total,floatval($total_facturas[$i]['facturas'] + $total_facturas_retail[$i]['facturas']));
        }
        // return $data;
        $this->content['facturas_recaudadas'] = $data;
        $this->content['facturas_totales'] = $data_total;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAdeudosByCliente ($id_cliente, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        $and = "";
        if (intval($id_cliente) > 0) {
            $where = " where cliente_id = ".intval($id_cliente);
            $and = " and cliente_id = ".intval($id_cliente);
        } else {
            $where = " where cliente_id > 0";
        }
        $and_year = " and year = '$year'";

        $year_anterior = intval($year) - 1;
        $totalAdeudos = $this->AdeudosGDP($id_cliente, $year);
        $totalRetail = $this->AdeudosRETAIL($id_cliente, $year);
        $total_notas_retail = $this->notasRETAIL($id_cliente, $year);
        $total_notas_gdp = $this->notasGDP($id_cliente, $year);

        $totalAdeudos_anterior = $this->AdeudosGDP($id_cliente, $year_anterior);
        $totalRetail_anterior = $this->AdeudosRETAIL($id_cliente, $year_anterior);
        $total_notas_retail_anterior = $this->notasRETAIL($id_cliente, $year_anterior);
        $total_notas_gdp_anterior = $this->notasGDP($id_cliente, $year_anterior);

        $sqlProyectos = "SELECT count(*) from vnt_recurso $where $and_year";
        $totalProyectos = $this->db->query($sqlProyectos)->fetchAll();

        $array_adeudos = [];
        $adeudo_contratos = $totalAdeudos[0]['contratos'] + $totalRetail[0]['facturado'];
        $adeudo_abonado = $totalAdeudos[0]['abonado'] + $totalRetail[0]['facturas_abonadas'];
        // $adeudo_facturado = $totalAdeudos[0]['monto_facturado'] + ($totalRetail[0]['facturado'] - $total_notas_retail[0]['notas_retail']);
        $adeudo_facturado = $totalAdeudos[0]['monto_facturado'] + $totalRetail[0]['facturado'];
        $restante = floatval(($totalAdeudos[0]['monto_facturado'] - $totalAdeudos[0]['abonado']) + ($totalRetail[0]['facturado'] - ($totalRetail[0]['facturas_abonadas'] + $total_notas_retail[0]['notas_retail'] + $total_notas_gdp[0]['notas_gdp'])));

        $adeudo_anterior = $totalAdeudos_anterior[0]['contratos'] + $totalRetail_anterior[0]['facturado'];
        $abonado_anterior = $totalAdeudos_anterior[0]['abonado'] + $totalRetail_anterior[0]['facturas_abonadas'];
        // $facturado_anterior = $totalAdeudos_anterior[0]['monto_facturado'] + ($totalRetail_anterior[0]['facturado'] - $total_notas_retail_anterior[0]['notas_retail']);
        $facturado_anterior = $totalAdeudos_anterior[0]['monto_facturado'] + $totalRetail_anterior[0]['facturado'];
        $restante_anterior = floatval(($totalAdeudos_anterior[0]['monto_facturado'] - $totalAdeudos_anterior[0]['abonado']) + ($totalRetail_anterior[0]['facturado'] - ($totalRetail_anterior[0]['facturas_abonadas'] + $total_notas_retail_anterior[0]['notas_retail'] + $total_notas_gdp_anterior[0]['notas_gdp'])));

        $array_adeudos['contratos'] = number_format(floatval($adeudo_contratos),2,'.',',');
        $array_adeudos['abonado'] = number_format(floatval($adeudo_abonado),2,'.',',');
        $array_adeudos['monto_facturado'] = number_format(floatval($adeudo_facturado),2,'.',',');
        $array_adeudos['monto_descontado'] = number_format(floatval($total_notas_retail[0]['notas_retail'] + $total_notas_gdp[0]['notas_gdp']),2,'.',',');
        $array_adeudos['restante'] = number_format(floatval($restante),2,'.',',');
        $array_adeudos['proyectos'] = $totalProyectos[0]['count'];

        $array_adeudos['contratos_anterior'] = number_format(floatval($adeudo_anterior),2,'.',',');
        $array_adeudos['abonado_anterior'] = number_format(floatval($abonado_anterior),2,'.',',');
        $array_adeudos['monto_facturado_anterior'] = number_format(floatval($facturado_anterior),2,'.',',');
        $array_adeudos['monto_descontado_anterior'] = number_format(floatval($total_notas_retail_anterior[0]['notas_retail'] + $total_notas_gdp_anterior[0]['notas_gdp']),2,'.',',');
        $array_adeudos['restante_anterior'] = number_format(floatval($restante_anterior),2,'.',',');

        $array_adeudos['contratosAll'] = number_format(floatval($adeudo_contratos + $adeudo_anterior),2,'.',',');
        $array_adeudos['abonadoAll'] = number_format(floatval($adeudo_abonado + $abonado_anterior),2,'.',',');
        $array_adeudos['monto_facturadoAll'] = number_format(floatval($adeudo_facturado + $facturado_anterior),2,'.',',');
        $array_adeudos['descontadoAll'] = number_format(floatval($total_notas_retail[0]['notas_retail'] + $total_notas_retail_anterior[0]['notas_retail'] + $total_notas_gdp[0]['notas_gdp'] + $total_notas_gdp_anterior[0]['notas_gdp']),2,'.',',');
        $array_adeudos['restanteAll'] = number_format(floatval($restante + $restante_anterior),2,'.',',');

        $sqlVencidas = "SELECT count(*), coalesce(sum(monto_factura),0)
            FROM vnt_contratos_facturas
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            inner join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id {$and}
            WHERE vnt_contratos_facturas.status != 'PAGADO' and fecha_vencimiento < '$hoy'";
        $totalVencidas = $this->db->query($sqlVencidas)->fetchAll();
        if (count($totalVencidas) > 0) {
            $array_adeudos['facturas_vencidas'] = $totalVencidas[0]['count'];
            $array_adeudos['monto_facturas_vencidas'] = number_format(floatval($totalVencidas[0]['coalesce']),2,'.',',');
        } else {
            $array_adeudos['facturas_vencidas'] = 0;
            $array_adeudos['monto_facturas_vencidas'] = 0;
        }

        $sqlVigentes = "SELECT count(*), coalesce(sum(monto_factura),0)
            FROM vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id
            inner join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id and vnt_clientes.id =".intval($id_cliente)."
            WHERE vnt_contratos_facturas.status != 'PAGADO' and fecha_vencimiento > '".$hoy."'
            group by vnt_contratos_facturas.id";
        $totalVigentes = $this->db->query($sqlVigentes)->fetchAll();
        if (count($totalVigentes) > 0) {
            $array_adeudos['facturas_vigentes'] = $totalVigentes[0]['count'];
            $array_adeudos['monto_facturas_vigentes'] = number_format(floatval($totalVigentes[0]['coalesce']),2,'.',',');
        } else {
            $array_adeudos['facturas_vigentes'] = 0;
            $array_adeudos['monto_facturas_vigentes'] = 0;
        }


        $this->content['adeudos'] = $array_adeudos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function AdeudosGDP ($id_cliente, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        $and = "";
        if (intval($id_cliente) > 0) {
            $where = " where cliente_id = ".intval($id_cliente);
            $and = " and cliente_id = ".intval($id_cliente);
        } else {
            $where = " where cliente_id > 0";
        }
        $and_year = " and year = '$year'";
        $sqlAdeudos = "SELECT coalesce(sum(monto_total),0) as contratos, coalesce(coalesce(sum(monto_total_abonado),0) - coalesce(sum(monto_cancelado_credito),0)) as abonado, coalesce(coalesce(sum(monto_facturado),0) - coalesce(sum(monto_cancelado_credito),0)) as monto_facturado from (SELECT vnt_contrato.recurso_id,vnt_clientes.id as cliente_id,vnt_contrato.monto_total, (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos 
            inner join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id 
            inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like 
            '%AMORTIZA%' and sys_documents.name not like '%NOTA DE CR%' and vnt_contratos_facturas.cancelada = false
            inner join vnt_contrato as x on x.id = vnt_contratos_facturas.contrato_id and x.id = vnt_contrato.id), 0)) as monto_total_abonado,
             (select COALESCE((select 
            sum(monto_factura) from vnt_contratos_facturas inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and sys_documents.name not like '%NOTA DE CR%' and 
            vnt_contratos_facturas.cancelada = false where vnt_contratos_facturas.contrato_id = 
            vnt_contrato.id),0)) as monto_facturado,
            (select COALESCE((select 
            sum(monto_factura) from vnt_contratos_facturas 
            inner join sys_documents on sys_documents.id = 
            vnt_contratos_facturas.document_id and ((sys_documents.name like '%AMORTIZA%' or sys_documents.name like '%NOTA DE CR%') and 
            vnt_contratos_facturas.cancelada = false) where vnt_contratos_facturas.contrato_id = 
            vnt_contrato.id),0)) as monto_cancelado_credito,vnt_recurso.subprograma_id, vnt_recurso.codigo, 
            vnt_clientes.razon_social as cliente from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id
            = vnt_recurso.id and vnt_contrato.year = '$year' join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id ) as y $where";
        return $this->db->query($sqlAdeudos)->fetchAll();
    }

    private function AdeudosRETAIL ($id_cliente, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        $and = "";
        if (intval($id_cliente) > 0) {
            $where = " where cliente_id = ".intval($id_cliente);
            $and = " and cliente_id = ".intval($id_cliente);
        } else {
            $where = " where cliente_id > 0";
        }
        $and_year = " and year = '$year'";
        $sql_retail = "SELECT coalesce(sum(facturado_impact), 0) as facturado, coalesce(sum(facturas_abonadas), 0) as facturas_abonadas FROM (SELECT vnt_remisiones.id, vnt_remisiones_facturas.monto_factura as monto_total,
                vnt_remisiones_facturas.monto_factura as facturado_impact,
                (select coalesce((select sum(vnt_remisiones_abonos.monto) 
                 from vnt_remisiones_abonos
                 inner join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones_abonos.factura_id
                 and vnt_remisiones_facturas.remision_id = vnt_remisiones.id and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false), 0)) as facturas_abonadas, vnt_remisiones_facturas.monto_factura as facturado_total
                from vnt_remisiones
                join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id and vnt_remisiones.tipo = 'HISTÓRICO' 
                and date_part('year', vnt_remisiones.fecha_venta) = '$year'
                join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones.id  {$where}
                and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false) as x";
        return $this->db->query($sql_retail)->fetchAll();
    }

    private function notasRETAIL ($id_cliente, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        $and = "";
        if (intval($id_cliente) > 0) {
            $where = " where cliente_id = ".intval($id_cliente);
            $and = " and cliente_id = ".intval($id_cliente);
        } else {
            $where = " where cliente_id > 0";
        }
        $and_year = " and year = '$year'";
        $sql_retail_nota = "SELECT count(x.id) as num, coalesce(sum(x.monto_factura),0) as notas_retail
                from vnt_remisiones_facturas as x
                inner join vnt_remisiones on vnt_remisiones.id = x.remision_id and  x.amortizacion = true and x.cancelado = false and x.factura_relacionada is not null 
                inner join vnt_remisiones_facturas on x.factura_relacionada = vnt_remisiones_facturas.id 
                inner join vnt_remisiones as y on y.id = vnt_remisiones_facturas.remision_id and date_part('year', y.fecha_venta) = '$year'";
        return $this->db->query($sql_retail_nota)->fetchAll();
    }

    private function notasGDP ($id_cliente, $year) {
        $hoy = date("Y-m-d");
        $where = "";
        $and = "";
        if (intval($id_cliente) > 0) {
            $and = " and vnt_recurso.cliente_id = ".intval($id_cliente);
        }
        $sql_retail_nota = "SELECT count(x.id) as num, coalesce(sum(x.monto_factura),0) as notas_gdp
            from vnt_contratos_facturas as x
            inner join vnt_contrato on vnt_contrato.id = x.contrato_id and x.cancelada = false 
            and x.factura_relacionada is not null 
            inner join vnt_contratos_facturas on x.factura_relacionada = vnt_contratos_facturas.uuid 
            inner join vnt_contrato as y on y.id = vnt_contratos_facturas.contrato_id and y.year = '$year'
            inner join vnt_recurso on vnt_recurso.id = y.recurso_id {$and}";
        return $this->db->query($sql_retail_nota)->fetchAll();
    }

    public function getTopClientes ($year) {
        $sql = "SELECT cliente_id, razon_social, count(*), sum(dias), (sum(dias)/count(*)) as promedio from (SELECT vnt_clientes.id as cliente_id, vnt_clientes.razon_social,vnt_contratos_facturas.fecha_vencimiento,vnt_facturas_abonos.fecha as fecha_pago, (vnt_facturas_abonos.fecha - vnt_contratos_facturas.fecha_vencimiento) as dias FROM public.vnt_facturas_abonos
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
            join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            order by cliente_id) as y group by cliente_id, razon_social order by promedio asc limit 3";
        $mejores = $this->db->query($sql)->fetchAll();

        $sql_peores = "SELECT cliente_id, razon_social, count(*), sum(dias), (sum(dias)/count(*)) as promedio from (SELECT vnt_clientes.id as cliente_id, vnt_clientes.razon_social,vnt_contratos_facturas.fecha_vencimiento,vnt_facturas_abonos.fecha as fecha_pago, (vnt_facturas_abonos.fecha - vnt_contratos_facturas.fecha_vencimiento) as dias FROM public.vnt_facturas_abonos
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
            join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            order by cliente_id) as y group by cliente_id, razon_social order by promedio desc limit 3";
        $peores = $this->db->query($sql_peores)->fetchAll();

        $sql_aliados = "SELECT cliente_id, razon_social, count(*), sum(dias), (sum(dias)/count(*))as promedio, aliado 
            from 
            (SELECT vnt_clientes.id as cliente_id, vnt_clientes.razon_social, 
            vnt_contratos_facturas.fecha_vencimiento, vnt_facturas_abonos.fecha as fecha_pago, 
            (vnt_facturas_abonos.fecha - vnt_contratos_facturas.fecha_vencimiento) as dias,
            com_aliados.apellido_paterno || ' ' || com_aliados.apellido_materno || ' ' || com_aliados.nombre as aliado
            FROM public.vnt_facturas_abonos
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id 
            join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            join com_comisiones on com_comisiones.contrato_id = vnt_contrato.id
            join com_aliados on com_aliados.id = com_comisiones.aliado_id
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id 
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id order by cliente_id) as y 
            group by cliente_id, razon_social, aliado order by promedio asc limit 3";
        $aliados_mejores = $this->db->query($sql_aliados)->fetchAll();

        $sql_aliados_peores = "SELECT cliente_id, razon_social, count(*), sum(dias), (sum(dias)/count(*))as promedio, aliado 
            from 
            (SELECT vnt_clientes.id as cliente_id, vnt_clientes.razon_social, 
            vnt_contratos_facturas.fecha_vencimiento, vnt_facturas_abonos.fecha as fecha_pago, 
            (vnt_facturas_abonos.fecha - vnt_contratos_facturas.fecha_vencimiento) as dias,
            com_aliados.apellido_paterno || ' ' || com_aliados.apellido_materno || ' ' || com_aliados.nombre as aliado
            FROM public.vnt_facturas_abonos
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id 
            join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id and vnt_contrato.year = '$year'
            join com_comisiones on com_comisiones.contrato_id = vnt_contrato.id
            join com_aliados on com_aliados.id = com_comisiones.aliado_id
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id 
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id order by cliente_id) as y 
            group by cliente_id, razon_social, aliado order by promedio desc limit 3";
        $aliados_peores = $this->db->query($sql_aliados_peores)->fetchAll();

        $array_top_clientes = [];
        $array_top_clientes['mejores'] = $mejores;
        $array_top_clientes['peores'] = $peores;
        $array_top_clientes['aliados_mejores'] = $aliados_mejores;
        $array_top_clientes['aliados_peores'] = $aliados_peores;

        $this->content['top'] = $array_top_clientes;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getProyectosByCliente ($id_cliente) {
        $sql = "SELECT vnt_contrato.id,vnt_contrato.recurso_id,vnt_clientes.id as cliente_id,vnt_contrato.monto_total,
            (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos 
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
            join vnt_contrato as w on w.id = vnt_contratos_facturas.contrato_id and w.id = vnt_contrato.id), 0)) 
            as monto_total_abonado,
            (select COALESCE((select sum(monto_factura) from vnt_contratos_facturas 
            where vnt_contratos_facturas.contrato_id = vnt_contrato.id),0)) as monto_facturado, 
            ((select COALESCE((select sum(monto_factura) from vnt_contratos_facturas 
            where vnt_contratos_facturas.contrato_id = vnt_contrato.id),0)) - 
             (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos 
            join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
            join vnt_contrato as w on w.id = vnt_contratos_facturas.contrato_id and w.id = vnt_contrato.id), 0))) as monto_restante, 
            vnt_recurso.subprograma_id, vnt_recurso.codigo, vnt_clientes.razon_social as cliente, 
            vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, vnt_recurso.nombre as proyecto
            from vnt_contrato 
            join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id and vnt_contrato.year = '2020' 
            join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
            join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
            join vnt_contratos_facturas on vnt_contratos_facturas.contrato_id = vnt_contrato.id where cliente_id = ".intval($id_cliente)."
            group by vnt_contrato.id, vnt_clientes.id, vnt_recurso.subprograma_id, vnt_recurso.codigo, vnt_programa.nombre,
            vnt_subprograma.nombre, vnt_recurso.nombre
            order by cliente_id";
        $data = $this->db->query($sql)->fetchAll();
        $this->content['adeudos'] = $data;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getDataCRM () {
        $sql_gdp = "SELECT s2.mes,COALESCE(s1.oportunidades,0) as oportunidades,coalesce(s1.monto, 0) as monto_cerrado from 
            (select date_part('month',DATE '2008-01-01' + (interval '1' month * generate_series(0,11)))as mes) as s2
            left join (select date_part('month',crm_logs.created) as mes,count(crm_logs.id) as oportunidades, sum(crm_oportunidades.valor_final) as monto
            FROM crm_logs
            inner join crm_oportunidades on crm_oportunidades.id = crm_logs.oportunidad_id and crm_logs.tipo = 'LOGRO' 
            -- and crm_oportunidades.tipo = 'GDP' 
            group by mes) as s1 on s2.mes = s1.mes";
        $gdp = $this->db->query($sql_gdp)->fetchAll();

        $data_gdp=array();
        foreach($gdp as $elemento){
            array_push($data_gdp,floatval($elemento['monto_cerrado']));
        }

        $this->content['gdp'] = $data_gdp;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getDataFinanzas ($year, $mes, $proyecto_id, $project_id, $sucursal_id, $vendedor_id, $concepto_id, $subconcepto_id) {
        $and = "";
        $and_sucursal = "";
        if ($proyecto_id > 0) {
            $and = $and . " and vnt_recurso.id = " . $proyecto_id;
        }
        if ($project_id > 0) {
            $and = $and . " and pmo_proyecto.id = " . $project_id;
        }
        if ($sucursal_id > 0) {
            $and = $and . " and pmo_sucursales.id = " . $sucursal_id;
            $and_sucursal = $and_sucursal . " where id = " . $sucursal_id;
        }
        if ($vendedor_id > 0) {
            $and = $and . " and sys_users.id = " . $vendedor_id;
        }
        if ($concepto_id > 0) {
            $and = $and . " and fin_gastos.concepto_id = " . $concepto_id;
        }
        if ($subconcepto_id > 0) {
            $and = $and . " and fin_gastos.subconcepto_id = " . $subconcepto_id;
        }

        $sql_total = "SELECT coalesce(sum(case when fin_solicitudes.iva = true then (fin_gastos.monto * 1.16) else fin_gastos.monto end), 0) as monto_total
            from fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.status = 'PAGADO' and date_part('year', fin_solicitudes.fecha_creado_validacion) = '$year' and date_part('month',fin_solicitudes.fecha_creado_validacion) = $mes
            inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id
            inner join sys_users on pmo_proyecto.lider_proyecto = sys_users.id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join fin_tipos_gastos on fin_tipos_gastos.id = fin_gastos.tipo
            inner join vnt_empresa on vnt_empresa.id = fin_solicitudes.empresa_id
            inner join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id $and";
        $total = $this->db->query($sql_total)->fetchAll();
        $sucursales = "SELECT * FROM pmo_sucursales $and_sucursal";
        $graficas = $this->db->query($sucursales)->fetchAll();
        $array_sucursales = [];
        foreach ($graficas as $graf) {
            $array_montos = [];
            $array_conceptos = [];
            $array=(object)array();
            $id = $graf['id'];
            $array->id = $graf['id'];
            $array->nombre = $graf['nombre'];
            $sql_conceptos = "SELECT pmo_sucursales.id, pmo_sucursales.nombre as sucursal, vnt_concepto.nombre as concepto, 
                coalesce(sum(case when fin_solicitudes.iva = true then (fin_gastos.monto * 1.16) else fin_gastos.monto end), 0) as monto_total
                from fin_gastos
                inner join vnt_concepto on vnt_concepto.id = fin_gastos.concepto_id
                inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and date_part('year',fin_solicitudes.fecha_creado) = '$year' and date_part('month', fin_solicitudes.fecha_creado) = $mes
                inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id
                inner join sys_users on pmo_proyecto.lider_proyecto = sys_users.id
                inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
                inner join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id and pmo_sucursales.id = $id $and
                group by pmo_sucursales.id, pmo_sucursales.nombre, vnt_concepto.nombre order by vnt_concepto.nombre";
            $data_conceptos = $this->db->query($sql_conceptos)->fetchAll();
            foreach($data_conceptos as $concepto) {
                $cantidad = floatval($concepto['monto_total']);
                if ($cantidad > 0) {
                    $cantidad = $cantidad/1000;
                    $cantidad = number_format($cantidad,2,'.',',');
                } else {
                    $cantidad = 0;
                }
                array_push($array_montos, floatval($concepto['monto_total']));
                array_push($array_conceptos, $concepto['concepto'] /* . ' ' . $cantidad . 'K' */);
            }
            $array->conceptos = $array_conceptos;
            $array->montos = $array_montos;
            if (sizeof($array_montos) > 0) {
                array_push($array_sucursales,$array);
            }
        }

        $this->content['data'] = [];
        $this->content['data_sucursales'] = $array_sucursales;
        $this->content['total'] = number_format(floatval($total[0]['monto_total']),2,'.',',');

        // gasto por concepto
        $grafica_concepto = [];
        $grafica_concepto_monto = [];

        $sql_conceptos = "SELECT vnt_concepto.nombre as concepto, coalesce(sum(case when fin_solicitudes.iva = true then (fin_gastos.monto * 1.16) else fin_gastos.monto end), 0) as monto_total
            from fin_gastos
            inner join vnt_concepto on vnt_concepto.id = fin_gastos.concepto_id
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and date_part('year',fin_solicitudes.fecha_creado) = '$year' and date_part('month', fin_solicitudes.fecha_creado) = $mes
            group by vnt_concepto.nombre order by vnt_concepto.nombre";
        $data_conceptos = $this->db->query($sql_conceptos)->fetchAll();
        foreach($data_conceptos as $concepto) {
            array_push($grafica_concepto_monto, floatval($concepto['monto_total']));
            array_push($grafica_concepto, $concepto['concepto'] /* . ' ' . $cantidad . 'K' */);
        }
        $array=(object)array();
        $array->id = 1;
        $array->nombre = 'GASTOS POR CONCEPTO';
        $array->grafica_concepto = $grafica_concepto;
        $array->grafica_concepto_monto = $grafica_concepto_monto;
        $this->content['grafica_concepto'] = [$array];

        // grafica por mes de conceptos de gasto
        $grafica_meses = [];
        if (intval($concepto_id) == 0) {
            $conceptos = Concepto::find();
            foreach($conceptos as $concepto) {
                $conceptoId = $concepto->id;
                $sql = "SELECT s2.mes, coalesce(s1.suma,0) as suma
                    FROM (select date_part('month',DATE '2008-01-01' + (interval '1' MONTH * generate_series(0,11)))AS mes) AS s2
                    LEFT JOIN (select date_part('month',fin_solicitudes.fecha_creado) as mes,
                    coalesce(sum(case when fin_solicitudes.iva = true then (fin_gastos.monto * 1.16) else fin_gastos.monto end), 0) as suma
                    from fin_gastos
                    inner join vnt_concepto on vnt_concepto.id = fin_gastos.concepto_id and vnt_concepto.id = $conceptoId
                    inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
                    where date_part('year',fin_solicitudes.fecha_creado) = '$year' group by mes) as s1 on s2.mes=s1.mes";
                $data = $this->db->query($sql)->fetchAll();
                
                $array_meses = [];
                $array=(object)array();
                $array->name = $concepto->nombre;
                $array->type = 'line';

                foreach($data as $dat) {
                    array_push($array_meses, $dat['suma']);
                }
                $array->data = $array_meses;
                array_push($grafica_meses, $array);
            }
        } else {
            $concepto = Concepto::findFirst($concepto_id);
            $sql = "SELECT s2.mes, coalesce(s1.suma,0) as suma
                FROM (select date_part('month',DATE '2008-01-01' + (interval '1' MONTH * generate_series(0,11)))AS mes) AS s2
                LEFT JOIN (select date_part('month',fin_solicitudes.fecha_creado) as mes,
                coalesce(sum(case when fin_solicitudes.iva = true then (fin_gastos.monto * 1.16) else fin_gastos.monto end), 0) as suma
                from fin_gastos
                inner join vnt_concepto on vnt_concepto.id = fin_gastos.concepto_id and vnt_concepto.id = $concepto_id
                inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
                where date_part('year',fin_solicitudes.fecha_creado) = '$year' group by mes) as s1 on s2.mes=s1.mes";
            $data = $this->db->query($sql)->fetchAll();
                
            $array_meses = [];
            $array=(object)array();
            $array->name = $concepto->nombre;
            $array->type = 'line';

            foreach($data as $dat) {
                array_push($array_meses, $dat['suma']);
            }
            $array->data = $array_meses;
            array_push($grafica_meses, $array);
        }
        $this->content['grafica_meses'] = $grafica_meses;

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
}
