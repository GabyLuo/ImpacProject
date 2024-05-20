<?php

use Phalcon\Mvc\Controller;

class ReportesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getReporte1 ()
    {
        $sql = "SELECT vnt_recurso.id, vnt_recurso.nombre, vnt_recurso.codigo from vnt_recurso";
        $recursos = $this->db->query($sql)->fetchAll();
        $arreglo = [];
        foreach ($recursos as $recurso) {
            $objeto=(object)array();

            $id = $recurso['id'];
            $sql_proyectos = "SELECT count (*) as total_proyectos from pmo_proyecto where recurso_id =$id";
            $total_proyectos = $this->db->query($sql_proyectos)->fetchAll();
            $sql_contratos = "SELECT count (*) as total_contratos from vnt_contrato where recurso_id =$id";
            $total_contratos = $this->db->query($sql_contratos)->fetchAll();
            $sql_contratos_firmados = "SELECT count (*) as total_contratos_firmados from vnt_contrato where recurso_id =$id and documento_final is not null";
            $total_contratos_firmados = $this->db->query($sql_contratos_firmados)->fetchAll();
            
            $objeto->id = $id;
            $objeto->codigo = $recurso['codigo'];
            $objeto->nombre = $recurso['nombre'];
            $objeto->total_proyectos = $total_proyectos[0]['total_proyectos'];
            $objeto->total_contratos = $total_contratos[0]['total_contratos'];
            $objeto->total_contratos_firmados = $total_contratos_firmados[0]['total_contratos_firmados'];
            array_push($arreglo, $objeto);
        }
        
        $this->content['reportes_proyectos'] = $arreglo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getReporteGastos ($recurso, $proyecto, $proveedor_id, $empresa_id, $lider_id, $concepto_id, $subconcepto_id, $factura, $status, $fecha_inicio, $fecha_fin, $comprobado) {
        if (intval($concepto_id) !== 0) {
            $concepto = " and vnt_concepto.id = ".$concepto_id;
        } else {
            $concepto = " ";
        }
        if (intval($subconcepto_id) !== 0) {
            $subconcepto = " and vnt_subconcepto.id = ".$subconcepto_id;
        } else {
            $subconcepto = " ";
        }
        $anio = date('Y');

        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $sql_auxiliar = "";
        if ($role === 'Auxiliar') {
            $sql_auxiliar = " AND
            ((exists (select pmo_colaboradores.proyecto_id 
                     from pmo_colaboradores 
                     where pmo_colaboradores.proyecto_id = fin_solicitudes.proyecto_id and pmo_colaboradores.usuario_id = $id))
            or (exists (select pmo_autorizadores.proyecto_id 
                     from pmo_autorizadores 
                     where pmo_autorizadores.proyecto_id = fin_solicitudes.proyecto_id and pmo_autorizadores.usuario_id = $id))
            or (exists (select pmo_responsable_pagos.proyecto_id 
                     from pmo_responsable_pagos 
                     where pmo_responsable_pagos.proyecto_id = fin_solicitudes.proyecto_id and pmo_responsable_pagos.usuario_id = $id))) ";
        }

        $sql = "";
        if(intval($recurso) !== 0) {
            $id_recurso = intval($recurso);
            $sql = $sql . " and vnt_recurso.id = $id_recurso";
        }
        if(intval($proyecto) !== 0) {
            $id_proyecto = intval($proyecto);
            $sql = $sql . " and pmo_proyecto.id = $id_proyecto";
        }
        if(intval($proveedor_id) !== 0) {
            $id_proveedor = intval($proveedor_id);
            $sql = $sql . " and pmo_proveedores.id = $id_proveedor";
        }
        if(intval($empresa_id) !== 0) {
            $id_empresa = intval($empresa_id);
            $sql = $sql . " and vnt_empresa.id = $id_empresa";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $sql = $sql . " and pmo_proyecto.lider_proyecto = $id_lider";
        }
        if ($factura === 'SI') {
            $sql = $sql . " and fin_solicitudes.iva = true";
        }
        if ($factura === 'NO') {
            $sql = $sql . " and fin_solicitudes.iva = false";
        }
        if($comprobado === 'SI') {
            $sql = $sql . " and fin_solicitudes.comprobado = true";
        }
        if($comprobado === 'NO') {
            $sql = $sql . " and fin_solicitudes.comprobado = false";
        }
        if($status !== 'all') {
            $sql = $sql . " and fin_solicitudes.status = '$status'";
        }
        if($fecha_inicio !== 'null') {
            $fecha_inicio = date('Y-m-d',strtotime($fecha_inicio));
            // $sql = $sql . " and fin_solicitudes.fecha_creado >= '$fecha_inicio'";
        }
        if($fecha_fin !== 'null') {
            $fecha_fin = date('Y-m-d',strtotime($fecha_fin));
            // $sql = $sql . " and fin_solicitudes.fecha_creado <= '$fecha_fin'";
        }
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $sql = $sql . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
        }

        $sql_query = "SELECT sys_users.nickname as colaborador,vnt_recurso.codigo,vnt_recurso.nombre as nombre_proyecto,pmo_actividades.nombre,fin_solicitudes.id,
            (SELECT fecha_autorizacion FROM public.fin_solicitudes_autorizador where solicitud_id = fin_solicitudes.id
            order by id desc limit 1) as fecha_pago,(select nombre from vnt_concepto where fin_gastos.concepto_id = vnt_concepto.id) as concepto,fin_gastos.concepto_id as id_concepto,(select nombre from vnt_subconcepto where fin_gastos.subconcepto_id = vnt_subconcepto.id) as subconcepto,fin_gastos.subconcepto_id as id_subconcepto, pmo_proveedores.razon_social, fin_gastos.descripcion,
            vnt_empresa.razon_social as empresa,pmo_proyecto.id as id_project,pmo_proyecto.nombre as nombre_project,fin_solicitudes.factura as solicitud_facturada,
            case 
            when fin_solicitudes.status = 'PAGADO' then 'EGRESOS'
            when fin_solicitudes.status != 'PAGADO' then 'PENDIENTE DE PAGO' end as egresos,
            case
            when fin_solicitudes.iva = true then 'CON FACTURA'
            when fin_solicitudes.iva = false then 'SIN FACTURA' end as factura,
            case 
            when fin_solicitudes.comprobado = true then 'TOTAL'
            when fin_solicitudes.comprobado = false then 'NO COMPROBADO' end as monto_comprobado,
            case 
            when (fin_gastos.pago ='banco1' or fin_gastos.pago = 'tarjeta1') then pmo_proveedores.banco  
            when (fin_gastos.pago ='banco2' or fin_gastos.pago = 'tarjeta2') then pmo_proveedores.banco2
            when (fin_gastos.pago ='banco3' or fin_gastos.pago = 'tarjeta3') then pmo_proveedores.banco3
            when (fin_gastos.pago ='banco4' or fin_gastos.pago = 'tarjeta4') then pmo_proveedores.banco4
            when fin_gastos.pago = 'toka' then 'TOKA' end as banco,
            -- case 
            -- when fin_gastos.pago ='banco1' then pmo_proveedores.clabe  
            -- when fin_gastos.pago ='banco2' then pmo_proveedores.clabe2
            -- when fin_gastos.pago ='banco3' then pmo_proveedores.clabe3
            -- when fin_gastos.pago ='banco4' then pmo_proveedores.clabe4
            -- when fin_gastos.pago ='tarjeta1' then pmo_proveedores.tarjeta1  
            -- when fin_gastos.pago ='tarjeta2' then pmo_proveedores.tarjeta2
            -- when fin_gastos.pago ='tarjeta3' then pmo_proveedores.tarjeta3
            -- when fin_gastos.pago ='tarjeta4' then pmo_proveedores.tarjeta4
            -- when fin_gastos.pago ='toka' then pmo_proveedores.toka end as clabe,
            fin_gastos.cuenta_pago as clabe,
            -- fin_gastos.monto as total_sin_iva,fin_gastos.monto + (fin_gastos.monto * 0.16) as total_con_iva,
            case 
            when fin_gastos.fecha_ejercido is not null then 'CONCLUIDO' else 'NO CONCLUIDO' end as estado,
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * 0.16)) else fin_gastos.monto end as total,
            case
            WHEN date_part('month',pmo_proyecto.inicio) = 1 THEN 'ENERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 2 THEN  'FEBRERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 3 THEN 'MARZO' 
            WHEN date_part('month',pmo_proyecto.inicio) = 4 THEN 'ABRIL' 
            WHEN date_part('month',pmo_proyecto.inicio) = 5 THEN 'MAYO'
            WHEN date_part('month',pmo_proyecto.inicio) = 6 THEN 'JUNIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 7 THEN 'JULIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 8 THEN 'AGOSTO'
            WHEN date_part('month',pmo_proyecto.inicio) = 9 THEN 'SEPTIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 10 THEN 'OCTUBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 11 THEN 'NOVIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 12 THEN 'DICIEMBRE'
            end as mes,vnt_recurso.rubro as rubro,
            (select razon_social from vnt_empresa where fin_solicitudes.subempresa_id = vnt_empresa.id) as subempresa, (select nickname from sys_users inner join pmo_proyecto on sys_users.id = pmo_proyecto.lider_proyecto inner join fin_solicitudes on fin_solicitudes.proyecto_id = pmo_proyecto.id and fin_solicitudes.id = fin_gastos.solicitud_id) as lider, fin_solicitudes.status,fin_solicitudes.fecha_creado, vnt_clientes.razon_social as cliente, fin_gastos.monto as subtotal, 
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto * 0.16) else 0 end as iva, fin_gastos.sumatoria_factura as monto_cobrado,
            case
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto + (fin_gastos.monto * 0.16)))
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto))
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura = 0) then 0
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura = 0) then 0 end as porcentaje_cobrado,
            case
            when fin_solicitudes.subempresa_id is not null then (fin_gastos.monto * (fin_gastos.comision/100))
            when fin_solicitudes.subempresa_id is null then 0
            end as cantidad_comision,
            case
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)) + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)))
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is null then (fin_gastos.monto + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is null then fin_gastos.monto
            when fin_solicitudes.subempresa_id is null then 0
            end as total_comision, (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal,
            (select nombre from pmo_proyecto where id = fin_solicitudes.proyecto_comision_id) as proyecto_comision
            from fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and (fin_solicitudes.proyecto_comision_id is null or fin_solicitudes.proyecto_comision_id = 0)
            inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
            inner join sys_users on fin_solicitudes.creador = sys_users.id {$sql_auxiliar}
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join fin_tipos_gastos on fin_tipos_gastos.id = fin_gastos.tipo
            inner join pmo_proveedores on pmo_proveedores.id = fin_gastos.proveedor_id
            inner join vnt_empresa on vnt_empresa.id = fin_solicitudes.empresa_id $sql

            union

            SELECT sys_users.nickname as colaborador,vnt_recurso.codigo,vnt_recurso.nombre as nombre_proyecto,pmo_actividades.nombre,fin_solicitudes.id,
            (SELECT fecha_autorizacion FROM public.fin_solicitudes_autorizador where solicitud_id = fin_solicitudes.id
            order by id desc limit 1) as fecha_pago,(select nombre from vnt_concepto where fin_gastos.concepto_id = vnt_concepto.id) as concepto,fin_gastos.concepto_id as id_concepto,(select nombre from vnt_subconcepto where fin_gastos.subconcepto_id = vnt_subconcepto.id) as subconcepto,fin_gastos.subconcepto_id as id_subconcepto, pmo_proveedores.razon_social, fin_gastos.descripcion,
            vnt_empresa.razon_social as empresa,pmo_proyecto.id as id_project,pmo_proyecto.nombre as nombre_project,fin_solicitudes.factura as solicitud_facturada,
            case 
            when fin_solicitudes.status = 'PAGADO' then 'EGRESOS'
            when fin_solicitudes.status != 'PAGADO' then 'PENDIENTE DE PAGO' end as egresos,
            case
            when fin_solicitudes.iva = true then 'CON FACTURA'
            when fin_solicitudes.iva = false then 'SIN FACTURA' end as factura,
            case 
            when fin_solicitudes.comprobado = true then 'TOTAL'
            when fin_solicitudes.comprobado = false then 'NO COMPROBADO' end as monto_comprobado,
            case 
            when (fin_gastos.pago ='banco1' or fin_gastos.pago = 'tarjeta1') then pmo_proveedores.banco  
            when (fin_gastos.pago ='banco2' or fin_gastos.pago = 'tarjeta2') then pmo_proveedores.banco2
            when (fin_gastos.pago ='banco3' or fin_gastos.pago = 'tarjeta3') then pmo_proveedores.banco3
            when (fin_gastos.pago ='banco4' or fin_gastos.pago = 'tarjeta4') then pmo_proveedores.banco4
            when fin_gastos.pago = 'toka' then 'TOKA' end as banco,
            -- case 
            -- when fin_gastos.pago ='banco1' then pmo_proveedores.clabe  
            -- when fin_gastos.pago ='banco2' then pmo_proveedores.clabe2
            -- when fin_gastos.pago ='banco3' then pmo_proveedores.clabe3
            -- when fin_gastos.pago ='banco4' then pmo_proveedores.clabe4
            -- when fin_gastos.pago ='tarjeta1' then pmo_proveedores.tarjeta1  
            -- when fin_gastos.pago ='tarjeta2' then pmo_proveedores.tarjeta2
            -- when fin_gastos.pago ='tarjeta3' then pmo_proveedores.tarjeta3
            -- when fin_gastos.pago ='tarjeta4' then pmo_proveedores.tarjeta4
            -- when fin_gastos.pago ='toka' then pmo_proveedores.toka end as clabe,
            fin_gastos.cuenta_pago as clabe,
            -- fin_gastos.monto as total_sin_iva,fin_gastos.monto + (fin_gastos.monto * 0.16) as total_con_iva,
            case 
            when fin_gastos.fecha_ejercido is not null then 'CONCLUIDO' else 'NO CONCLUIDO' end as estado,
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * 0.16)) else fin_gastos.monto end as total,
            case
            WHEN date_part('month',pmo_proyecto.inicio) = 1 THEN 'ENERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 2 THEN  'FEBRERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 3 THEN 'MARZO' 
            WHEN date_part('month',pmo_proyecto.inicio) = 4 THEN 'ABRIL' 
            WHEN date_part('month',pmo_proyecto.inicio) = 5 THEN 'MAYO'
            WHEN date_part('month',pmo_proyecto.inicio) = 6 THEN 'JUNIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 7 THEN 'JULIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 8 THEN 'AGOSTO'
            WHEN date_part('month',pmo_proyecto.inicio) = 9 THEN 'SEPTIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 10 THEN 'OCTUBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 11 THEN 'NOVIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 12 THEN 'DICIEMBRE'
            end as mes,vnt_recurso.rubro as rubro,
            (select razon_social from vnt_empresa where fin_solicitudes.subempresa_id = vnt_empresa.id) as subempresa, (select nickname from sys_users inner join pmo_proyecto on sys_users.id = pmo_proyecto.lider_proyecto inner join fin_solicitudes on fin_solicitudes.proyecto_comision_id = pmo_proyecto.id and fin_solicitudes.id = fin_gastos.solicitud_id) as lider, fin_solicitudes.status,fin_solicitudes.fecha_creado, vnt_clientes.razon_social as cliente, fin_gastos.monto as subtotal, 
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto * 0.16) else 0 end as iva, fin_gastos.sumatoria_factura as monto_cobrado,
            case
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto + (fin_gastos.monto * 0.16)))
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto))
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura = 0) then 0
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura = 0) then 0 end as porcentaje_cobrado,
            case
            when fin_solicitudes.subempresa_id is not null then (fin_gastos.monto * (fin_gastos.comision/100))
            when fin_solicitudes.subempresa_id is null then 0
            end as cantidad_comision,
            case
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)) + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)))
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is null then (fin_gastos.monto + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is null then fin_gastos.monto
            when fin_solicitudes.subempresa_id is null then 0
            end as total_comision, (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal,
            (select nombre from pmo_proyecto where id = fin_solicitudes.proyecto_comision_id) as proyecto_comision
            from fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_comision_id is not null
            inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
            inner join sys_users on fin_solicitudes.creador = sys_users.id {$sql_auxiliar}
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_comision_id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join fin_tipos_gastos on fin_tipos_gastos.id = fin_gastos.tipo
            inner join pmo_proveedores on pmo_proveedores.id = fin_gastos.proveedor_id
            inner join vnt_empresa on vnt_empresa.id = fin_solicitudes.empresa_id $sql";
        $sql_final = "SELECT * FROM ($sql_query) as x where (date_part('year',fecha_pago) = '$anio' or date_part('year', fecha_creado) = '$anio') order by id";
        $arreglo = $this->db->query($sql_final)->fetchAll();
        $nuevo = [];
            foreach ($arreglo as $elemento){
                $s=(object)array();
                $id_project = $elemento['id_project'];
                $s->id_project = $elemento['id_project'];
                $s->colaborador = $elemento['colaborador'];
                $s->codigo = $elemento['codigo'];
                $s->nombre_proyecto = $elemento['nombre_proyecto'];
                $s->nombre = $elemento['nombre'];
                $s->id = $elemento['id'];
                $s->fecha_pago = $elemento['fecha_pago'];
                $s->pfecha_pago = $elemento['fecha_pago'];
                $s->concepto = $elemento['concepto'];
                $s->subconcepto = $elemento['subconcepto'];
                $s->razon_social = $elemento['razon_social'];
                $s->descripcion = $elemento['descripcion'];
                $s->banco = $elemento['banco'];
                $s->clabe = $elemento['clabe'];
                $s->estado = $elemento['estado'];
                $s->empresa = $elemento['empresa'];
                $s->egresos = $elemento['egresos'];
                $s->factura = $elemento['factura'];
                $s->mes = $elemento['mes'];
                $s->rubro = $elemento['rubro'];
                $s->monto_comprobado = $elemento['monto_comprobado'];
                $s->total = number_format(floatval($elemento['total']),2,'.',',');
                $s->subempresa = $elemento['subempresa'];
                $s->lider = $elemento['lider'];
                $s->status = $elemento['status'];
                $s->fecha_creado = $elemento['fecha_creado']; 
                $s->subtotal = number_format(floatval($elemento['subtotal']),2,'.',',');
                $s->iva = number_format(floatval($elemento['iva']),2,'.',',');
                $s->cantidad_comision = number_format(floatval($elemento['cantidad_comision']),2,'.',',');
                $s->total_comision = number_format(floatval($elemento['total_comision']),2,'.',',');
                $s->monto_cobrado = number_format(floatval($elemento['total']),2,'.',',');
                $s->porcentaje_cobrado = round($elemento['porcentaje_cobrado'],2);
                $s->cliente = $elemento['cliente'];
                $s->sucursal = $elemento['sucursal'];
                $s->nombre_project = $elemento['nombre_project'];
                $s->proyecto_comision = $elemento['proyecto_comision'];
                if(intval($elemento['solicitud_facturada']) === 0) {
                    $s->color_factura = 'red-10';
                    $s->icon_factura = 'fas fa-times';
                } else {
                    $s->color_factura = 'green-9';
                    $s->icon_factura = 'fas fa-check';
                }
                $s->solicitud_facturada = $elemento['solicitud_facturada'];
                if (intval($concepto_id) !== 0 && intval($subconcepto_id) === 0) {
                    if ($elemento['id_concepto'] === intval($concepto_id)) {
                        array_push($nuevo,$s);
                    }
                }
                if (intval($subconcepto_id) !== 0 && intval($concepto_id) === 0) {
                    if ($elemento['id_subconcepto'] === intval($subconcepto_id)) {
                        array_push($nuevo,$s);
                    }
                }
                if (intval($concepto_id) !== 0 && intval($subconcepto_id) !== 0) {
                    if (($elemento['id_concepto'] === intval($concepto_id)) && ($elemento['id_subconcepto'] === intval($subconcepto_id))) {
                        array_push($nuevo,$s);
                    }
                }
                if (intval($concepto_id) === 0 && intval($subconcepto_id) === 0) {
                    array_push($nuevo,$s);
                }
            }
        $this->content['reportes_gastos'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getReporteAuditoria ($estado, $municipio, $tipo) {
        $busqueda_estado = "";
        $busqueda_municipio = "";
        if (intval($estado) > 0) {
            $id_estado = intval($estado);
            $busqueda_estado = " and vnt_estado.id = $id_estado";
        }
        if (intval($municipio) > 0) {
            $id_municipio = intval($municipio);
            $busqueda_municipio = " and vnt_clientes.municipio_id <> null and vnt_municipio.id = $id_municipio";
        }
        if (intval($tipo) === 0) {
            $sql = "SELECT -- y.id, y.nombre, y.hijos,--
        y.proyecto_id, y.project, y.proyecto, y.codigo, y.estado, y.municipio from (SELECT pmo_actividades.id, pmo_actividades.nombre,pmo_actividades.proyecto_id, pmo_proyecto.nombre as project,
                               vnt_recurso.nombre as proyecto, vnt_recurso.codigo, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio,
                    (select count(*) from pmo_actividades as x where padre_id = pmo_actividades.id) as hijos, 
        (select nombre from pmo_actividades as x where id = pmo_actividades.padre_id) as padre from pmo_actividades
                              inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
                              inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
                               inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$busqueda_estado." 
                                 left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$busqueda_municipio.") as y
                inner join fin_gastos on fin_gastos.actividad_id = y.id
                and y.hijos > 0
                group by -- y.id, y.nombre, y.hijos, 
                y.proyecto_id, y.project, y.proyecto, y.codigo, y.estado, y.municipio";
        } else {
            $sql = "SELECT pmo_proyecto.nombre as project, vnt_recurso.codigo, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, vnt_recurso.nombre as proyecto
            from (SELECT pmo_actividades.id, pmo_actividades.costo, pmo_actividades.nombre, pmo_actividades.proyecto_id, (pmo_actividades.costo - 
            ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                    FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id 
            and fin_gastos.actividad_id = pmo_actividades.id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' 
            or fin_gastos.fecha_ejercido is not null)), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma 
            FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = pmo_actividades.id
            and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total2))) 
            as cantidad_disponible 
            from pmo_actividades) as y -- where y.cantidad_disponible < -0.1
            inner join pmo_proyecto on pmo_proyecto.id = y.proyecto_id and y.cantidad_disponible < -0.1
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$busqueda_estado."
             left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$busqueda_municipio."
            group by pmo_proyecto.nombre, vnt_recurso.codigo, vnt_estado.nombre, vnt_municipio.nombre, vnt_recurso.nombre";
        }
        
        // var_dump($sql);
        $auditoria = $this->db->query($sql)->fetchAll();
        $this->content['auditoria'] = $auditoria; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_reporteAuditoria ($estado, $municipio, $tipo) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Código', 'Proyecto','Project','Estado','Municipio'), ',');

        $busqueda_estado = "";
        $busqueda_municipio = "";
        if (intval($estado) > 0) {
            $id_estado = intval($estado);
            $busqueda_estado = " and vnt_estado.id = $id_estado";
        }
        if (intval($municipio) > 0) {
            $id_municipio = intval($municipio);
            $busqueda_municipio = " and vnt_clientes.municipio_id <> null and vnt_municipio.id = $id_municipio";
        }
        if (intval($tipo) === 0) {
            $sql = "SELECT -- y.id, y.nombre, y.hijos,--
        y.proyecto_id, y.project, y.proyecto, y.codigo, y.estado, y.municipio from (SELECT pmo_actividades.id, pmo_actividades.nombre,pmo_actividades.proyecto_id, pmo_proyecto.nombre as project,
                               vnt_recurso.nombre as proyecto, vnt_recurso.codigo, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio,
                    (select count(*) from pmo_actividades as x where padre_id = pmo_actividades.id) as hijos, 
        (select nombre from pmo_actividades as x where id = pmo_actividades.padre_id) as padre from pmo_actividades
                              inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
                              inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
                               inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$busqueda_estado." 
                                 left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$busqueda_municipio.") as y
                inner join fin_gastos on fin_gastos.actividad_id = y.id
                and y.hijos > 0
                group by -- y.id, y.nombre, y.hijos, 
                y.proyecto_id, y.project, y.proyecto, y.codigo, y.estado, y.municipio";
        } else {
            $sql = "SELECT pmo_proyecto.nombre as project, vnt_recurso.codigo, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, vnt_recurso.nombre as proyecto
            from (SELECT pmo_actividades.id, pmo_actividades.costo, pmo_actividades.nombre, pmo_actividades.proyecto_id, (pmo_actividades.costo - 
            ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                    FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id 
            and fin_gastos.actividad_id = pmo_actividades.id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' 
            or fin_gastos.fecha_ejercido is not null)), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma 
            FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = pmo_actividades.id
            and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total2))) 
            as cantidad_disponible 
            from pmo_actividades) as y -- where y.cantidad_disponible < -0.1
            inner join pmo_proyecto on pmo_proyecto.id = y.proyecto_id and y.cantidad_disponible < -0.1
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$busqueda_estado."
             left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$busqueda_municipio."
            group by pmo_proyecto.nombre, vnt_recurso.codigo, vnt_estado.nombre, vnt_municipio.nombre, vnt_recurso.nombre";
        }
        // var_dump($sql);
        $arreglo = $this->db->query($sql)->fetchAll();
            foreach($arreglo as $elemento){
                fputcsv($fp, [
                    $elemento['codigo'],
                    $elemento['proyecto'],
                    $elemento['project'],
                    $elemento['estado'],
                    $elemento['municipio']
                    ], ',');
            }
            rewind($fp);
            $output = stream_get_contents($fp);
            mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
            fclose($fp);

            $this->response->resetHeaders();
            $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_auditoria'. '.csv');
            $this->response->setContent($output);
            $this->response->send();
    }

    public function reporteProjects ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT pmo_proyecto.id,vnt_recurso.nombre as proyecto, pmo_proyecto.nombre as project, pmo_proyecto.presupuesto, pmo_proyecto.presupuesto_actual, (select sum(pmo_actividades.costo) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), (select sum(pmo_actividades.presupuesto_inicial) as presupuesto_inicial_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as lider, 
        (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or (fin_gastos.fecha_ejercido is not null and fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO'))) as cambios),
        (select coalesce(sum(cambios.suma), 0) as recurso_solicitado from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'CREADO')) as cambios), 
        pmo_proyecto.inicio as fecha_inicio, pmo_proyecto.fin as fecha_termino, 
        (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = pmo_proyecto.empresa_id),vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, pmo_proyecto.created
        FROM pmo_proyecto
        inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." and pmo_proyecto.year = '$year'
        inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id 
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by pmo_proyecto.nombre";

        // print_r($sql);
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];
            foreach ($arreglo as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->proyecto = $elemento['proyecto'];
                $s->project = $elemento['project'];
                $s->presupuesto = number_format(floatval($elemento['presupuesto']),2,'.',',');
                $s->presupuesto_actual = number_format(floatval($elemento['presupuesto_actual']),2,'.',',');
                $s->recurso_solicitado = number_format(floatval($elemento['recurso_solicitado']),2,'.',',');
                $s->presupuesto_inicial_actividad_principal = number_format(floatval($elemento['presupuesto_inicial_actividad_principal']),2,'.',',');
                $s->presupuesto_actividad_principal = number_format(floatval($elemento['presupuesto_actividad_principal']),2,'.',',');
                $s->diferencia_presupuestos = number_format(floatval($elemento['presupuesto_actual'] - $elemento['presupuesto']),2,'.',',');
                $s->diferencia_presupuestos_actividades = number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['presupuesto_inicial_actividad_principal']),2,'.',',');
                $s->estado = $elemento['estado'];
                $s->municipio = $elemento['municipio'];
                $s->lider = $elemento['lider'];
                if (intval($elemento['avance_real'] > 0)) {
                    $s->porcentaje_avance = round((intval($elemento['avance_real']) * 100) / (intval($elemento['actividades_level_1']) * 100), 0);
                } else {
                    $s->porcentaje_avance = 0;
                }
                $s->recurso_ejercido = number_format(floatval($elemento['recurso_ejercido']),2,'.',',');
                $s->recurso_por_ejercer = number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',',');
                if ($elemento['presupuesto_actividad_principal'] > 0 &&  $elemento['recurso_ejercido'] > 0) {
                    $s->porcentaje_recurso_ejercido = round((floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal'])), 0);
                    // $s->porcentaje_recurso_ejercido = (floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal']));
                } else {
                    $s->porcentaje_recurso_ejercido = 0;
                }
                $s->optimizacion = 100 - $s->porcentaje_recurso_ejercido;
                $s->fecha_inicio = $elemento['fecha_inicio'];
                $s->fecha_termino = $elemento['fecha_termino'];
                $s->empresa = $elemento['empresa'];
                $s->programa = $elemento['programa'];
                $s->subprograma = $elemento['subprograma'];
                $s->creado = date("Y-m-d H:i:s", strtotime($elemento['created']));
                array_push($nuevo,$s);
            }
        $this->content['reportes_projects'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function reporteProjectsMonto ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT pmo_proyecto.id,vnt_recurso.nombre as proyecto, pmo_proyecto.nombre as project, pmo_proyecto.presupuesto, pmo_proyecto.presupuesto_actual, (select sum(pmo_actividades.costo) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), (select sum(pmo_actividades.presupuesto_inicial) as presupuesto_inicial_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as lider, 
        (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 

        (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)) as cambios),

        (select coalesce(sum(cambios.suma), 0) as recurso_solicitado from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'CREADO')) as cambios),

        (select coalesce(sum(cambios.suma), 0) as recurso_ejercido_sin_iva from (select fin_gastos.monto as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null) and fin_gastos.concepto_id != 5) as cambios),

        (select coalesce(sum(cambios.suma), 0) as recurso_sin_ejercer_sin_iva from (select fin_gastos.monto as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status != 'PAGADO' and fin_solicitudes.status != 'CANCELADO') and fin_gastos.concepto_id != 5) as cambios),

        (select coalesce(sum(cambios.suma), 0) as gastos_financieros from (select fin_gastos.monto as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null) and fin_gastos.concepto_id = 5) as cambios),

        pmo_proyecto.inicio as fecha_inicio, pmo_proyecto.fin as fecha_termino, pmo_proyecto.monto_bolsa,
        (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = pmo_proyecto.empresa_id),vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, pmo_proyecto.created, vnt_clientes.razon_social as cliente, case when pmo_proyecto.finalizado = true then 'FINALIZADO' else 'ACTIVO' end as status_project, (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal
        FROM pmo_proyecto 
        inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." and pmo_proyecto.year = '$year'
        inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id 
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by pmo_proyecto.nombre";

        // print_r($sql);
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];
            foreach ($arreglo as $elemento){
                $utilidad_bruta = 0;
                $utilidad_operacion = 0;
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->proyecto = $elemento['proyecto'];
                $s->project = $elemento['project'];
                $s->presupuesto = number_format(floatval($elemento['presupuesto']),2,'.',',');
                $s->monto_bolsa = number_format(floatval($elemento['monto_bolsa']),2,'.',',');
                $monto_bolsa = $elemento['monto_bolsa'];
                $gastos_financieros = $elemento['gastos_financieros'];
                if ($monto_bolsa > 0) {
                    $utilidad_bruta = $monto_bolsa - ($elemento['recurso_ejercido_sin_iva'] + $elemento['recurso_sin_ejercer_sin_iva']);
                    $utilidad_operacion = $utilidad_bruta - $gastos_financieros;
                } else {
                    $utilidad_bruta = 0;
                    $utilidad_operacion = 0;
                }
                // var_dump($gastos_financieros);
                $s->utilidad_operacion = number_format(floatval($utilidad_operacion),2,'.',',');
                $s->utilidad_bruta = number_format(floatval($utilidad_bruta),2,'.',',');
                $s->presupuesto_actual = number_format(floatval($elemento['presupuesto_actual']),2,'.',',');
                $s->recurso_solicitado = number_format(floatval($elemento['recurso_solicitado']),2,'.',',');
                $s->presupuesto_inicial_actividad_principal = number_format(floatval($elemento['presupuesto_inicial_actividad_principal']),2,'.',',');
                $s->presupuesto_actividad_principal = number_format(floatval($elemento['presupuesto_actividad_principal']),2,'.',',');
                $s->diferencia_presupuestos = number_format(floatval($elemento['presupuesto_actual'] - $elemento['presupuesto']),2,'.',',');
                $s->diferencia_presupuestos_actividades = number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['presupuesto_inicial_actividad_principal']),2,'.',',');
                $utilidad_neta = floatval($elemento['monto_bolsa'] - $elemento['presupuesto_actividad_principal']);
                $s->utilidad_neta = number_format(floatval($elemento['monto_bolsa'] - $elemento['presupuesto_actividad_principal']),2,'.',',');
                $s->estado = $elemento['estado'];
                $s->municipio = $elemento['municipio'];
                $s->lider = $elemento['lider'];
                if (intval($elemento['avance_real'] > 0)) {
                    $s->porcentaje_avance = round((intval($elemento['avance_real']) * 100) / (intval($elemento['actividades_level_1']) * 100), 0);
                } else {
                    $s->porcentaje_avance = 0;
                }
                $s->recurso_ejercido = number_format(floatval($elemento['recurso_ejercido']),2,'.',',');
                $s->recurso_por_ejercer = number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',',');
                if ($elemento['monto_bolsa'] > 0 &&  $elemento['presupuesto_actividad_principal'] > 0) {
                    $s->porcentaje_recurso_ejercido = round((floatval($elemento['presupuesto_actividad_principal']) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                } else {
                    $s->porcentaje_recurso_ejercido = 0;
                }
                if ($elemento['monto_bolsa'] > 0 &&  $utilidad_neta > 0) {
                    $s->porcentaje_utilidad = round((floatval($utilidad_neta) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                } else {
                    $s->porcentaje_utilidad = 0;
                }
                $importe = $elemento['monto_bolsa'] - $elemento['recurso_ejercido'];
                $s->importe = number_format(floatval($elemento['monto_bolsa'] - $elemento['recurso_ejercido']),2,'.',',');
                if ($importe > 0) {
                    $s->porcentaje_importe = round((floatval($importe) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                } else {
                    $s->porcentaje_importe = 0;
                }
                $s->optimizacion = 100 - $s->porcentaje_recurso_ejercido;
                $s->fecha_inicio = $elemento['fecha_inicio'];
                $s->fecha_termino = $elemento['fecha_termino'];
                $s->empresa = $elemento['empresa'];
                $s->programa = $elemento['programa'];
                $s->subprograma = $elemento['subprograma'];
                $s->creado = date("Y-m-d H:i:s", strtotime($elemento['created']));
                $s->cliente = $elemento['cliente'];
                $s->sucursal = $elemento['sucursal'];
                $s->status_project = $elemento['status_project'];
                array_push($nuevo,$s);
            }
        $this->content['reportes_projects'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function reporteProjectsUtilidad ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
        if(intval($municipio_id) !== 0) {
            $id_municipio = intval($municipio_id);
            $municipio = " and vnt_municipio <> null and vnt_municipio.id = $id_municipio";
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT vnt_recurso.id,vnt_recurso.nombre as proyecto, vnt_recurso.codigo,(select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, vnt_recurso.monto as monto_bolsa, vnt_recurso.id as proyecto_id_suma, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as creador, vnt_clientes.razon_social as cliente
        FROM vnt_recurso
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id and vnt_recurso.year = '$year'
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        inner join sys_users on sys_users.id = vnt_recurso.created_by
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by vnt_recurso.nombre";

        // print_r($sql);
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];
            foreach ($arreglo as $elemento){
                $id = $elemento['id'];
                $recurso = $elemento['id'];
                $sql_u = "SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
                    inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
                    and pmo_proyecto.recurso_id = $recurso and pmo_actividades.nivel = '1'";
                $u = $this->db->query($sql_u)->fetchAll();
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->codigo = $elemento['codigo'];
                $s->proyecto = $elemento['proyecto'];
                $s->cliente = $elemento['cliente'];
                $s->presupuesto_actividad_principal = number_format(floatval($u[0]['presupuesto_actividad_principal']),2,'.',',');
                $presupuesto_actividad_principal = floatval($u[0]['presupuesto_actividad_principal']);
                $s->recurso_ejercido = number_format(floatval($elemento['recurso_ejercido']),2,'.',',');
                $s->recurso_por_ejercer = number_format(floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',',');
                $s->programa = $elemento['programa'];
                $s->subprograma = $elemento['subprograma'];
                $s->estado = $elemento['estado'];
                $s->municipio = $elemento['municipio'];
                $s->monto_bolsa = number_format(floatval($elemento['monto_bolsa']),2,'.',',');
                $monto_bolsa = $elemento['monto_bolsa'];
                if (count($u) > 0) {
                    $utilidad_projects = floatval($elemento['monto_bolsa'] - $u[0]['presupuesto_actividad_principal']);
                    $s->utilidad_projects = number_format(floatval($elemento['monto_bolsa'] - $u[0]['presupuesto_actividad_principal']),2,'.',',');
                } else {
                    $s->utilidad_projects = 0;
                }
                if (floatval($monto_bolsa) > 0 && $s->utilidad_projects !== 0) {
                    $s->utilidad_porcentaje = round(($utilidad_projects/$monto_bolsa)*100, 2);
                } else {
                    $s->utilidad_porcentaje = 0;
                }
                if (floatval($monto_bolsa) > 0 && $presupuesto_actividad_principal !== 0) {
                    $s->gasto_porcentaje = round(($presupuesto_actividad_principal/$monto_bolsa)*100, 2);
                } else {
                    $s->gasto_porcentaje = 0;
                }
                if ($elemento['recurso_ejercido'] > 0 && $presupuesto_actividad_principal !== 0) {
                    $s->ejercido_porcentaje = round(($elemento['recurso_ejercido']/$presupuesto_actividad_principal)*100, 2);
                } else {
                    $s->ejercido_porcentaje = 0;
                }
                if (floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']) > 0 && $presupuesto_actividad_principal !== 0) {
                    $s->ejercer_porcentaje = round((floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido'])/$presupuesto_actividad_principal)*100, 2);
                } else {
                    $s->ejercer_porcentaje = 0;
                }
                $s->creador = $elemento['creador'];
                array_push($nuevo,$s);
            }
        $this->content['reportes_projects'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_reporteProjects ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Proyecto','Programa','Subprograma','Project','Fecha creación','Recurso inicial','Recurso actual','Diferencia','Estado','Municipio', 'Líder', '% Avance', 'Recurso solicitado', 'Recurso ejercido','Recurso por ejercer','% Ejercido vs Avance', 'Optimización', 'Fecha de inicio', 'Fecha de término','Empresa'), ',');

        if(intval($municipio_id) !== 0) {
            $id_municipio = intval($municipio_id);
            $municipio = " and vnt_municipio <> null and vnt_municipio.id = $id_municipio";
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT pmo_proyecto.id,vnt_recurso.nombre as proyecto, pmo_proyecto.nombre as project, pmo_proyecto.presupuesto_actual, (select sum(pmo_actividades.costo) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), (select sum(pmo_actividades.presupuesto_inicial) as presupuesto_inicial_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as lider, 
        (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or (fin_gastos.fecha_ejercido is not null and fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO'))) as cambios),
        (select coalesce(sum(cambios.suma), 0) as recurso_solicitado from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'CREADO')) as cambios), pmo_proyecto.inicio as fecha_inicio, pmo_proyecto.fin as fecha_termino, 
        (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = pmo_proyecto.empresa_id), vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, pmo_proyecto.created 
        FROM pmo_proyecto
        inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." and pmo_proyecto.year = '$year'
        inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id 
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by pmo_proyecto.nombre";

        // print_r($sql);
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];
            /* foreach ($arreglo as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->proyecto = $elemento['proyecto'];
                $s->project = $elemento['project'];
                $s->presupuesto_actual = number_format(floatval($elemento['presupuesto_actual']),2,'.',',');
                $s->estado = $elemento['estado'];
                $s->municipio = $elemento['municipio'];
                $s->lider = $elemento['lider'];
                if (intval($elemento['avance_real'] > 0)) {
                    $s->porcentaje_avance = round((intval($elemento['avance_real']) * 100) / (intval($elemento['actividades_level_1']) * 100), 0);
                } else {
                    $s->porcentaje_avance = 0;
                }
                $s->recurso_ejercido = number_format(floatval($elemento['recurso_ejercido']),2,'.',',');
                $s->recurso_por_ejercer = number_format(floatval($elemento['presupuesto_actual'] - $elemento['recurso_ejercido']),2,'.',',');
                if ($elemento['presupuesto_actual'] > 0 &&  $elemento['recurso_ejercido'] > 0) {
                    $s->porcentaje_recurso_ejercido = round((floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actual'])), 0);
                } else {
                    $s->porcentaje_recurso_ejercido = 0;
                }
                $s->optimizacion = 100 - $s->porcentaje_recurso_ejercido;
                $s->fecha_inicio = $elemento['fecha_inicio'];
                $s->fecha_termino = $elemento['fecha_termino'];
                $s->empresa = $elemento['empresa'];
                array_push($nuevo,$s);
            } */

            foreach($arreglo as $elemento){
                if (intval($elemento['avance_real'] > 0)) {
                    $avance_real = round((intval($elemento['avance_real']) * 100) / (intval($elemento['actividades_level_1']) * 100), 0);
                } else {
                    $avance_real = 0;
                }
                if ($elemento['presupuesto_actividad_principal'] > 0 &&  $elemento['recurso_ejercido'] > 0) {
                    $porcentaje = round((floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal'])), 0);
                    $porcentaje_optimizacion = 100 - $porcentaje;
                    } else {
                        $porcentaje = 0;
                        $porcentaje_optimizacion = 100;
                    }
                fputcsv($fp, [
                    $elemento['proyecto'],
                    $elemento['programa'],
                    $elemento['subprograma'],
                    $elemento['project'],
                    date("Y-m-d H:i:s", strtotime($elemento['created'])),
                    number_format(floatval($elemento['presupuesto_inicial_actividad_principal']),2,'.',','),
                    number_format(floatval($elemento['presupuesto_actividad_principal']),2,'.',','),
                    number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['presupuesto_inicial_actividad_principal']),2,'.',','),
                    $elemento['estado'],
                    $elemento['municipio'],
                    $elemento['lider'],
                    $avance_real.'%',
                    number_format(floatval($elemento['recurso_solicitado']),2,'.',','),
                    number_format(floatval($elemento['recurso_ejercido']),2,'.',','),
                    number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',','),
                    $porcentaje.'% - '.$avance_real.'%',
                    $porcentaje_optimizacion.'%',
                    $elemento['fecha_inicio'],
                    $elemento['fecha_termino'],
                    $elemento['empresa'],
                    ], ',');
            }
            rewind($fp);
            $output = stream_get_contents($fp);
            mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
            fclose($fp);

            $this->response->resetHeaders();
            $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_presupuesto_projects'. '.csv');
            $this->response->setContent($output);
            $this->response->send();
    }

    public function exportCSV_reporteProjectsMonto ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($fp, array('Estado','Cliente','Sucursal','Programa','Project','Monto de la bolsa','Recurso actual','% de Gasto','Utilidad presupuestada','% de Utilidad','Recurso ejercido','Utilidad importe','% Utilidad real', 'Status'), ',');

        if(intval($municipio_id) !== 0) {
            $id_municipio = intval($municipio_id);
            $municipio = " and vnt_municipio <> null and vnt_municipio.id = $id_municipio";
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT pmo_proyecto.id,vnt_recurso.nombre as proyecto, pmo_proyecto.nombre as project, pmo_proyecto.presupuesto_actual, (select sum(pmo_actividades.costo) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), (select sum(pmo_actividades.presupuesto_inicial) as presupuesto_inicial_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id), vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as lider, 
        (select sum(avance) as avance_real from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select count(*) as actividades_level_1 from pmo_actividades where proyecto_id = pmo_proyecto.id and nivel = 1), 
        (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)) as cambios),
        (select coalesce(sum(cambios.suma), 0) as recurso_solicitado from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_solicitudes.proyecto_id = pmo_proyecto.id and (fin_solicitudes.status != 'RECHAZADO' and fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'CREADO')) as cambios), pmo_proyecto.inicio as fecha_inicio, pmo_proyecto.fin as fecha_termino, pmo_proyecto.monto_bolsa,
        (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = pmo_proyecto.empresa_id), vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, pmo_proyecto.created, vnt_clientes.razon_social as cliente, case when pmo_proyecto.finalizado = true then 'FINALIZADO' else 'ACTIVO' end as status_project, (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal
        FROM pmo_proyecto 
        inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto".$lider." and pmo_proyecto.year = '$year'
        inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id 
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by pmo_proyecto.nombre";
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];
            foreach($arreglo as $elemento){
                if (intval($elemento['avance_real'] > 0)) {
                    $avance_real = round((intval($elemento['avance_real']) * 100) / (intval($elemento['actividades_level_1']) * 100), 0);
                } else {
                    $avance_real = 0;
                }
                if ($elemento['presupuesto_actividad_principal'] > 0 &&  $elemento['recurso_ejercido'] > 0) {
                    $porcentaje = round((floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal'])), 0);
                    $porcentaje_optimizacion = 100 - $porcentaje;
                    } else {
                        $porcentaje = 0;
                        $porcentaje_optimizacion = 100;
                    }
                //
                if ($elemento['monto_bolsa'] > 0 &&  $elemento['presupuesto_actividad_principal'] > 0) {
                    $porcentaje_recurso_ejercido = round((floatval($elemento['presupuesto_actividad_principal']) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                    // $s->porcentaje_recurso_ejercido = (floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal']));
                } else {
                    $porcentaje_recurso_ejercido = 0;
                }
                $utilidad_neta = floatval($elemento['monto_bolsa'] - $elemento['presupuesto_actividad_principal']);
                if ($elemento['monto_bolsa'] > 0 &&  $utilidad_neta > 0) {
                    $porcentaje_utilidad = round((floatval($utilidad_neta) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                    // $s->porcentaje_recurso_ejercido = (floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal']));
                } else {
                    $porcentaje_utilidad = 0;
                }
                /////////////////////////////////////
                $importe_c = $elemento['monto_bolsa'] - $elemento['recurso_ejercido'];
                $importe = number_format(floatval($elemento['monto_bolsa'] - $elemento['recurso_ejercido']),2,'.',',');
                if ($importe_c > 0) {
                    $porcentaje_importe = round((floatval($importe_c) * 100) / (floatval($elemento['monto_bolsa'])), 2);
                    // $s->porcentaje_recurso_ejercido = (floatval($elemento['recurso_ejercido']) * 100) / (floatval($elemento['presupuesto_actividad_principal']));
                } else {
                    $porcentaje_importe = 0;
                }
                fputcsv($fp, [
                    $elemento['estado'],
                    // $elemento['municipio'],
                    $elemento['cliente'],
                    $elemento['sucursal'],
                    $elemento['programa'],
                    // $elemento['subprograma'],
                    $elemento['project'],
                    number_format(floatval($elemento['monto_bolsa']),2,'.',','),
                    // date("Y-m-d H:i:s", strtotime($elemento['created'])),
                    // number_format(floatval($elemento['presupuesto_inicial_actividad_principal']),2,'.',','),
                    number_format(floatval($elemento['presupuesto_actividad_principal']),2,'.',','),
                    $porcentaje_recurso_ejercido,
                    // number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['presupuesto_inicial_actividad_principal']),2,'.',','),
                    number_format(floatval($elemento['monto_bolsa'] - $elemento['presupuesto_actividad_principal']),2,'.',','),
                    $porcentaje_utilidad,
                    // $elemento['lider'],
                    // $avance_real.'%',
                    // number_format(floatval($elemento['recurso_solicitado']),2,'.',','),
                    number_format(floatval($elemento['recurso_ejercido']),2,'.',','),
                    // number_format(floatval($elemento['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',','),
                    // $porcentaje.'% - '.$avance_real.'%',
                    // $porcentaje_optimizacion.'%',
                    // $elemento['fecha_inicio'],
                    // $elemento['fecha_termino'],
                    // $elemento['empresa'],
                    $importe,
                    $porcentaje_importe,
                    $elemento['status_project']
                    ], ',');
            }
            rewind($fp);
            $output = stream_get_contents($fp);
            mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
            fclose($fp);

            $this->response->resetHeaders();
            $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_utilidades_projects'. '.csv');
            $this->response->setContent($output);
            $this->response->send();
    }

    public function exportCSV_reporteProjectsUtilidad ($lider_id, $empresa_id, $estado_id, $municipio_id, $year) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Creador','Estado','Municipio','Código','Proyecto','Programa','Subprograma','Cliente','Monto de la bolsa','Presupuesto','Utilidad bruta','% Utilidad','% Gasto','Recurso ejercido','Recurso por ejercer','% Presupuesto ejercido', '% Presupuesto por ejercer'), ',');

        if(intval($municipio_id) !== 0) {
            $id_municipio = intval($municipio_id);
            $municipio = " and vnt_municipio <> null and vnt_municipio.id = $id_municipio";
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
            $empresa = " and vnt_empresa.id = $id_empresa";
        } else {
            $empresa = "";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $lider = " and pmo_proyecto.lider_proyecto = $id_lider";
        } else {
            $lider = "";
        }

        $sql = "SELECT vnt_recurso.id,vnt_recurso.nombre as proyecto, vnt_recurso.codigo,(select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),vnt_programa.nombre as programa, vnt_subprograma.nombre as subprograma, vnt_recurso.monto as monto_bolsa, vnt_recurso.id as proyecto_id_suma, vnt_estado.nombre as estado, vnt_municipio.nombre as municipio, sys_users.nickname as creador, vnt_clientes.razon_social as cliente
        FROM vnt_recurso
        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id and vnt_recurso.year = '$year'
        inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id".$estado."
        inner join vnt_subprograma on vnt_subprograma.id = vnt_recurso.subprograma_id
        inner join vnt_programa on vnt_programa.id = vnt_subprograma.programa_id
        inner join sys_users on sys_users.id = vnt_recurso.created_by
        left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id".$municipio;

        $sql = $sql . " order by vnt_recurso.nombre";

        // print_r($sql);
        $arreglo = $this->db->query($sql)->fetchAll();

        $nuevo = [];

            foreach($arreglo as $elemento){
                $id = $elemento['id'];
                $recurso = $elemento['id'];
                $sql_u = "SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
                    inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
                    and pmo_proyecto.recurso_id = $recurso and nivel = '1'";
                $u = $this->db->query($sql_u)->fetchAll();

                $monto_bolsa = number_format(floatval($elemento['monto_bolsa']),2,'.',',');
                if (count($u) > 0) {
                    $utilidad_projects_validar = floatval($elemento['monto_bolsa'] - $u[0]['presupuesto_actividad_principal']);
                    $utilidad_projects = number_format(floatval($elemento['monto_bolsa'] - $u[0]['presupuesto_actividad_principal']),2,'.',',');
                } else {
                    $utilidad_projects = 0;
                }
                $presupuesto_actividad_principal = floatval($u[0]['presupuesto_actividad_principal']);
                $monto_bolsa_validar = $elemento['monto_bolsa'];
                //
                if (floatval($monto_bolsa_validar) > 0 && $utilidad_projects_validar !== 0) {
                    $utilidad_porcentaje = round(($utilidad_projects_validar/$monto_bolsa_validar)*100, 2);
                } else {
                    $utilidad_porcentaje = 0;
                }
                if (floatval($monto_bolsa_validar) > 0 && $presupuesto_actividad_principal !== 0) {
                    $gasto_porcentaje = round(($presupuesto_actividad_principal/$monto_bolsa_validar)*100, 2);
                } else {
                    $gasto_porcentaje = 0;
                }
                if ($elemento['recurso_ejercido'] > 0 && $presupuesto_actividad_principal !== 0) {
                    $ejercido_porcentaje = round(($elemento['recurso_ejercido']/$presupuesto_actividad_principal)*100, 2);
                } else {
                    $ejercido_porcentaje = 0;
                }
                if (floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']) > 0 && $presupuesto_actividad_principal !== 0) {
                    $ejercer_porcentaje = round((floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido'])/$presupuesto_actividad_principal)*100, 2);
                } else {
                    $ejercer_porcentaje = 0;
                }

                fputcsv($fp, [
                    $elemento['creador'],
                    $elemento['estado'],
                    $elemento['municipio'],
                    $elemento['codigo'],
                    $elemento['proyecto'],
                    $elemento['programa'],
                    $elemento['subprograma'],
                    $elemento['cliente'],
                    $monto_bolsa,
                    number_format(floatval($u[0]['presupuesto_actividad_principal']),2,'.',','),
                    $utilidad_projects,
                    $utilidad_porcentaje,
                    $gasto_porcentaje,
                    number_format(floatval($elemento['recurso_ejercido']),2,'.',','),
                    number_format(floatval($u[0]['presupuesto_actividad_principal'] - $elemento['recurso_ejercido']),2,'.',','),
                    $ejercido_porcentaje,
                    $ejercer_porcentaje
                    ], ',');
            }
            rewind($fp);
            $output = stream_get_contents($fp);
            mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
            fclose($fp);

            $this->response->resetHeaders();
            $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_presupuesto_projects'. '.csv');
            $this->response->setContent($output);
            $this->response->send();
    }

    public function exportCSV_gastos ($recurso, $proyecto, $proveedor_id, $empresa_id, $lider_id, $concepto_id, $subconcepto_id, $factura, $status, $fecha_inicio, $fecha_fin, $role, $user_id, $comprobado) {
        $content = $this->content;

        $sql_auxiliar = "";
        if ($role === 'Auxiliar') {
            $sql_auxiliar = " AND
            ((exists (select pmo_colaboradores.proyecto_id 
                     from pmo_colaboradores 
                     where pmo_colaboradores.proyecto_id = fin_solicitudes.proyecto_id and pmo_colaboradores.usuario_id = $user_id))
            or (exists (select pmo_autorizadores.proyecto_id 
                     from pmo_autorizadores 
                     where pmo_autorizadores.proyecto_id = fin_solicitudes.proyecto_id and pmo_autorizadores.usuario_id = $user_id))
            or (exists (select pmo_responsable_pagos.proyecto_id 
                     from pmo_responsable_pagos 
                     where pmo_responsable_pagos.proyecto_id = fin_solicitudes.proyecto_id and pmo_responsable_pagos.usuario_id = $user_id))) ";
        }

        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Líder','Project','Cliente', 'Sucursal', 'Presupuesto comisionado', 'Status','Colaborador', 'Clave/Proyecto', 'Proyecto', 'Tarea','# Solicitud','Fecha creado', 'Fecha de pago', 'Concepto general', 'Subconcepto','Beneficiario', 'Concepto específico', 'Empresa','Subempresa', 'Banco beneficiario', 'Cuenta beneficiaria','Subtotal','Monto iva', 'Monto total','Monto comisión','Total + comisión', 'Tipo', 'Concluido', 'No. Factura','Monto cobrado','Porcentaje cobrado', 'Monto comprobado', 'Factura', 'Posible feha de pago', 'Mes', 'Rubro'), ',');

        $sql = "SELECT sys_users.nickname as colaborador,vnt_recurso.codigo,vnt_recurso.nombre as nombre_proyecto,pmo_actividades.nombre,fin_solicitudes.id,
            (SELECT fecha_autorizacion FROM public.fin_solicitudes_autorizador where solicitud_id = fin_solicitudes.id
            order by id desc limit 1) as fecha_pago,(select nombre from vnt_concepto where fin_gastos.concepto_id = vnt_concepto.id) as concepto,fin_gastos.concepto_id as id_concepto,(select nombre from vnt_subconcepto where fin_gastos.subconcepto_id = vnt_subconcepto.id) as subconcepto,fin_gastos.subconcepto_id as id_subconcepto, pmo_proveedores.razon_social, fin_gastos.descripcion,
            vnt_empresa.razon_social as empresa, pmo_proyecto.id as id_project,pmo_proyecto.nombre as nombre_project,
            case
            when fin_solicitudes.factura = true then 'SI'
            when fin_solicitudes.factura = false then 'NO' end as solicitud_facturada,
            case 
            when fin_solicitudes.status = 'PAGADO' then 'EGRESOS'
            when fin_solicitudes.status != 'PAGADO' then 'PENDIENTE DE PAGO' end as egresos,
            case
            when fin_solicitudes.iva = true then 'CON FACTURA'
            when fin_solicitudes.iva = false then 'SIN FACTURA' end as factura,
            case 
            when fin_solicitudes.comprobado = true then 'TOTAL'
            when fin_solicitudes.comprobado = false then 'NO COMPROBADO' end as monto_comprobado,
            case 
            when (fin_gastos.pago ='banco1' or fin_gastos.pago = 'tarjeta1') then pmo_proveedores.banco  
            when (fin_gastos.pago ='banco2' or fin_gastos.pago = 'tarjeta2') then pmo_proveedores.banco2
            when (fin_gastos.pago ='banco3' or fin_gastos.pago = 'tarjeta3') then pmo_proveedores.banco3
            when (fin_gastos.pago ='banco4' or fin_gastos.pago = 'tarjeta4') then pmo_proveedores.banco4
            when fin_gastos.pago = 'toka' then 'TOKA' end as banco,
            --case 
            --when fin_gastos.pago ='banco1' then pmo_proveedores.clabe  
            --when fin_gastos.pago ='banco2' then pmo_proveedores.clabe2
            --when fin_gastos.pago ='banco3' then pmo_proveedores.clabe3
            --when fin_gastos.pago ='banco4' then pmo_proveedores.clabe4
            --when fin_gastos.pago ='tarjeta1' then pmo_proveedores.tarjeta1  
            --when fin_gastos.pago ='tarjeta2' then pmo_proveedores.tarjeta2
            --when fin_gastos.pago ='tarjeta3' then pmo_proveedores.tarjeta3
            --when fin_gastos.pago ='tarjeta4' then pmo_proveedores.tarjeta4
            --when fin_gastos.pago ='toka' then pmo_proveedores.toka end as clabe,
            fin_gastos.cuenta_pago as clabe,
            -- fin_gastos.monto as total_sin_iva,fin_gastos.monto + (fin_gastos.monto * 0.16) as total_con_iva,
            case 
            when fin_gastos.fecha_ejercido is not null then 'CONCLUIDO' else 'NO CONCLUIDO' end as estado,
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * 0.16)) else fin_gastos.monto end as total,
            case
            WHEN date_part('month',pmo_proyecto.inicio) = 1 THEN 'ENERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 2 THEN  'FEBRERO'
            WHEN date_part('month',pmo_proyecto.inicio) = 3 THEN 'MARZO' 
            WHEN date_part('month',pmo_proyecto.inicio) = 4 THEN 'ABRIL' 
            WHEN date_part('month',pmo_proyecto.inicio) = 5 THEN 'MAYO'
            WHEN date_part('month',pmo_proyecto.inicio) = 6 THEN 'JUNIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 7 THEN 'JULIO'
            WHEN date_part('month',pmo_proyecto.inicio) = 8 THEN 'AGOSTO'
            WHEN date_part('month',pmo_proyecto.inicio) = 9 THEN 'SEPTIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 10 THEN 'OCTUBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 11 THEN 'NOVIEMBRE'
            WHEN date_part('month',pmo_proyecto.inicio) = 12 THEN 'DICIEMBRE'
            end as mes,vnt_recurso.rubro as rubro,
            (select razon_social from vnt_empresa where fin_solicitudes.subempresa_id = vnt_empresa.id) as subempresa, (select nickname from sys_users inner join pmo_proyecto on sys_users.id = pmo_proyecto.lider_proyecto inner join fin_solicitudes on fin_solicitudes.proyecto_id = pmo_proyecto.id and fin_solicitudes.id = fin_gastos.solicitud_id) as lider, fin_solicitudes.status,fin_solicitudes.fecha_creado, vnt_clientes.razon_social as cliente, fin_gastos.monto as subtotal, 
            case
            when fin_solicitudes.iva = true then (fin_gastos.monto * 0.16) else 0 end as iva, fin_gastos.sumatoria_factura as monto_cobrado,
            case
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto + (fin_gastos.monto * 0.16)))
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura > 0) then ((fin_gastos.sumatoria_factura * 100) / (fin_gastos.monto))
            when (fin_solicitudes.iva = true and fin_gastos.sumatoria_factura = 0) then 0
            when (fin_solicitudes.iva = false and fin_gastos.sumatoria_factura = 0) then 0 end as porcentaje_cobrado,
            case
            when fin_solicitudes.subempresa_id is not null then (fin_gastos.monto * (fin_gastos.comision/100))
            when fin_solicitudes.subempresa_id is null then 0
            end as cantidad_comision,
            case
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)) + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)))
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is null then (fin_gastos.monto + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is null then fin_gastos.monto
            when fin_solicitudes.subempresa_id is null then 0
            end as total_comision, (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal, (select nombre from pmo_proyecto where id = fin_solicitudes.proyecto_comision_id) as proyecto_comision         
            from fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
            inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
            inner join sys_users on fin_solicitudes.creador = sys_users.id {$sql_auxiliar}
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join fin_tipos_gastos on fin_tipos_gastos.id = fin_gastos.tipo
            inner join pmo_proveedores on pmo_proveedores.id = fin_gastos.proveedor_id
            inner join vnt_empresa on vnt_empresa.id = fin_solicitudes.empresa_id";
        
        if(intval($recurso) !== 0) {
            $id_recurso = intval($recurso);
            $sql = $sql . " and vnt_recurso.id = $id_recurso";
        }
        if(intval($proyecto) !== 0) {
            $id_proyecto = intval($proyecto);
            $sql = $sql . " and pmo_proyecto.id = $id_proyecto";
        }
        if(intval($proveedor_id) !== 0) {
            $id_proveedor = intval($proveedor_id);
            $sql = $sql . " and pmo_proveedores.id = $id_proveedor";
        }
        if(intval($empresa_id) !== 0) {
            $id_empresa = intval($empresa_id);
            $sql = $sql . " and vnt_empresa.id = $id_empresa";
        }
        if(intval($lider_id) !== 0) {
            $id_lider = intval($lider_id);
            $sql = $sql . " and pmo_proyecto.lider_proyecto = $id_lider";
        }
        if($factura === 'SI') {
            $sql = $sql . " and fin_solicitudes.iva = true";
        }
        if($factura === 'NO') {
            $sql = $sql . " and fin_solicitudes.iva = false";
        }
        if($comprobado === 'SI') {
            $sql = $sql . " and fin_solicitudes.comprobado = true";
        }
        if($comprobado === 'NO') {
            $sql = $sql . " and fin_solicitudes.comprobado = false";
        }
        if($status !== 'all') {
            $sql = $sql . " and fin_solicitudes.status = '$status'";
        }
        if($fecha_inicio !== 'null') {
            $fecha_inicio = date('Y-m-d',strtotime($fecha_inicio));
            // $sql = $sql . " and fin_solicitudes.fecha_creado >= '$fecha_inicio'";
        }
        if($fecha_fin !== 'null') {
            $fecha_fin = date('Y-m-d',strtotime($fecha_fin));
            // $sql = $sql . " and fin_solicitudes.fecha_creado <= '$fecha_fin'";
        }
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $sql = $sql . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
        }
        if(intval($concepto_id) > 0) {
            $concepto_id = intval($concepto_id);
            $sql = $sql = $sql . " and fin_gastos.concepto_id = $concepto_id";
        }
        if(intval($subconcepto_id) > 0) {
            $subconcepto_id = intval($subconcepto_id);
            $sql = $sql = $sql . " and fin_gastos.subconcepto_id = $subconcepto_id";
        }
        $sql_final = $sql . " order by fin_solicitudes.id";

        $arreglo = $this->db->query($sql_final)->fetchAll();   
        foreach ($arreglo as $elemento) {
            $id_project = $elemento['id_project'];

                    fputcsv($fp, [
                        $elemento['lider'],
                        $elemento['nombre_project'],
                        $elemento['cliente'],
                        $elemento['sucursal'],
                        $elemento['proyecto_comision'],
                        $elemento['status'],
                        $elemento['colaborador'],
                        $elemento['codigo'],
                        $elemento['nombre_proyecto'],
                        $elemento['nombre'],
                        $elemento['id'],
                        $elemento['fecha_creado'],
                        $elemento['fecha_pago'],
                        $elemento['concepto'],
                        $elemento['subconcepto'],
                        $elemento['razon_social'],
                        $elemento['descripcion'],
                        $elemento['empresa'],
                        $elemento['subempresa'],
                        $elemento['banco'],
                        'CLABE '.$elemento['clabe'],
                        number_format(floatval($elemento['subtotal']),2,'.',','),
                        number_format(floatval($elemento['iva']),2,'.',','),
                        number_format(floatval($elemento['total']),2,'.',','),
                        number_format(floatval($elemento['cantidad_comision']),2,'.',','),
                        number_format(floatval($elemento['total_comision']),2,'.',','),
                        $elemento['egresos'],
                        $elemento['estado'],
                        $elemento['factura'],
                        number_format(floatval($elemento['total']),2,'.',','),
                        round($elemento['porcentaje_cobrado'],2),
                        $elemento['monto_comprobado'],
                        $elemento['solicitud_facturada'],
                        $elemento['fecha_pago'],
                        $elemento['mes'],
                        $elemento['rubro']
                        ], ',');
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_gastos'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function getReporteCobranza ($recurso, $cliente, $empresa, $year) {
        $year_anterior = intval($year) - 1;
        $sql_recurso = "";
        $sql_cliente = "";
        $sql_empresa = "";
        $sql_remisiones = "";
        if(intval($cliente) !== 0) {
            $id_cliente = intval($cliente);
            $sql_cliente = " and vnt_clientes.id = $id_cliente";
        }
        if(intval($empresa) !== 0) {
            $id_empresa = intval($empresa);
            $sql_empresa = " and vnt_empresa.id = $id_empresa";
        }
        if(intval($recurso) !== 0) {
            $id_recurso = intval($recurso);
            $sql_recurso = " and vnt_programa.id = $id_recurso";
        } else {
            $sql_remisiones = " UNION ALL 
                SELECT vnt_remisiones.id, date_part('year', vnt_remisiones.fecha_venta) || '' as anio,(select vnt_municipio.nombre 
                from vnt_municipio where vnt_municipio.id = vnt_clientes.municipio_id) as municipio,vnt_estado.nombre as estado, 'NO APLICA' as aliado,
                'NO APLICA' as num_contrato, vnt_remisiones.folio || ' - ' || vnt_remisiones_facturas.name as nombre, 'NO APLICA' as recurso, vnt_clientes.razon_social
                as cliente,vnt_empresa.razon_social as empresa, vnt_remisiones_facturas.monto_factura as monto_total, 0 as contratos_impact,
                vnt_remisiones_facturas.monto_factura as facturado_impact,(select coalesce((select sum(x.monto_factura) 
                 from vnt_remisiones_facturas as x where x.factura_relacionada = vnt_remisiones_facturas.id and x.remision_id > 0 and x.cancelado = false), 0)) as facturado_notas,
                (select coalesce((select sum(vnt_remisiones_abonos.monto) 
                 from vnt_remisiones_abonos
                 inner join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones_abonos.factura_id
                 and vnt_remisiones_facturas.remision_id = vnt_remisiones.id), 0)) as facturas_abonadas, 'REMISIONES' as tipo, 0 as facturado_impact_cancelado, vnt_remisiones_facturas.monto_factura as  facturado_total, 0 as descontado
                from vnt_remisiones
                join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id {$sql_cliente} and vnt_remisiones.tipo = 'HISTÓRICO' and vnt_remisiones.cancelado = false
                and (date_part('year', vnt_remisiones.fecha_venta) = '$year' or date_part('year', vnt_remisiones.fecha_venta) = '$year_anterior')
                join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones.id and vnt_remisiones_facturas.amortizacion = false and vnt_remisiones_facturas.cancelado = false
                join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
                join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id {$sql_empresa}";
        }

        $sql = "SELECT vnt_contrato.id, vnt_contrato.year || '' as anio, (select vnt_municipio.nombre from vnt_municipio where vnt_municipio.id = vnt_clientes.municipio_id) as municipio, vnt_estado.nombre as estado, vnt_recurso.aliado,vnt_contrato.num_contrato,vnt_contrato.nombre, vnt_programa.nombre as recurso,vnt_clientes.razon_social as cliente, vnt_empresa.razon_social as empresa, vnt_contrato.monto_total, (vnt_contrato.monto_total - (vnt_contrato.monto_total * vnt_contrato.porcentaje)) as contratos_impact, 
            (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and sys_documents.name not like '%NOTA DE CR%' and vnt_contratos_facturas.cancelada = false
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id), 0)) as facturado_impact,
            (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and (sys_documents.name like '%AMORTIZA%' or sys_documents.name like '%NOTA DE CR%') and vnt_contratos_facturas.cancelada = false), 0)) as facturado_notas,
             -- añadidos los abonos de las amortizaciones
             (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and (sys_documents.name like '%AMORTIZA%' or sys_documents.name like '%NOTA DE CR%') and vnt_contratos_facturas.factura_relacionada is not null and vnt_contratos_facturas.cancelada = false), 0)) +
             (select coalesce((select sum(vnt_facturas_abonos.monto)
             from vnt_facturas_abonos
             inner join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
             and vnt_contratos_facturas.contrato_id = vnt_contrato.id
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and vnt_contratos_facturas.cancelada = false), 0)) as facturas_abonadas, 'CONTRATOS' as tipo,
             0 as facturado_impact_cancelado,
             (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and vnt_contratos_facturas.cancelada = false and vnt_contratos_facturas.factura_relacionada is null), 0)) as facturado_total,
             (SELECT coalesce(sum(x.monto_factura),0)
            from vnt_contratos_facturas as x 
            inner join vnt_contratos_facturas on x.factura_relacionada = vnt_contratos_facturas.uuid
            and x.cancelada = false and vnt_contratos_facturas.contrato_id = vnt_contrato.id) as descontado
            from vnt_contrato
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id and (vnt_contrato.year = '$year' or vnt_contrato.year = '$year_anterior')
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id {$sql_cliente}
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id {$sql_recurso}
            join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
            join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id {$sql_empresa}".$sql_remisiones;

        $sql_final = $sql . " order by anio,tipo";
        $arreglo = $this->db->query($sql_final)->fetchAll();
        $nuevo = [];
            foreach ($arreglo as $elemento){
                $falta_cobrar = 0;
                $contrato=(object)array();
                $contrato->id = $elemento['id'];
                $contrato->anio = $elemento['anio'];
                $contrato->municipio = $elemento['municipio'];
                $contrato->estado = $elemento['estado'];
                $contrato->aliado = $elemento['aliado'];
                $contrato->num_contrato = $elemento['num_contrato'];
                $contrato->nombre = $elemento['nombre'];
                $contrato->recurso = $elemento['recurso'];
                $contrato->cliente = $elemento['cliente'];
                $contrato->empresa = $elemento['empresa'];
                $contrato->monto_total = number_format(floatval($elemento['monto_total']),2,'.',',');
                $contrato->contratos_impact = number_format(floatval($elemento['contratos_impact']),2,'.',',');
                $contrato->facturado_impact = number_format(floatval($elemento['facturado_impact'] - $elemento['facturado_notas'] - $elemento['facturado_impact_cancelado']),2,'.',',');
                $facturado_impact = floatval($elemento['facturado_impact'] - $elemento['facturado_notas'] - $elemento['facturado_impact_cancelado']);
                // $contrato->facturado_notas = number_format(floatval($elemento['facturado_notas']),2,'.',',');
                $contrato->facturado_total = number_format(floatval($elemento['facturado_impact'] - $elemento['facturado_notas']),2,'.',',');
                if (floatval($elemento['contratos_impact']) > 0) {
                    $contrato->porcentaje_facturado = round(($facturado_impact * 100)/$elemento['monto_total'],2);
                } else {
                    $contrato->porcentaje_facturado = 0;
                }
                $falta_facturar = $elemento['monto_total'] - $facturado_impact;
                $falta_cobrar = $elemento['facturado_total'] - $elemento['facturas_abonadas'];
                if ($falta_facturar < 0) {
                    $falta_facturar = 0;
                }
                if ($elemento['facturado_impact_cancelado'] > 0) {
                    $falta_facturar = $falta_facturar - $elemento['facturado_impact_cancelado'];
                }
                if ($elemento['tipo'] == 'REMISIONES') {
                    $contrato->porcentaje_facturado = 100;
                    $falta_facturar = 0;
                    if ($facturado_impact == 0) {
                        $falta_cobrar = 0;
                    }
                    if ($elemento['facturado_notas'] > 0) {

                    }
                }
                $contrato->falta_facturar = number_format(floatval($falta_facturar),2,'.',',');
                $contrato->falta_cobrar = number_format(floatval($falta_cobrar),2,'.',',');
                $contrato->cobrado = number_format(floatval($facturado_impact - $falta_cobrar),2,'.',',');
                $contrato->tipo = $elemento['tipo'];
                if ($elemento['tipo'] == 'REMISIONES') {
                    if ($elemento['facturado_notas'] > 0) {
                        $contrato->falta_cobrar = number_format(floatval($elemento['facturado_impact'] - ($elemento['facturas_abonadas'] + $elemento['facturado_notas'])),2,'.',',');
                        $contrato->cobrado = number_format(floatval($elemento['facturas_abonadas']),2,'.',',');
                    }
                    $contrato->facturado_impact = number_format(floatval($elemento['facturado_impact']),2,'.',',');
                    $contrato->facturado_notas = number_format(floatval($elemento['facturado_notas']),2,'.',',');
                } else {
                    if ($elemento['descontado'] > 0) {
                        $contrato->facturado_notas = number_format(floatval($elemento['descontado']),2,'.',',');
                        // $contrato->falta_cobrar = number_format(floatval($falta_cobrar - $elemento['descontado']),2,'.',',');
                        if (floatval($elemento['contratos_impact']) > 0) {
                            $contrato->porcentaje_facturado = round((($facturado_impact + $elemento['descontado']) * 100)/$elemento['monto_total'],2);
                        } else {
                            $contrato->porcentaje_facturado = 0;
                        }
                    } else {
                        $contrato->facturado_notas = number_format(floatval(0),2,'.',',');
                    }
                }
                if ($elemento['anio'] == $year) {
                    array_push($nuevo,$contrato);
                } else {
                    if ($falta_cobrar > 1 && $falta_cobrar > 0) {
                        array_push($nuevo,$contrato);
                    }
                }
                
            }
        $this->content['reportes_cobranza'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_cobranza ($recurso, $cliente, $empresa, $year) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Tipo','Año', 'Municipio', 'Estado', 'Contacto', 'Número de contrato', 'Nombre', 'Recurso', 'Cliente', 'Empresa', 'Monto total', 'Contratos IMPACT', 'Facturado IMPACT','% Facturado', 'Falta facturar', 'Cobrado', 'Falta cobrar', 'Descontado'), ',');

        $sql_recurso = "";
        $sql_cliente = "";
        $sql_empresa = "";
        $sql_remisiones = "";
        $year_anterior = intval($year) - 1;
        if(intval($cliente) !== 0) {
            $id_cliente = intval($cliente);
            $sql_cliente = " and vnt_clientes.id = $id_cliente";
        }
        if(intval($empresa) !== 0) {
            $id_empresa = intval($empresa);
            $sql_empresa = " and vnt_empresa.id = $id_empresa";
        }
        if(intval($recurso) !== 0) {
            $id_recurso = intval($recurso);
            $sql_recurso = " and vnt_programa.id = $id_recurso";
        } else {
            $sql_remisiones = " UNION ALL 
                SELECT vnt_remisiones.id, date_part('year', vnt_remisiones.fecha_venta) || '' as anio,(select vnt_municipio.nombre 
                from vnt_municipio where vnt_municipio.id = vnt_clientes.municipio_id) as municipio,vnt_estado.nombre as estado, 'NO APLICA' as aliado,
                'NO APLICA' as num_contrato, vnt_remisiones.folio || ' - ' || vnt_remisiones_facturas.name as nombre, 'NO APLICA' as recurso, vnt_clientes.razon_social
                as cliente,vnt_empresa.razon_social as empresa, vnt_remisiones_facturas.monto_factura as monto_total, 0 as contratos_impact,
                vnt_remisiones_facturas.monto_factura as facturado_impact,(select coalesce((select sum(x.monto_factura) 
                 from vnt_remisiones_facturas as x where x.factura_relacionada = vnt_remisiones_facturas.id and x.remision_id > 0 and x.cancelado = false), 0)) as facturado_notas,
                (select coalesce((select sum(vnt_remisiones_abonos.monto) 
                 from vnt_remisiones_abonos
                 inner join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones_abonos.factura_id
                 and vnt_remisiones_facturas.remision_id = vnt_remisiones.id), 0)) as facturas_abonadas, 'REMISIONES' as tipo, 0 as facturado_impact_cancelado, vnt_remisiones_facturas.monto_factura as  facturado_total, 0 as descontado
                from vnt_remisiones
                join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id {$sql_cliente} and vnt_remisiones.tipo = 'HISTÓRICO' and vnt_remisiones.cancelado = false
                and (date_part('year', vnt_remisiones.fecha_venta) = '$year' or date_part('year', vnt_remisiones.fecha_venta) = '$year_anterior')
                join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones.id and vnt_remisiones_facturas.amortizacion = false and vnt_remisiones_facturas.cancelado = false
                join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
                join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id {$sql_empresa}";
        }

        $sql = "SELECT vnt_contrato.id, vnt_contrato.year || '' as anio, (select vnt_municipio.nombre from vnt_municipio where vnt_municipio.id = vnt_clientes.municipio_id) as municipio, vnt_estado.nombre as estado, vnt_recurso.aliado,vnt_contrato.num_contrato,vnt_contrato.nombre, vnt_programa.nombre as recurso,vnt_clientes.razon_social as cliente, vnt_empresa.razon_social as empresa, vnt_contrato.monto_total, (vnt_contrato.monto_total - (vnt_contrato.monto_total * vnt_contrato.porcentaje)) as contratos_impact, 
            (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and sys_documents.name not like '%NOTA DE CR%' and vnt_contratos_facturas.cancelada = false
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id), 0)) as facturado_impact,
            (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and (sys_documents.name like '%AMORTIZA%' or sys_documents.name like '%NOTA DE CR%') and vnt_contratos_facturas.cancelada = false), 0)) as facturado_notas,
             -- añadidos los abonos de las amortizaciones
             (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and (sys_documents.name like '%AMORTIZA%' or sys_documents.name like '%NOTA DE CR%') and vnt_contratos_facturas.factura_relacionada is not null and vnt_contratos_facturas.cancelada = false), 0)) +
             (select coalesce((select sum(vnt_facturas_abonos.monto)
             from vnt_facturas_abonos
             inner join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id
             and vnt_contratos_facturas.contrato_id = vnt_contrato.id
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and vnt_contratos_facturas.cancelada = false), 0)) as facturas_abonadas, 'CONTRATOS' as tipo,
             0 as facturado_impact_cancelado,
             (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
             from vnt_contratos_facturas 
             inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
             where vnt_contratos_facturas.contrato_id = vnt_contrato.id and vnt_contratos_facturas.cancelada = false and vnt_contratos_facturas.factura_relacionada is null), 0)) as facturado_total,
             (SELECT coalesce(sum(x.monto_factura),0)
            from vnt_contratos_facturas as x 
            inner join vnt_contratos_facturas on x.factura_relacionada = vnt_contratos_facturas.uuid
            and x.cancelada = false and vnt_contratos_facturas.contrato_id = vnt_contrato.id) as descontado
            from vnt_contrato
            join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id and (vnt_contrato.year = '$year' or vnt_contrato.year = '$year_anterior')
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id {$sql_cliente}
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id {$sql_recurso}
            join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
            join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id {$sql_empresa}".$sql_remisiones;
            $sql_final = $sql . " order by anio, tipo";
        $arreglo = $this->db->query($sql_final)->fetchAll();
        foreach($arreglo as $elemento){
            if ($elemento['tipo'] == 'REMISIONES') {
                $facturado_impact = floatval($elemento['facturado_impact']);
            } else {
                $facturado_impact = floatval($elemento['facturado_impact'] - $elemento['facturado_notas'] - $elemento['facturado_impact_cancelado']);
            }
            if (floatval($elemento['contratos_impact']) > 0) {
                $porcentaje_facturado = round(($facturado_impact * 100)/$elemento['monto_total'],2);
            } else {
                $porcentaje_facturado = 0;
            }
            $falta_facturar = $elemento['monto_total'] - $facturado_impact;
            $falta_cobrar = $elemento['facturado_total'] - $elemento['facturas_abonadas'];
            //
            //
            if ($falta_facturar < 0) {
                $falta_facturar = 0;
            }
            if ($elemento['facturado_impact_cancelado'] > 0) {
                $falta_facturar = $falta_facturar - $elemento['facturado_impact_cancelado'];
            }
            if ($elemento['tipo'] == 'REMISIONES') {
                $porcentaje_facturado = 100;
                $falta_facturar = 0;
                if ($facturado_impact == 0) {
                    $falta_cobrar = 0;
                }
            }
            if ($elemento['tipo'] == 'REMISIONES') {
                if ($elemento['facturado_notas'] > 0) {
                    $falta_cobrar_total = number_format(floatval($elemento['facturado_impact'] - ($elemento['facturas_abonadas'] + $elemento['facturado_notas'])),2,'.',',');
                } else {
                    $falta_cobrar_total = $falta_cobrar;
                }
                $cobrado_total = number_format(floatval($elemento['facturas_abonadas']),2,'.',',');
                $descontado = number_format(floatval($elemento['facturado_notas']),2,'.',',');
            } else {
                
                $cobrado_total = number_format(floatval($facturado_impact - $falta_cobrar),2,'.',',');
                $falta_cobrar_total = number_format(floatval($falta_cobrar),2,'.',',');
                if ($elemento['descontado'] > 0) {
                    $descontado = number_format(floatval($elemento['descontado']),2,'.',',');
                    if (floatval($elemento['contratos_impact']) > 0) {
                        $porcentaje_facturado = round((($facturado_impact) * 100)/$elemento['monto_total'],2);
                    } else {
                        $porcentaje_facturado = 0;
                    }
                    // $falta_cobrar_total = number_format(floatval($falta_cobrar - $elemento['descontado']),2,'.',',');
                } else {
                    $descontado = number_format(floatval(0),2,'.',',');
                }
            }
            if ($elemento['anio'] == $year || ($elemento['anio'] == $year_anterior && $falta_cobrar > 1)) {
                fputcsv($fp, [
                $elemento['tipo'],
                $elemento['anio'],
                $elemento['municipio'],
                $elemento['estado'],
                $elemento['aliado'],
                $elemento['num_contrato'],
                $elemento['nombre'],
                $elemento['recurso'],
                $elemento['cliente'],
                $elemento['empresa'],
                number_format(floatval($elemento['monto_total']),2,'.',','),
                number_format(floatval($elemento['contratos_impact']),2,'.',','),
                number_format(floatval($facturado_impact),2,'.',','),
                $porcentaje_facturado,
                number_format(floatval($falta_facturar),2,'.',','),
                $cobrado_total,
                $falta_cobrar_total,
                $descontado
                ], ',');
            }
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_cobranza'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function getReporteProyectos ($estado,$municipio,$programa,$empresa,$contrato) {
        if (intval($estado) !== 0) {
            $where_estado = " and vnt_estado.id = $estado ";
        } else {
            $where_estado = " ";
        }
        if (intval($municipio) !== 0) {
            $where_municipio = " and vnt_municipio.id = $municipio ";
        } else {
            $where_municipio = " ";
        }
        if (intval($programa) !== 0) {
            $where_programa = " and vnt_subprograma.id = $programa ";
        } else {
            $where_programa = " ";
        }
        if (intval($empresa) !== 0) {
            $where_empresa = " and vnt_empresa.id = $empresa ";
        } else {
            $where_empresa = " ";
        }
        if (intval($contrato) !== 0) {
            $where_contrato = " and vnt_contrato.id = $contrato ";
        } else {
            $where_contrato = " ";
        }
        $sql = "SELECT vnt_contrato.monto_total as contrato, vnt_empresa.razon_social as empresa, vnt_subprograma.nombre,vnt_contrato.num_contrato,
        vnt_estado.nombre as estado, (select vnt_municipio.nombre from vnt_municipio where vnt_clientes.municipio_id = vnt_municipio.id) as municipio, vnt_contrato.rep_legal_contrato as lider,
        case when vnt_empresa.razon_social like lower('isspe') then 'SI' else 'NO' end as acad, 
        case when vnt_empresa.razon_social like lower('isspe') then vnt_contrato.monto_total * 0.2 else 0 end as academia,
        case when vnt_empresa.razon_social like lower('isspe') then vnt_contrato.monto_total - (vnt_contrato.monto_total * 0.2) else vnt_contrato.monto_total end as ingresos_impact, sys_users.nickname as created_by, vnt_clientes.razon_social as cliente, vnt_recurso.codigo as codigo, vnt_recurso.nombre as proyecto, vnt_contrato.year,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id), 0)) as facturado_impact,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id and sys_documents.name like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = true), 0)) as facturado_notas,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura)
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = true), 0)) as facturado_impact_cancelado
        from vnt_contrato inner join vnt_empresa on vnt_contrato.empresa_id = vnt_empresa.id".$where_empresa."inner join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id 
        inner join vnt_clientes on vnt_recurso.cliente_id = vnt_clientes.id
        inner join vnt_estado on vnt_clientes.estado_id = vnt_estado.id".$where_estado.$where_municipio."
        inner join vnt_subprograma on vnt_recurso.subprograma_id = vnt_subprograma.id".$where_programa."
        inner join sys_users on sys_users.id = vnt_contrato.created_by
        ".$where_contrato."
        order by vnt_contrato.id";
        $contratos = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($contratos as $elemento){
                $facturado_impact = floatval($elemento['facturado_impact'] - $elemento['facturado_notas'] - $elemento['facturado_impact_cancelado']);
                $s=(object)array();
                $s->num_contrato = $elemento['num_contrato'];
                $s->municipio = $elemento['municipio'];
                $s->nombre = $elemento['nombre'];
                $s->estado = $elemento['estado'];
                $s->empresa = $elemento['empresa'];
                $s->lider = $elemento['lider'];
                $s->acad = $elemento['acad'];
                $s->contrato = number_format(floatval($elemento['contrato']),2,'.',',');
                $s->academia = number_format(floatval($elemento['academia']),2,'.',',');
                $s->ingresos_impact = number_format(floatval($elemento['ingresos_impact']),2,'.',',');
                $s->facturado = number_format(floatval($facturado_impact),2,'.',',');
                $s->created_by = $elemento['created_by'];
                $s->cliente = $elemento['cliente'];
                $s->codigo = $elemento['codigo'];
                $s->year = $elemento['year'];
                $s->proyecto = $elemento['proyecto'];          
                array_push($nuevo,$s);
            }

        $this->content['reportes_contratos'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_proyectos ($estado,$municipio,$programa,$empresa,$contrato) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Año','# Contrato','Código','Proyecto', 'Cliente', 'Municipio', 'Estado', 'Programa','Empresa', 'Líder', 'Contrato', 'Acad','Academia', 'Ingresos IMPACT', 'Creador contrato ', 'Facturado'), ',');

        if (intval($estado) !== 0) {
            $where_estado = " and vnt_estado.id = $estado ";
        } else {
            $where_estado = " ";
        }
        if (intval($municipio) !== 0) {
            $where_municipio = " and vnt_municipio.id = $municipio ";
        } else {
            $where_municipio = " ";
        }
        if (intval($programa) !== 0) {
            $where_programa = " and vnt_subprograma.id = $programa ";
        } else {
            $where_programa = " ";
        }
        if (intval($empresa) !== 0) {
            $where_empresa = " and vnt_empresa.id = $empresa ";
        } else {
            $where_empresa = " ";
        }
        if (intval($contrato) !== 0) {
            $where_contrato = " and vnt_contrato.id = $contrato ";
        } else {
            $where_contrato = " ";
        }
        $sql = "SELECT vnt_contrato.monto_total as contrato, vnt_empresa.razon_social as empresa, vnt_subprograma.nombre,vnt_contrato.num_contrato,
        vnt_estado.nombre as estado, (select vnt_municipio.nombre from vnt_municipio where vnt_clientes.municipio_id = vnt_municipio.id) as municipio, vnt_contrato.rep_legal_contrato as lider,
        case when vnt_empresa.razon_social like lower('isspe') then 'SI' else 'NO' end as acad, 
        case when vnt_empresa.razon_social like lower('isspe') then vnt_contrato.monto_total * 0.2 else 0 end as academia,
        case when vnt_empresa.razon_social like lower('isspe') then vnt_contrato.monto_total - (vnt_contrato.monto_total * 0.2) else vnt_contrato.monto_total end as ingresos_impact, sys_users.nickname as created_by, vnt_clientes.razon_social as cliente, vnt_recurso.codigo, vnt_recurso.nombre as proyecto, vnt_contrato.year,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id), 0)) as facturado_impact,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura) 
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id and sys_documents.name like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = false), 0)) as facturado_notas,
        (select coalesce((select sum(vnt_contratos_facturas.monto_factura)
         from vnt_contratos_facturas 
         inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
         where vnt_contratos_facturas.contrato_id = vnt_contrato.id and sys_documents.name not like '%AMORTIZA%' and vnt_contratos_facturas.cancelada = true), 0)) as facturado_impact_cancelado
        from vnt_contrato inner join vnt_empresa on vnt_contrato.empresa_id = vnt_empresa.id".$where_empresa."inner join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id 
        inner join vnt_clientes on vnt_recurso.cliente_id = vnt_clientes.id
        inner join vnt_estado on vnt_clientes.estado_id = vnt_estado.id".$where_estado.$where_municipio."
        inner join vnt_subprograma on vnt_recurso.subprograma_id = vnt_subprograma.id".$where_programa."
        inner join sys_users on sys_users.id = vnt_contrato.created_by
        ".$where_contrato."
        order by vnt_contrato.id";
        $contratos = $this->db->query($sql)->fetchAll();
 
        foreach($contratos as $elemento){
            $facturado_impact = floatval($elemento['facturado_impact'] - $elemento['facturado_notas'] - $elemento['facturado_impact_cancelado']);
            fputcsv($fp, [
                $elemento['year'],
                $elemento['num_contrato'],
                $elemento['codigo'],
                $elemento['proyecto'],
                $elemento['cliente'],
                $elemento['municipio'],
                $elemento['estado'],
                $elemento['nombre'],
                $elemento['empresa'],
                $elemento['lider'],
                number_format(floatval($elemento['contrato']),2,'.',','),
                $elemento['acad'],
                number_format(floatval($elemento['academia']),2,'.',','),
                number_format(floatval($elemento['ingresos_impact']),2,'.',','),
                $elemento['created_by'],
                number_format(floatval($facturado_impact),2,'.',',')
                ], ',');
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_contratos'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function reporteLicitaciones ($estado, $recurso, $status, $empresa, $year) {
        $sql = "SELECT lic_licitacion.id,lic_licitacion.folio, (select concat (codigo, nombre) from vnt_recurso where vnt_recurso.id = lic_licitacion.recurso_id) as proyecto,lic_licitacion.status, vnt_estado.nombre as entidad, lic_licitacion.titulo as procedimiento, lic_licitacion.descripcion, lic_licitacion.contrato,lic_licitacion.orden_compra,lic_licitacion.monto_inicial, lic_licitacion.monto_adjudicado, lic_licitacion.comprador, (select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = lic_licitacion.empresa_id), lic_licitacion.empresa,lic_licitacion.fecha_inicio, lic_licitacion.fecha_presentacion, lic_licitacion.fecha_fallo, sys_users.nickname, lic_licitacion.responsable, lic_licitacion.responsable_gdp, crm_oportunidades.nombre as oportunidad, crm_oportunidades.status as status_crm, 
            case 
            when lic_licitacion.status = 'CREADA' then lic_licitacion.observaciones_creada 
            when lic_licitacion.status = 'EN PROCESO' then lic_licitacion.observaciones_proceso 
            when lic_licitacion.status = 'PRESENTADA' then lic_licitacion.observaciones_presentada
            when lic_licitacion.status = 'ADJUDICADA' then lic_licitacion.observaciones_adjudicada
            when lic_licitacion.status = 'NO ADJUDICADA' then lic_licitacion.observaciones_no_adjudicada
            when lic_licitacion.status = 'CANCELADA' then lic_licitacion.observaciones_cancelada
            end as observaciones_licitacion
            from lic_licitacion
            inner join vnt_estado on vnt_estado.id = lic_licitacion.entidad_id
            inner join sys_users on sys_users.id = lic_licitacion.created_by and lic_licitacion.year = '$year' and lic_licitacion.status_mostrado = 'ACTIVO'
            left join crm_oportunidades on crm_oportunidades.licitacion_id = lic_licitacion.id";

        if (intval($recurso) > 0) {
            $sql = $sql . " and lic_licitacion.recurso_id = $recurso";
        }
        if (intval($estado) > 0) {
            $sql = $sql . " and lic_licitacion.entidad_id = $estado";
        }
        if (intval($empresa) > 0) {
            $sql = $sql . " and lic_licitacion.empresa_id = $empresa_id";
        }
        if ($status != '0' || $status != 0) {
            $sql = $sql . " and lic_licitacion.status = '$status'";
        }
        // var_dump($status);
        $licitaciones = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($licitaciones as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $id = $elemento['id'];
                $sql_respaldo = "SELECT id, licitacion_id, empresa_id, created, created_by, modified, modified_by, empresa,(select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = lic_respaldo.empresa_id) as empresa_nombre FROM lic_respaldo where licitacion_id = $id";
                $respaldos = $this->db->query($sql_respaldo)->fetchAll();
                if (sizeof($respaldos) === 0) {
                    $s->respaldo_1 = '';
                    $s->respaldo_2 = '';
                    $s->respaldo_3 = '';
                    $s->respaldo_4 = '';
                } else {
                    if (sizeof($respaldos) === 1) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                    }
                    if (sizeof($respaldos) === 2) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                    }
                    if (sizeof($respaldos) == 3) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                        if ($respaldos[2]['empresa_id'] > 0) {
                            $s->respaldo_3 = $respaldos[2]['empresa_nombre'];
                        } else {
                            $s->respaldo_3 = $respaldos[2]['empresa'];
                        }
                    }
                    if (sizeof($respaldos) == 4) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                        if ($respaldos[2]['empresa_id'] > 0) {
                            $s->respaldo_3 = $respaldos[2]['empresa_nombre'];
                        } else {
                            $s->respaldo_3 = $respaldos[2]['empresa'];
                        }
                        if ($respaldos[3]['empresa_id'] > 0) {
                            $s->respaldo_4 = $respaldos[3]['empresa_nombre'];
                        } else {
                            $s->respaldo_4 = $respaldos[3]['empresa'];
                        }
                    }
                }
                $s->folio = $elemento['folio'];
                $s->proyecto = $elemento['proyecto'];
                $s->status = $elemento['status'];
                $s->observaciones_licitacion = $elemento['observaciones_licitacion'];
                $s->entidad = $elemento['entidad'];
                $s->procedimiento = $elemento['procedimiento'];
                $s->descripcion = $elemento['descripcion'];
                $s->contrato = $elemento['contrato'];
                $s->orden_compra = $elemento['orden_compra'];
                $s->monto_inicial = number_format(floatval($elemento['monto_inicial']),2,'.',',');
                $s->monto_adjudicado = number_format(floatval($elemento['monto_adjudicado']),2,'.',',');
                $s->comprador = $elemento['comprador'];
                $s->razon_social = $elemento['razon_social'];          
                $s->empresa = $elemento['empresa'];
                $s->fecha_inicio = $elemento['fecha_inicio'];
                $s->fecha_presentacion = $elemento['fecha_presentacion'];
                $s->fecha_fallo = $elemento['fecha_fallo'];
                $s->nickname = $elemento['nickname'];
                $s->responsable = $elemento['responsable'];
                $s->responsable_gdp = $elemento['responsable_gdp'];
                $s->oportunidad = $elemento['oportunidad'];
                $s->status_crm = $elemento['status_crm'];
                array_push($nuevo,$s);
            }

        $this->content['licitaciones'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_reporteLicitaciones ($estado, $recurso, $status, $empresa, $year) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Folio', 'Proyecto', 'Status', 'Observaciones', 'Entidad', 'Procedimiento', 'Descripción', 'Contrato', 'Orden de compra', 'Monto inicial', 'Monto adjudicado', 'Comprador', 'Empresa concursante','Empresa aliada','Respaldo 1','Respaldo 2','Respaldo 3','Respaldo 4','Fecha inicio','Fecha presentación','Fecha fallo','Usuario', 'Responsable licitación', 'Responsable GDP', 'Oportunidad', 'Status CRM'), ',');

        $sql = "SELECT lic_licitacion.id,lic_licitacion.folio, (select concat (codigo, nombre) from vnt_recurso where vnt_recurso.id = lic_licitacion.recurso_id) as proyecto,lic_licitacion.status, vnt_estado.nombre as entidad, lic_licitacion.titulo as procedimiento, lic_licitacion.descripcion, lic_licitacion.contrato,lic_licitacion.orden_compra,lic_licitacion.monto_inicial, lic_licitacion.monto_adjudicado, lic_licitacion.comprador, (select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = lic_licitacion.empresa_id), lic_licitacion.empresa,lic_licitacion.fecha_inicio, lic_licitacion.fecha_presentacion, lic_licitacion.fecha_fallo, sys_users.nickname, lic_licitacion.responsable, lic_licitacion.responsable_gdp, crm_oportunidades.nombre as oportunidad, crm_oportunidades.status as status_crm,
            case 
            when lic_licitacion.status = 'CREADA' then lic_licitacion.observaciones_creada 
            when lic_licitacion.status = 'EN PROCESO' then lic_licitacion.observaciones_proceso 
            when lic_licitacion.status = 'PRESENTADA' then lic_licitacion.observaciones_presentada
            when lic_licitacion.status = 'ADJUDICADA' then lic_licitacion.observaciones_adjudicada
            when lic_licitacion.status = 'NO ADJUDICADA' then lic_licitacion.observaciones_no_adjudicada
            when lic_licitacion.status = 'CANCELADA' then lic_licitacion.observaciones_cancelada
            end as observaciones_licitacion
            from lic_licitacion
            inner join vnt_estado on vnt_estado.id = lic_licitacion.entidad_id
            inner join sys_users on sys_users.id = lic_licitacion.created_by and lic_licitacion.year = '$year' and lic_licitacion.status_mostrado = 'ACTIVO'
            left join crm_oportunidades on crm_oportunidades.licitacion_id = lic_licitacion.id";

        if (intval($recurso) > 0) {
            $sql = $sql . " and lic_licitacion.recurso_id = $recurso";
        }
        if (intval($estado) > 0) {
            $sql = $sql . " and lic_licitacion.entidad_id = $estado";
        }
        if (intval($empresa) > 0) {
            $sql = $sql . " and lic_licitacion.empresa_id = $empresa_id";
        }
        if ($status != '0' || $status != 0) {
            $sql = $sql . " and lic_licitacion.status = '$status'";
        }
        // var_dump($status);
        $licitaciones = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($licitaciones as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $id = $elemento['id'];
                $sql_respaldo = "SELECT id, licitacion_id, empresa_id, created, created_by, modified, modified_by, empresa,(select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = lic_respaldo.empresa_id) as empresa_nombre FROM lic_respaldo where licitacion_id = $id";
                $respaldos = $this->db->query($sql_respaldo)->fetchAll();
                if (sizeof($respaldos) === 0) {
                    $s->respaldo_1 = '';
                    $s->respaldo_2 = '';
                    $s->respaldo_3 = '';
                    $s->respaldo_4 = '';
                } else {
                    if (sizeof($respaldos) === 1) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        $s->respaldo_2 = '';
                        $s->respaldo_3 = '';
                        $s->respaldo_4 = '';
                    }
                    if (sizeof($respaldos) === 2) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                        $s->respaldo_3 = '';
                        $s->respaldo_4 = '';
                    }
                    if (sizeof($respaldos) == 3) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                        if ($respaldos[2]['empresa_id'] > 0) {
                            $s->respaldo_3 = $respaldos[2]['empresa_nombre'];
                        } else {
                            $s->respaldo_3 = $respaldos[2]['empresa'];
                        }
                        $s->respaldo_4 = '';
                    }
                    if (sizeof($respaldos) == 4) {
                        if ($respaldos[0]['empresa_id'] > 0) {
                            $s->respaldo_1 = $respaldos[0]['empresa_nombre'];
                        } else {
                            $s->respaldo_1 = $respaldos[0]['empresa'];
                        }
                        if ($respaldos[1]['empresa_id'] > 0) {
                            $s->respaldo_2 = $respaldos[1]['empresa_nombre'];
                        } else {
                            $s->respaldo_2 = $respaldos[1]['empresa'];
                        }
                        if ($respaldos[2]['empresa_id'] > 0) {
                            $s->respaldo_3 = $respaldos[2]['empresa_nombre'];
                        } else {
                            $s->respaldo_3 = $respaldos[2]['empresa'];
                        }
                        if ($respaldos[3]['empresa_id'] > 0) {
                            $s->respaldo_4 = $respaldos[3]['empresa_nombre'];
                        } else {
                            $s->respaldo_4 = $respaldos[3]['empresa'];
                        }
                    }
                }
                $s->folio = $elemento['folio'];
                $s->proyecto = $elemento['proyecto'];
                $s->status = $elemento['status'];
                $s->observaciones_licitacion = $elemento['observaciones_licitacion'];
                $s->entidad = $elemento['entidad'];
                $s->procedimiento = $elemento['procedimiento'];
                $s->descripcion = $elemento['descripcion'];
                $s->contrato = $elemento['contrato'];
                $s->orden_compra = $elemento['orden_compra'];
                $s->monto_inicial = number_format(floatval($elemento['monto_inicial']),2,'.',',');
                $s->monto_adjudicado = number_format(floatval($elemento['monto_adjudicado']),2,'.',',');
                $s->comprador = $elemento['comprador'];
                $s->razon_social = $elemento['razon_social'];          
                $s->empresa = $elemento['empresa'];
                $s->fecha_inicio = $elemento['fecha_inicio'];
                $s->fecha_presentacion = $elemento['fecha_presentacion'];
                $s->fecha_fallo = $elemento['fecha_fallo'];
                $s->nickname = $elemento['nickname'];
                $s->responsable = $elemento['responsable'];
                $s->responsable_gdp = $elemento['responsable_gdp'];
                $s->oportunidad = $elemento['oportunidad'];
                $s->status_crm = $elemento['status_crm'];
                array_push($nuevo,$s);
            }

            foreach ($nuevo as $elemento) {
                fputcsv($fp, [
                $elemento->folio,
                $elemento->proyecto,
                $elemento->status,
                $elemento->observaciones_licitacion,
                $elemento->entidad,
                $elemento->procedimiento,
                $elemento->descripcion,
                $elemento->contrato,
                $elemento->orden_compra,
                $elemento->monto_inicial,
                $elemento->monto_adjudicado,
                $elemento->comprador,
                $elemento->razon_social,
                $elemento->empresa,
                $elemento->respaldo_1,
                $elemento->respaldo_2,
                $elemento->respaldo_3,
                $elemento->respaldo_4,
                $elemento->fecha_inicio,
                $elemento->fecha_presentacion,
                $elemento->fecha_fallo,
                $elemento->nickname,
                $elemento->responsable,
                $elemento->responsable_gdp,
                $elemento->oportunidad,
                $elemento->status_crm
                ], ',');
            }

        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_licitaciones'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function getKardex ($almacen, $producto, $fecha_inicio, $fecha_fin) {
        if ($fecha_inicio !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio));
        } else {
            $fecha_inicio = '';
        }
        if($fecha_fin !== 'null') {
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin));
        } else {
            $fecha_fin = '';
        }
        $sql = "SELECT * from getsaldos($almacen, $producto, '$fecha_inicio', '$fecha_fin') order by created, id";
        // var_dump($sql);
        $kardex = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($kardex as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->tipo_id = $elemento['tipo_id'];
                $s->folio = $elemento['folio'];
                $s->almacen_id = $elemento['almacen_id'];
                $s->empresa_id = $elemento['empresa_id'];
                $s->status = $elemento['status'];
                $s->almacen = $elemento['almacen'];
                if ($elemento['movimiento'] === 'ENTRADA') {
                    $s->icon = 'add';
                    $s->color = 'green';
                }
                if ($elemento['movimiento'] === 'INVENTARIO INICIAL') {
                    $s->icon = 'add';
                    $s->color = 'cyan';
                } 
                if ($elemento['movimiento'] === 'SALIDA')  {
                    $s->icon = 'arrow_forward';
                    $s->color = 'red';
                }
                if ($elemento['movimiento'] === 'TRASPASO (SALIDA)') {
                    $s->icon = 'arrow_forward';
                    $s->color = 'orange';
                }
                if ($elemento['movimiento'] === 'TRASPASO (ENTRADA)') {
                    $s->icon = 'add';
                    $s->color = 'purple';
                }
                $s->movimiento = $elemento['movimiento'];
                $s->cantidad = $elemento['cantidad'];
                $s->costo = number_format(floatval($elemento['costo']),2,'.',',');
                $s->producto_id = $elemento['producto_id'];
                $s->producto = $elemento['producto'];
                $s->created = date("Y-m-d H:i:s", strtotime($elemento['created']));
                $s->existencia = $elemento['existencia'];          
                array_push($nuevo,$s);
            }
        $this->content['kardex'] = $nuevo; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();   
    }

    public function exportCSV_kardex ($almacen, $producto, $fecha_inicio, $fecha_fin) {
        if($fecha_inicio !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio));
        } else {
            $fecha_inicio = '';
        }
        if($fecha_fin !== 'null') {
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin));
        } else {
            $fecha_fin = '';
        }
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Folio', 'Movimiento', 'Status', 'Fecha', 'Almacén', 'Producto', 'Cantidad', 'Saldo'), ',');

        $sql = "SELECT * from getsaldos($almacen, $producto, '$fecha_inicio', '$fecha_fin') order by created, id";
        // var_dump($sql);
        $kardex = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($kardex as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->tipo_id = $elemento['tipo_id'];
                $s->folio = $elemento['folio'];
                $s->almacen_id = $elemento['almacen_id'];
                $s->empresa_id = $elemento['empresa_id'];
                $s->status = $elemento['status'];
                $s->almacen = $elemento['almacen'];
                if ($elemento['movimiento'] === 'ENTRADA') {
                    $s->icon = 'add';
                    $s->color = 'green';
                }
                if ($elemento['movimiento'] === 'INVENTARIO INICIAL') {
                    $s->icon = 'add';
                    $s->color = 'cyan';
                } 
                if ($elemento['movimiento'] === 'SALIDA') {
                    $s->icon = 'arrow_forward';
                    $s->color = 'red';
                }
                if ($elemento['movimiento'] === 'TRASPASO (SALIDA)') {
                    $s->icon = 'arrow_forward';
                    $s->color = 'orange';
                }
                if ($elemento['movimiento'] === 'TRASPASO (ENTRADA)') {
                    $s->icon = 'add';
                    $s->color = 'green';
                }
                $s->movimiento = $elemento['movimiento'];
                $s->cantidad = $elemento['cantidad'];
                $s->costo = number_format(floatval($elemento['costo']),2,'.',',');
                $s->producto_id = $elemento['producto_id'];
                $s->producto = $elemento['producto'];
                $s->created = date("Y-m-d H:i:s", strtotime($elemento['created']));;
                $s->existencia = $elemento['existencia'];          
                array_push($nuevo,$s);
            }

            foreach ($nuevo as $elemento) {
                fputcsv($fp, [
                $elemento->folio,
                $elemento->movimiento,
                $elemento->status,
                $elemento->created,
                $elemento->almacen,
                $elemento->producto,
                $elemento->cantidad,
                $elemento->existencia
                ], ',');
            }

        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Kardex'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function existencias_productos ($almacen, $producto, $fecha_inicio, $fecha_fin) {
        $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
        $productos = $this->db->query($sql)->fetchAll();
        if ($almacen === 'null') {

            $productos = [];

            $almacenes = Almacenes::find();
            foreach ($almacenes as $alm) {
                $sql = "SELECT * from get_existencias($alm->id, $producto, $fecha_inicio, $fecha_fin)";
                $data = $this->db->query($sql)->fetchAll();
                for ($i=0; $i < count($data); $i++) {
                    $data[$i]['almacen'] = $alm->nombre; 
                    array_push($productos, $data[$i]);
                } 
            }
            /* for ($i=0; $i < count($productos); $i++) { 
                $almacenOut = Almacenes::findFirst("clave = '".$productos[$i]['codigo_categoria']."'");
                $productos[$i]['almacen'] = "";
                if(!empty($almacenOut->nombre)){
                    $productos[$i]['almacen'] = $almacenOut->nombre;
                }
            } */
        } else {
            $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
            $productos = $this->db->query($sql)->fetchAll();
            $almacenOut = Almacenes::findFirst($almacen);
            for ($i=0; $i < count($productos); $i++) { 
                $productos[$i]['almacen'] = $almacenOut->nombre;
            }
        }
        $this->content['existencias'] = $productos; 
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function reporte_oportunidades ($ejecutivo_id, $sector_id, $subsector_id, $status, $empresa, $tipo, $orden, $ente, $metodo, $estado, $municipio, $aliado_id) {
        $where_ejecutivo = "";
        $and = "";
        if (intval($ejecutivo_id) > 0) {
            $where_ejecutivo = " and sys_users.id = " . intval($ejecutivo_id);
        }
        if (intval($sector_id) > 0) {
            $and = $and . " and crm_oportunidades.sector_id = " . intval($sector_id);
        }
        if (intval($subsector_id) > 0) {
            $and = $and . " and crm_oportunidades.subsector_id = " . intval($subsector_id);
        }
        if ($status != 'all') {
            $and = $and . " and crm_oportunidades.status = '$status'";
        }
        if (intval($empresa) > 0) {
            $and = $and . " and crm_oportunidades.empresa_id = " . intval($empresa);
        }
        if (intval($aliado_id) > 0) {
            $and = $and . " and EXISTS(SELECT id from crm_aliados where crm_aliados.oportunidad_id = crm_oportunidades.id and crm_aliados.aliado_id = " . intval($aliado_id) . ")";
        }
        if ($tipo == 'GDP') {
            if ($orden !== 'all') {
                $and = $and . " and crm_oportunidades.orden = '$orden'";
            }
            if ($ente !== 'all') {
                $and = $and . " and crm_oportunidades.ente = '$ente'";
            }
            if ($metodo !== 'all') {
                $and = $and . " and crm_oportunidades.adjudicacion = '$metodo'";
            }
            if (($orden == 'ESTATAL' || $orden == 'MUNICIPAL') && intval($estado) > 0) {
                $and = $and . " and crm_oportunidades.estado_id = " . intval($estado);
            }
            if ($orden == 'MUNICIPAL' && intval($municipio) > 0) {
                $and = $and . " and crm_oportunidades.municipio_id = " . intval($municipio);
            }
        }
        if ($tipo != 'all') {
            $and = $and . " and crm_oportunidades.tipo = '$tipo'";
        }
        
        $sql = "SELECT crm_oportunidades.id, crm_oportunidades.nombre, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = crm_oportunidades.sector_id) as sector, (select sum(crm_oportunidades.valor) from crm_oportunidades) as valor_total, (select sum(crm_oportunidades.valor_final) from crm_oportunidades) as valor_totalfinal, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = crm_oportunidades.subsector_id) as subsector,crm_oportunidades.valor, to_char(crm_oportunidades.created, 'DD/MM/YYYY') as creada, sys_users.nickname, crm_oportunidades.status, crm_etapas.nivel || '/' || (select count(*) from crm_etapas as x where x.carril_id = crm_etapas.carril_id) as paso, (select coalesce((select sum(porcentaje) from crm_etapas as x where x.carril_id = crm_etapas.carril_id and nivel < crm_etapas.nivel),0)) as porcentaje, case when crm_oportunidades.status = 'PENDIENTE' then 'amber' when crm_oportunidades.status = 'LOGRADA' then 'green' when crm_oportunidades.status = 'NO LOGRADA' then 'red' end as color, to_char(crm_oportunidades.created, 'YYYY/MM/DD') as fecha_calculo, crm_oportunidades.etapa_id, crm_oportunidades.carril_id, (select count(*) from crm_etapas as x where x.carril_id = crm_etapas.carril_id) as pasos_max, (select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = crm_oportunidades.empresa_id) as empresa, case when crm_oportunidades.orden = 'all' then '' else crm_oportunidades.orden end as orden, case when crm_oportunidades.ente = 'all' then '' else crm_oportunidades.ente end as ente, case when crm_oportunidades.adjudicacion = 'all' then '' else crm_oportunidades.adjudicacion end as adjudicacion, (select vnt_estado.nombre from vnt_estado where vnt_estado.id = crm_oportunidades.estado_id) as estado, (select vnt_municipio.nombre from vnt_municipio where vnt_municipio.id = crm_oportunidades.municipio_id) as municipio, crm_oportunidades.valor_final, crm_prospectos.nombre_compania, (select to_char(crm_logs.created, 'DD/MM/YYYY')
            FROM crm_logs
            where crm_logs.oportunidad_id = crm_oportunidades.id
            and crm_logs.tipo = 'LOGRO' order by created limit 1) as fecha_ganada
            from crm_oportunidades
            inner join sys_users on sys_users.id = crm_oportunidades.ejecutivo_id {$where_ejecutivo} and crm_oportunidades.status != 'ELIMINADA'
            inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id {$and}
            inner join crm_prospectos on crm_prospectos.id = crm_oportunidades.prospecto_id";
        $oportunidades = $this->db->query($sql)->fetchAll();
        date_default_timezone_set("America/Mexico_City");
        $i = 0;
        $valor = 0;
        $valor_final = 0;
        foreach ($oportunidades as $key => $oportunidad) {
            $oportunidades[$key]['aliado'] = '';
            $id = $oportunidades[$key]['id'];
            $aliado = AliadosCrm::findFirst('oportunidad_id ='.$id);
            if ($aliado) {
                $oportunidades[$key]['aliado'] = Aliados::findFirst($aliado->aliado_id)->nombre;
            }
            $fecha_hoy=date_create(date('Y-m-d'));
            $fecha_creada = date_create(date('Y-m-d', strtotime($oportunidades[$key]['fecha_calculo'])));
            $dias_transcurridos=date_diff($fecha_hoy, $fecha_creada);
            $oportunidades[$key]['dias'] = $dias_transcurridos->days;
            $etapas = Etapas::find('carril_id = '.$oportunidad['carril_id']);
            $dias_etapa_anterior = 0;
            if ($oportunidades[$key]['status'] !== 'ELIMINADA') {
                $valor = $valor + $oportunidades[$key]['valor'];
                $valor_final = $valor_final + $oportunidades[$key]['valor_final'];
            }
            foreach ($etapas as $etapa) {
                $dias_etapa_anterior = $dias_etapa_anterior + $etapa->dias;
                $consulta_porcentaje = "SELECT coalesce((select sum(porcentaje) from crm_etapas as x where x.carril_id = {$oportunidad['carril_id']} and nivel < {$etapa->nivel}),0)";
                $porcentaje_estimado = $this->db->query($consulta_porcentaje)->fetchAll();
                if ($oportunidad['status'] !== 'NO LOGRADA') {
                    if ($dias_transcurridos->days <= $dias_etapa_anterior) {
                        $oportunidades[$key]['estimada'] = $etapa->nivel . '/' . $oportunidad['pasos_max'];
                        $oportunidades[$key]['avance_estimado'] = $porcentaje_estimado[0]['coalesce'];
                        break;
                    }
                } else {
                    $oportunidades[$key]['estimada'] = '0/' . $oportunidad['pasos_max'];
                    $oportunidades[$key]['avance_estimado'] = 0;
                    break;
                }
            }
        }
        $this->content['reporte'] = $oportunidades;
        $this->content['valor'] = $valor;
        $this->content['valor_final'] = $valor_final;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_oportunidades ($ejecutivo_id, $sector_id, $subsector_id, $status, $empresa, $tipo, $orden, $ente, $metodo, $estado, $municipio, $aliado_id) {

        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('OPORTUNIDAD', 'PROSPECTO', 'ALIADO', 'SECTOR', 'SUBSECTOR', 'EMPRESA', 'ORDEN DE GOBIERNO', 'TIPO DE ENTE', 'ADJUDICACIÓN', 'ESTADO', 'VALOR', 'VALOR FINAL', 'FECHA CREACIÓN', 'FECHA GANADA', 'EJECUTIVO', 'ESTATUS', 'AVANCE ESTIMADO', '% ESTIMADO', 'AVANCE REAL', '% REAL', 'DÍAS CRM'), ',');

        $where_ejecutivo = "";
        $and = "";
        if (intval($ejecutivo_id) > 0) {
            $where_ejecutivo = " and sys_users.id = " . intval($ejecutivo_id);
        }
        if (intval($sector_id) > 0) {
            $and = $and . " and crm_oportunidades.sector_id = " . intval($sector_id);
        }
        if (intval($subsector_id) > 0) {
            $and = $and . " and crm_oportunidades.subsector_id = " . intval($subsector_id);
        }
        if ($status != 'all') {
            $and = $and . " and crm_oportunidades.status = '$status'";
        }
        if (intval($empresa) > 0) {
            $and = $and . " and crm_oportunidades.empresa_id = " . intval($empresa);
        }
        if (intval($aliado_id) > 0) {
            $and = $and . " and EXISTS(SELECT id from crm_aliados where crm_aliados.oportunidad_id = crm_oportunidades.id and crm_aliados.aliado_id = " . intval($aliado_id) . ")";
        }
        if ($tipo == 'GDP') {
            if ($orden !== 'all') {
                $and = $and . " and crm_oportunidades.orden = '$orden'";
            }
            if ($ente !== 'all') {
                $and = $and . " and crm_oportunidades.ente = '$ente'";
            }
            if ($metodo !== 'all') {
                $and = $and . " and crm_oportunidades.adjudicacion = '$metodo'";
            }
            if (($orden == 'ESTATAL' || $orden == 'MUNICIPAL') && intval($estado) > 0) {
                $and = $and . " and crm_oportunidades.estado_id = " . intval($estado);
            }
            if ($orden == 'MUNICIPAL' && intval($municipio) > 0) {
                $and = $and . " and crm_oportunidades.municipio_id = " . intval($municipio);
            }
        }
        if ($tipo != 'all') {
            $and = $and . " and crm_oportunidades.tipo = '$tipo'";
        }
        
        $sql = "SELECT crm_oportunidades.id, crm_oportunidades.nombre, (select vnt_programa.nombre from vnt_programa where vnt_programa.id = crm_oportunidades.sector_id) as sector, (select sum(crm_oportunidades.valor) from crm_oportunidades) as valor_total, (select sum(crm_oportunidades.valor_final) from crm_oportunidades) as valor_totalfinal, (select vnt_subprograma.nombre from vnt_subprograma where vnt_subprograma.id = crm_oportunidades.subsector_id) as subsector,crm_oportunidades.valor, to_char(crm_oportunidades.created, 'DD/MM/YYYY') as creada, sys_users.nickname, crm_oportunidades.status, crm_etapas.nivel || ' de ' || (select count(*) from crm_etapas as x where x.carril_id = crm_etapas.carril_id) as paso, (select coalesce((select sum(porcentaje) from crm_etapas as x where x.carril_id = crm_etapas.carril_id and nivel < crm_etapas.nivel),0)) as porcentaje, case when crm_oportunidades.status = 'PENDIENTE' then 'amber' when crm_oportunidades.status = 'LOGRADA' then 'green' when crm_oportunidades.status = 'NO LOGRADA' then 'red' end as color, to_char(crm_oportunidades.created, 'YYYY/MM/DD') as fecha_calculo, crm_oportunidades.etapa_id, crm_oportunidades.carril_id, (select count(*) from crm_etapas as x where x.carril_id = crm_etapas.carril_id) as pasos_max, (select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = crm_oportunidades.empresa_id) as empresa, case when crm_oportunidades.orden = 'all' then '' else crm_oportunidades.orden end as orden, case when crm_oportunidades.ente = 'all' then '' else crm_oportunidades.ente end as ente, case when crm_oportunidades.adjudicacion = 'all' then '' else crm_oportunidades.adjudicacion end as adjudicacion, (select vnt_estado.nombre from vnt_estado where vnt_estado.id = crm_oportunidades.estado_id) as estado, (select vnt_municipio.nombre from vnt_municipio where vnt_municipio.id = crm_oportunidades.municipio_id) as municipio, crm_oportunidades.valor_final, crm_prospectos.nombre_compania, (select to_char(crm_logs.created, 'DD/MM/YYYY')
            FROM crm_logs
            where crm_logs.oportunidad_id = crm_oportunidades.id
            and crm_logs.tipo = 'LOGRO' order by created limit 1) as fecha_ganada
            from crm_oportunidades
            inner join sys_users on sys_users.id = crm_oportunidades.ejecutivo_id {$where_ejecutivo} and crm_oportunidades.status != 'ELIMINADA'
            inner join crm_etapas on crm_etapas.id = crm_oportunidades.etapa_id {$and}
            inner join crm_prospectos on crm_prospectos.id = crm_oportunidades.prospecto_id";
        $oportunidades = $this->db->query($sql)->fetchAll();
        date_default_timezone_set("America/Mexico_City");
        $i = 0;
        foreach ($oportunidades as $key => $oportunidad) {
            $oportunidades[$key]['estimada'] = '';
            $oportunidades[$key]['avance_estimado'] = '';
            $oportunidades[$key]['aliado'] = '';
            $id = $oportunidades[$key]['id'];
            $aliado = AliadosCrm::findFirst('oportunidad_id ='.$id);
            if ($aliado) {
                $oportunidades[$key]['aliado'] = Aliados::findFirst($aliado->aliado_id)->nombre;
            }
            $fecha_hoy=date_create(date('Y-m-d'));
            $fecha_creada = date_create(date('Y-m-d', strtotime($oportunidades[$key]['fecha_calculo'])));
            $dias_transcurridos=date_diff($fecha_hoy, $fecha_creada);
            $oportunidades[$key]['dias'] = $dias_transcurridos->days;
            $etapas = Etapas::find('carril_id = '.$oportunidad['carril_id']);
            $dias_etapa_anterior = 0;
            foreach ($etapas as $etapa) {
                $dias_etapa_anterior = $dias_etapa_anterior + $etapa->dias;
                $consulta_porcentaje = "SELECT coalesce((select sum(porcentaje) from crm_etapas as x where x.carril_id = {$oportunidad['carril_id']} and nivel < {$etapa->nivel}),0)";
                $porcentaje_estimado = $this->db->query($consulta_porcentaje)->fetchAll();
                if ($oportunidad['status'] !== 'NO LOGRADA') {
                    if ($dias_transcurridos->days <= $dias_etapa_anterior) {
                        $oportunidades[$key]['estimada'] = $etapa->nivel . ' de ' . $oportunidad['pasos_max'];
                        $oportunidades[$key]['avance_estimado'] = $porcentaje_estimado[0]['coalesce'];
                        break;
                    }
                } else {
                    $oportunidades[$key]['estimada'] = '0 de' . $oportunidad['pasos_max'];
                    $oportunidades[$key]['avance_estimado'] = 0;
                    break;
                }
            }
        }
        foreach($oportunidades as $elemento){
            fputcsv($fp, [
                $elemento['nombre'],
                $elemento['nombre_compania'],
                $elemento['aliado'],
                $elemento['sector'],
                $elemento['subsector'],
                $elemento['empresa'],
                $elemento['orden'],
                $elemento['ente'],
                $elemento['adjudicacion'],
                $elemento['estado'],
                number_format(floatval($elemento['valor']),2,'.',','),
                number_format(floatval($elemento['valor_final']),2,'.',','),
                $elemento['creada'],
                $elemento['fecha_ganada'],
                $elemento['nickname'],
                $elemento['status'],
                $elemento['estimada'],
                $elemento['avance_estimado'],
                $elemento['paso'],
                $elemento['porcentaje'],
                $elemento['dias']
                ], ',');
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_CRM'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function get_facturas ($cliente, $empresa, $folio, $ffiscal, $remision, $fecha_inicio, $fecha_fin) {
        $emp = "";
        $cli = "";
        $where = "";
        $whereRemision = "";
        $fechasR = "";
        $fechasC = "";

        if($folio != '$$$FOL$$$'){
            $where = "where COALESCE(vnt_contratos_facturas.folio_xml,'') ilike '%$folio%'";
            $whereRemision = "where COALESCE(vnt_remisiones_facturas.folio_xml,'') ilike '%$folio%'";
        }

        if($ffiscal != '$$$FFIS$$$'){
            $where = "where COALESCE(vnt_contratos_facturas.uuid,'') ilike '%$ffiscal%'";
            $whereRemision = "where COALESCE(vnt_remisiones_facturas.uuid,'') ilike '%$ffiscal%'";
        }

        if($remision != '$$$DEL$$$'){
            $where = "";
            $whereRemision = "where COALESCE(vnt_remisiones.folio,'') ilike '%$remision%'";
        }

        if(intval($empresa) != 0) {
            $emp = " and vnt_empresa.id = $empresa ";
        }
        if(intval($cliente) != 0) {
            $cli = " and vnt_clientes.id = $cliente ";
        }

        if($fecha_inicio != '$$$FINI$$$' && $fecha_fin != '$$$FIN$$$'){
            $fechasR = "and vnt_remisiones.fecha_venta between '$fecha_inicio' and '$fecha_fin'";
            $fechasC = "and vnt_contratos_facturas.fecha_factura between '$fecha_inicio' and '$fecha_fin'";
        }
        

        $sql = "SELECT 'PROYECTO' as tipo, to_char(vnt_contratos_facturas.fecha_factura, 'DD-MM-YYYY') as fecha_venta, vnt_contrato.year,(select vnt_municipio.nombre as municipio from vnt_municipio where id = vnt_clientes.municipio_id), vnt_estado.nombre as estado, vnt_contrato.num_contrato, '---' as folio_remision, vnt_recurso.codigo, vnt_recurso.nombre as proyecto, vnt_contrato.monto_total as monto_contrato, vnt_empresa.razon_social as empresa, vnt_clientes.razon_social as cliente, vnt_contratos_facturas.folio_xml as folio, vnt_contratos_facturas.uuid as folio_fiscal, case when cancelada =true then 'CANCELADA' else 'VIGENTE' end as status, vnt_contratos_facturas.monto_factura, (select case when sys_documents.name like '%AMORTIZA%' then 'AMORTIZACION' when sys_documents.name like '%NOTA DE CR%' then 'NOTA DE CRÉDITO' else '' end as amortizacion 
            from vnt_contratos_facturas as x inner join sys_documents on sys_documents.id = x.document_id and x.id = vnt_contratos_facturas.id), (select coalesce((select sum(vnt_facturas_abonos.monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id),0)) as abonos, (select coalesce((select sum(x.monto_factura) from vnt_contratos_facturas as x where x.factura_relacionada = vnt_contratos_facturas.uuid),0)) as facturas_dependientes
            from vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id {$fechasC}
            inner join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id {$cli}
            inner join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id {$emp}
            inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id
            {$where}

            union all

            select 'REMISION' as tipo, to_char(vnt_remisiones.fecha_venta, 'DD-MM-YYYY') as fecha_venta, date_part('year', vnt_remisiones.fecha_venta) || '' as year,(select vnt_municipio.nombre from vnt_municipio 
            where vnt_municipio.id = vnt_clientes.municipio_id) as municipio,vnt_estado.nombre as estado, ' ' as num_contrato,
            vnt_remisiones.folio as folio_remision, '' as codigo, '' as proyecto, 0 as monto_contrato, vnt_empresa.razon_social
            as empresa, vnt_clientes.razon_social as cliente, folio_xml as folio, vnt_remisiones_facturas.uuid as folio_fiscal,case when vnt_remisiones_facturas.cancelado =true then 'CANCELADA' else 'VIGENTE' end as status,vnt_remisiones_facturas.monto_factura, case when vnt_remisiones_facturas.descripcion like '%AMORTIZA%' then 'AMORTIZACION' when vnt_remisiones_facturas.descripcion like '%NOTA DE CR%' then 'NOTA DE CREDITO' else '' end as amortizacion, (select coalesce((select sum(vnt_remisiones_abonos.monto) from vnt_remisiones_abonos where factura_id = vnt_remisiones.id), 0)) as abonos, (select coalesce((select sum(x.monto_factura) from vnt_remisiones_facturas as x where x.factura_relacionada = vnt_remisiones_facturas.id and x.remision_id > 0),0)) as facturas_dependientes
            from vnt_remisiones_facturas
            join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id {$fechasR}
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id and vnt_remisiones.tipo = 'HISTÓRICO' {$cli}
            join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
            join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id {$emp}
            {$whereRemision}

            order by year, tipo";
        $this->content['facturas'] = $this->db->query($sql)->fetchAll();
        foreach ($this->content['facturas'] as $key => $factura) {
            $monto = $this->content['facturas'][$key]['monto_factura'];
            $abono = $this->content['facturas'][$key]['abonos'];
            $facturas_dependientes = $this->content['facturas'][$key]['facturas_dependientes'];
            $this->content['facturas'][$key]['monto_contrato'] = number_format(floatval($this->content['facturas'][$key]['monto_contrato']),2,'.',',');
            $this->content['facturas'][$key]['monto_factura'] = number_format(floatval($this->content['facturas'][$key]['monto_factura']),2,'.',',');
            $this->content['facturas'][$key]['abonos'] = number_format(floatval($this->content['facturas'][$key]['abonos']),2,'.',',');
            $this->content['facturas'][$key]['falta_cobrar'] = number_format(floatval($monto - $abono),2,'.',',');
            if ($this->content['facturas'][$key]['amortizacion'] != '' || $this->content['facturas'][$key]['status'] == 'CANCELADA') {
                $this->content['facturas'][$key]['falta_cobrar'] = number_format(floatval(0),2,'.',',');
            }
            if ($this->content['facturas'][$key]['facturas_dependientes'] > 0) {
                $this->content['facturas'][$key]['falta_cobrar'] = number_format(floatval($monto - ($facturas_dependientes + $abono)),2,'.',',');
            }
            // var_dump($monto);
            // var_dump($abono);
        }
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV_facturas ($cliente, $empresa, $folio, $ffiscal, $remision, $fecha_inicio, $fecha_fin) {
        $emp = "";
        $cli = "";
        $where = "";
        $whereRemision = "";
        $fechasR = "";
        $fechasC = "";

        if($folio != '$$$FOL$$$'){
            $where = "where COALESCE(vnt_contratos_facturas.folio_xml,'') ilike '%$folio%'";
            $whereRemision = "where COALESCE(vnt_remisiones_facturas.folio_xml,'') ilike '%$folio%'";
        }

        if($ffiscal != '$$$FFIS$$$'){
            $where = "where COALESCE(vnt_contratos_facturas.uuid,'') ilike '%$ffiscal%'";
            $whereRemision = "where COALESCE(vnt_remisiones_facturas.uuid,'') ilike '%$ffiscal%'";
        }

        if($remision != '$$$DEL$$$'){
            $where = "";
            $whereRemision = "where COALESCE(vnt_remisiones.folio,'') ilike '%$remision%'";
        }

        if($fecha_inicio != '$$$FINI$$$' && $fecha_fin != '$$$FIN$$$'){
            $fechasR = "and vnt_remisiones.fecha_venta between '$fecha_inicio' and '$fecha_fin'";
            $fechasC = "and vnt_contratos_facturas.fecha_factura between '$fecha_inicio' and '$fecha_fin'";
        }

        if(intval($empresa) != 0) {
            $emp = " and vnt_empresa.id = $empresa ";
        }
        if(intval($cliente) != 0) {
            $cli = " and vnt_clientes.id = $cliente ";
        }

        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Tipo', 'Fecha factura', 'Año', 'Municipio', 'Estado', 'Número de contrato', 'Folio remisión', 'Código del proyecto', 'Nombre del Proyecto', 'Importe del contrato', 'Empresa', 'Cliente', 'Folio', 'Folio fiscal', 'Status', 'Monto total', 'Amortizacion', 'Abonos', 'Falta cobrar'), ',');

        $sql = "SELECT 'PROYECTO' as tipo, to_char(vnt_contratos_facturas.fecha_factura, 'DD-MM-YYYY') as fecha_venta,vnt_contrato.year,(select vnt_municipio.nombre as municipio from vnt_municipio where id = vnt_clientes.municipio_id), vnt_estado.nombre as estado, vnt_contrato.num_contrato, '---' as folio_remision, vnt_recurso.codigo, vnt_recurso.nombre as proyecto, vnt_contrato.monto_total as monto_contrato, vnt_empresa.razon_social as empresa, vnt_clientes.razon_social as cliente, vnt_contratos_facturas.folio_xml as folio, vnt_contratos_facturas.uuid as folio_fiscal, case when cancelada =true then 'CANCELADA' else 'VIGENTE' end as status, vnt_contratos_facturas.monto_factura, (select case when sys_documents.name like '%AMORTIZA%' then 'AMORTIZACION' when sys_documents.name like '%NOTA DE CR%' then 'NOTA DE CRÉDITO' else '' end as amortizacion from vnt_contratos_facturas as x inner join sys_documents on sys_documents.id = x.document_id and x.id = vnt_contratos_facturas.id), (select coalesce((select sum(vnt_facturas_abonos.monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id),0)) as abonos, (select coalesce((select sum(x.monto_factura) from vnt_contratos_facturas as x where x.factura_relacionada = vnt_contratos_facturas.uuid),0)) as facturas_dependientes
            from vnt_contratos_facturas
            inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id {$fechasC}
            inner join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id {$cli}
            inner join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id {$emp}
            inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id
            {$where}

            union all

            select 'REMISION' as tipo, to_char(vnt_remisiones.fecha_venta, 'DD-MM-YYYY') as fecha_venta, date_part('year', vnt_remisiones.fecha_venta) || '' as year,(select vnt_municipio.nombre from vnt_municipio 
            where vnt_municipio.id = vnt_clientes.municipio_id) as municipio,vnt_estado.nombre as estado, ' ' as num_contrato,
            vnt_remisiones.folio as folio_remision, '' as codigo, '' as proyecto, 0 as monto_contrato, vnt_empresa.razon_social
            as empresa, vnt_clientes.razon_social as cliente, vnt_remisiones_facturas.folio_xml as folio, vnt_remisiones_facturas.uuid as folio_fiscal,
            'VIGENTE' as status, vnt_remisiones_facturas.monto_factura, case when vnt_remisiones_facturas.descripcion like '%AMORTIZA%' then 'AMORTIZACION' when vnt_remisiones_facturas.descripcion like '%NOTA DE CR%' then 'NOTA DE CREDITO' else '' end as amortizacion, (select coalesce((select sum(vnt_remisiones_abonos.monto) from vnt_remisiones_abonos where factura_id = vnt_remisiones.id), 0)) as abonos, (select coalesce((select sum(x.monto_factura) from vnt_remisiones_facturas as x where x.factura_relacionada = vnt_remisiones_facturas.id and x.remision_id > 0),0)) as facturas_dependientes
            from vnt_remisiones_facturas
            join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id {$fechasR}
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id and vnt_remisiones.tipo = 'HISTÓRICO' {$cli}
            join vnt_estado on vnt_clientes.estado_id = vnt_estado.id
            join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id {$emp}
            {$whereRemision}

            order by year, tipo";
        $facturas = $this->db->query($sql)->fetchAll();
        foreach ($facturas as $key => $factura) {
            $monto = $facturas[$key]['monto_factura'];
            $abonos = $facturas[$key]['abonos'];
            $facturas_dependientes = $facturas[$key]['facturas_dependientes'];
            $facturas[$key]['monto_contrato'] = number_format(floatval($facturas[$key]['monto_contrato']),2,'.',',');
            $facturas[$key]['monto_factura'] = number_format(floatval($facturas[$key]['monto_factura']),2,'.',',');
            $facturas[$key]['abonos'] = number_format(floatval($facturas[$key]['abonos']),2,'.',',');
            $facturas[$key]['falta_cobrar'] = number_format(floatval($monto - $abonos),2,'.',',');
            if ($facturas[$key]['amortizacion'] != '') {
                $facturas[$key]['falta_cobrar'] = number_format(floatval(0),2,'.',',');
            }
            if ($facturas[$key]['facturas_dependientes'] > 0) {
                $facturas[$key]['falta_cobrar'] = number_format(floatval($monto - ($facturas_dependientes + $abonos)),2,'.',',');
            }
        }

        foreach ($facturas as $elemento) {
            fputcsv($fp, [
                $elemento['tipo'],
                $elemento['fecha_venta'],
                $elemento['year'],
                $elemento['municipio'],
                $elemento['estado'],
                $elemento['num_contrato'],
                $elemento['folio_remision'],
                $elemento['codigo'],
                $elemento['proyecto'],
                $elemento['monto_contrato'],
                $elemento['empresa'],
                $elemento['cliente'],
                $elemento['folio'],
                $elemento['folio_fiscal'],
                $elemento['status'],
                $elemento['monto_factura'],
                $elemento['amortizacion'],
                $elemento['abonos'],
                $elemento['falta_cobrar']
                ], ',');
        }

        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Facturas'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function existencias_productos_csv ($almacen, $producto, $fecha_inicio, $fecha_fin) {
        /* $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
        $products = $this->db->query($sql)->fetchAll(); */

        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');
        fputcsv($fp, array(utf8_decode('Almacen'),utf8_decode('Categoría'), utf8_decode('Línea'), utf8_decode('Presentación'), utf8_decode('Código'), utf8_decode('Producto'), utf8_decode('Existencias')), ',');

        if ($almacen === 'null') {
            $productos = [];
            $almacenes = Almacenes::find();
            foreach ($almacenes as $alm) {
                $sql = "SELECT * from get_existencias($alm->id, $producto, $fecha_inicio, $fecha_fin)";
                $data = $this->db->query($sql)->fetchAll();
                for ($i=0; $i < count($data); $i++) {
                    $data[$i]['almacen'] = $alm->nombre; 
                    array_push($productos, $data[$i]);
                } 
            }
        } else {
            $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
            $productos = $this->db->query($sql)->fetchAll();
            $almacenOut = Almacenes::findFirst($almacen);
            for ($i=0; $i < count($productos); $i++) { 
                $productos[$i]['almacen'] = $almacenOut->nombre;
            }
        }

        foreach ($productos as $p) {
            fputcsv($fp, array(utf8_decode($p['almacen']),utf8_decode($p['categoria']), utf8_decode($p['linea']), utf8_decode($p['presentacion']), utf8_decode($p['codigo_categoria'].' - '.$p['codigo_linea'].' - '.$p['codigo_producto']), utf8_decode($p['nombre']), utf8_decode($p['existencia'])), ',');
        }

        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Existencias'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function existencias_productos_pdf ($almacen, $producto, $fecha_inicio, $fecha_fin) {
        // $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
        // $products = $this->db->query($sql)->fetchAll(); 

        $almacenTop = "Todos";
        $productoTop = "Todos";
        if ($almacen !== 'null') {
            $alIn = Almacenes::findFirst($almacen);
            $almacenTop = $alIn->nombre;
        }
        if ($producto !== 'null') {
            $prIn = Productos::findFirst($producto);
            $productoTop = $prIn->nombre;
        }

        $pdf = new PDFExistencias('L','mm','Letter');
        $pdf->AliasNbPages();
        $pdf->SetOrderNumber('hola');
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetFont('Arial','',13);

        $pdf->AddPage();
        $pdf->SetY($pdf->GetY()-3);
        $pdf->Cell(80,10,utf8_decode("Almacen: ".$almacenTop),0,1,'L');
        $pdf->SetXY($pdf->GetX()+100, $pdf->GetY()-10);
        $pdf->Cell(80,10,"Producto: ".$productoTop,0,1,'L');
        $pdf->SetY($pdf->GetY()-3);

        $pdf->Ln();
        $pdf->SetFont('Arial','',10);
        $pdf->SetWidths(array(7, 40, 33, 35, 25, 30, 70, 20));
        $pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
        $pdf->SetHeight(6);
        $pdf->SetDrawEdge(false);
        $pdf->SetFill(array(true, true, true, true, true, true, true, true));
        $pdf->SetFillColor(8, 85, 134);
        $pdf->SetTextColor(255);
        $pdf->Row(array(utf8_decode('#'),utf8_decode('Almacen'),utf8_decode('Categoría'), utf8_decode('Línea'), utf8_decode('Presentación'), utf8_decode('Código'), utf8_decode('Producto'),utf8_decode('Existencias')));
        $pdf->SetAligns(array('C', 'L', 'L', 'L', 'L', 'L', 'L', 'R'));
        $totalWeight = 0;

        $contador = 1;

        if ($almacen === 'null') {
            $productos = [];
            $almacenes = Almacenes::find();
            foreach ($almacenes as $alm) {
                $sql = "SELECT * from get_existencias($alm->id, $producto, $fecha_inicio, $fecha_fin)";
                $data = $this->db->query($sql)->fetchAll();
                for ($i=0; $i < count($data); $i++) {
                    $data[$i]['almacen'] = $alm->nombre; 
                    array_push($productos, $data[$i]);
                } 
            }
        } else {
            $sql = "SELECT * from get_existencias($almacen, $producto, $fecha_inicio, $fecha_fin)";
            $productos = $this->db->query($sql)->fetchAll();
            $almacenOut = Almacenes::findFirst($almacen);
            for ($i=0; $i < count($productos); $i++) { 
                $productos[$i]['almacen'] = $almacenOut->nombre;
            }
        }

        foreach ($productos as $p) {
            $pdf->SetFont('Arial','',7);
            $pdf->SetFillColor(255);
            $pdf->SetTextColor(0);
            $pdf->Row(array($contador,utf8_decode($p['almacen']),utf8_decode($p['categoria']), utf8_decode($p['linea']), utf8_decode($p['presentacion']), utf8_decode($p['codigo_categoria'].' - '.$p['codigo_linea'].' - '.$p['codigo_producto']), utf8_decode($p['nombre']), number_format($p['existencia'], 3, '.', ',')));
            $contador++;
        }

                $pdf->SetTitle("Packing List Lote #",true);
                $pdf->Output('I', "Packing List Lote #", true);

                $response = new Phalcon\Http\Response();
                $response->setHeader("Content-Type", "application/pdf");
                $response->setHeader("Content-Disposition", 'inline; filename="Packing List Lote #"');
                return $response;

    }

}

class PDFExistencias extends FPDF
{
    var $widths;
    var $aligns;
    var $height;
    var $orderNumber;
    var $drawEdge = true;
    var $fillCell = false;

    function Header()
    {
        $nowDate = new DateTime();
        $path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';
        $img = $path . 'logo.png';
        $this->Image($img,10,5,75,0,'PNG');
        $this->SetFont('Arial','',17);
        $this->SetX($this->GetX()+165);
        $this->SetTextColor(25, 85, 134);
        $this->MultiCell(130, 0, utf8_decode("Reporte de existencias "), 0, 'C', false);
        $this->SetY($this->GetY()+6);
        $this->SetFont('Arial','',14);
        $this->SetX($this->GetX()+218);
        $this->SetTextColor(0);
        $this->Cell(130, 2, utf8_decode("Fecha: ".$nowDate->format('d-m-Y')), 0, 'C', false);
        $this->Cell(130, 10, 0, 'C', false);
        $this->SetY($this->GetY()+12);
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(260);
        $this->Cell(195, 6, "www.panelw.com", 0, 0, 'C', false);
        $this->SetFont('Arial', '', 10);
        $this->SetY(274);
        $this->SetFillColor(249, 203, 16);
        $this->SetTextColor(0);
        $this->Rect(0,268,216,190,'DF');
        $this->Cell(0,0,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'R');
        $this->Ln();
    }

    function SetWidths($w)
    {
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        $this->aligns=$a;
    }

    function SetHeight($h)
    {
        $this->height=$h;
    }

    function SetOrderNumber($o)
    {
        $this->orderNumber=$o;
    }

    function SetDrawEdge($de)
    {
        $this->drawEdge=$de;
    }

    function SetFill($f)
    {
        $this->fill=$f;
    }

    function Row($data)
    {
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=$this->height*$nb;
        $this->CheckPageBreak($h);
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $f=isset($this->fill[$i]) ? $this->fill[$i] : false;
            $x=$this->GetX();
            $y=$this->GetY();
            if ($this->drawEdge) {
                $this->Rect($x,$y,$w,$h);
            }
            $this->MultiCell($w,$this->height,$data[$i],1,$a,$f);
            $this->SetXY($x+$w,$y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}