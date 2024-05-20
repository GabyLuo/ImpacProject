<?php

use Phalcon\Mvc\Controller;

class PowerBiController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function exportJSON () {
        $hoy = date("Y-m-d");
        $year = date("Y");
        $content = $this->content;
        
        $sql = "SELECT s.*, pm.razon_social as proveedor, pp.nombre as proyecto, ve.razon_social as empresa,
        sy.nickname as creador, vr.tipo
        from fin_solicitudes as s
        inner join pmo_proveedores as pm on pm.id = s.proveedor_id
        inner join pmo_proyecto as pp on pp.id = s.proyecto_id
        inner join vnt_recurso as vr on vr.id = pp.recurso_id
        inner join vnt_empresa as ve on ve.id = s.empresa_id
        inner join sys_users as sy on sy.id = s.creador";
        $arreglo_solicitudes = $this->db->query($sql)->fetchAll();

        $sql_actividades = "SELECT  * from pmo_actividades";
        $arreglo_actividades = $this->db->query($sql_actividades)->fetchAll();

        $sql_usuarios = "SELECT id, nickname from sys_users";
        $arreglo_usuarios = $this->db->query($sql_usuarios)->fetchAll();

        $sql_clientes = "SELECT id, razon_social, status, codigo, nombre_corto, rfc FROM vnt_clientes";
        $arreglo_clientes = $this->db->query($sql_clientes)->fetchAll();

        $sql_empresas = "SELECT id, razon_social, rfc, rep_legal, curp_representante, regimen, nombre_corto, rfc_representante, subempresa_id, padre_id, telefono, correo FROM vnt_empresa";
        $arreglo_empresas = $this->db->query($sql_empresas)->fetchAll();

        $sql_programas = "SELECT id, nombre FROM vnt_programa";
        $arreglo_programas = $this->db->query($sql_programas)->fetchAll();

        $sql_recurso = "SELECT id, cliente_id, subprograma_id, monto, codigo, fuente_financiamiento, rubro, nombre, aliado, aliado1, aliado2, aliado3, year, licitacion_id, oportunidad_id, adjudicacion, sucursal_id, created FROM vnt_recurso;";
        $arreglo_recursos = $this->db->query($sql_recurso)->fetchAll();

        $sql_subprograma = "SELECT id, programa_id, nombre, codigo FROM vnt_subprograma";
        $arreglo_subprograma = $this->db->query($sql_subprograma)->fetchAll();

        $sql_sucursales = "SELECT id, empresa_id, nombre, encargado, telefono FROM vnt_sucursales";
        $arreglo_sucursales = $this->db->query($sql_sucursales)->fetchAll();

        $sql_proyecto = "SELECT * from pmo_proyecto";
        $arreglo_proyecto = $this->db->query($sql_proyecto)->fetchAll();

        $sql_contratos = "SELECT id, recurso_id, empresa_id, fecha_inicio, monto_total,nombre, porcentaje, year FROM vnt_contrato";
        $arreglo_contratos = $this->db->query($sql_contratos)->fetchAll();

        $sql_contratos_facturas = "SELECT id, contrato_id, fecha_factura, document_id, fecha_vencimiento, monto_factura, status, cancelada, descripcion, factura_relacionada, uuid FROM vnt_contratos_facturas";
        $arreglo_contratos_facturas = $this->db->query($sql_contratos_facturas)->fetchAll();

        $sql_remisiones = "SELECT id, empresa_id, cliente_id, fecha_venta, status, tipo, status_cobranza, cancelado FROM vnt_remisiones";
        $arreglo_remisiones = $this->db->query($sql_remisiones)->fetchAll();

        $sql_remisiones_facturas = "SELECT id, remision_id, fecha_factura, document_id,monto_factura, status,account_id, doc_type, name, year, descripcion, repetido, contrato_id, amortizacion, factura_relacionada, cancelado FROM vnt_remisiones_facturas";
        $arreglo_remisiones_facturas = $this->db->query($sql_remisiones_facturas)->fetchAll();

        $sql_documentos = "SELECT sys_documents.id, sys_documents.name from sys_documents inner join vnt_contratos_facturas on vnt_contratos_facturas.document_id = sys_documents.id";
        $arreglo_documentos = $this->db->query($sql_documentos)->fetchAll();

        $sql_facturas_abonos = "SELECT * from vnt_facturas_abonos";
        $arreglo_facturas_abonos = $this->db->query($sql_facturas_abonos)->fetchAll();

        $sql_conceptos = "SELECT id, nombre FROM vnt_concepto";
        $arreglo_conceptos = $this->db->query($sql_conceptos)->fetchAll();

        $sql_gastos = "SELECT id, monto, concepto_id, solicitud_id, subconcepto_id from fin_gastos";
        $arreglo_gastos = $this->db->query($sql_gastos)->fetchAll();

        $sql_proyectos_tabla = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,case when vnt_recurso.nombre is null then ' ' else vnt_recurso.nombre end as nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, vnt_recurso.year,
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, vnt_recurso.tipo, pmo_sucursales.nombre as sucursal, vnt_recurso.tipo
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            inner join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id
            order by vnt_recurso.codigo";
        $arreglo_proyectos_tabla = $this->db->query($sql_proyectos_tabla)->fetchAll();

        $sql_dash_proyectos = "SELECT pmo_proyecto.id, vnt_recurso.codigo, pmo_proyecto.nombre, sys_users.nickname,pmo_proyecto.dias,pmo_proyecto.fin as fecha_limite,pmo_proyecto.lider_proyecto,
                (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades where nivel = '1' and proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades from pmo_actividades where proyecto_id = pmo_proyecto.id),
                (select count(*) as actividades_vencidas from pmo_actividades where proyecto_id = pmo_proyecto.id and fin < '$hoy'),
                (select sum(costo) as costo_estimado from (select pmo_actividades.id, pmo_actividades.costo, pmo_actividades.fin, (select count(d.id) as hijos from pmo_actividades as d where padre_id = pmo_actividades.id) from pmo_actividades where pmo_actividades.proyecto_id = pmo_proyecto.id) as tabla_detalles_hijos where hijos = 0 and fin < '$hoy'),
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
                end as suma from (select fin_solicitudes.id, fin_solicitudes.iva, fin_solicitudes.total, fin_solicitudes.subempresa_id, sum(fin_gastos.monto * (fin_gastos.comision/100)) as comision_calculada, fin_solicitudes.proyecto_id, fin_solicitudes.status from fin_solicitudes, fin_gastos where fin_solicitudes.id = fin_gastos.solicitud_id group by fin_solicitudes.id) as tabla_gastos_solicitudes where proyecto_id = pmo_proyecto.id and status = 'PAGADO') as cambios), pmo_sucursales.nombre as sucursal, vnt_recurso.tipo
                from pmo_proyecto 
                inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id -- and pmo_proyecto.year = '$year'
                left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id
                inner join sys_users on sys_users.id = pmo_proyecto.lider_proyecto
                inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id 
                inner join vnt_estado on vnt_estado.id = vnt_clientes.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id order by sys_users.nickname, vnt_recurso.codigo, pmo_proyecto.nombre";
        $arreglo_dash_proyectos = $this->db->query($sql_dash_proyectos)->fetchAll();

        $sql_adeudo_gdp = "SELECT coalesce(sum(monto_total),0) as contratos, coalesce(coalesce(sum(monto_total_abonado),0) - coalesce(sum(monto_cancelado_credito),0)) as abonado, coalesce(coalesce(sum(monto_facturado),0) - coalesce(sum(monto_cancelado_credito),0)) as monto_facturado, cliente_id, razon_social, year, proyecto, sucursal_nombre, tipo from (SELECT vnt_contrato.recurso_id,vnt_clientes.id as cliente_id,vnt_clientes.razon_social as razon_social, vnt_contrato.monto_total,
            (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos 
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
            vnt_contrato.id),0)) as monto_cancelado_credito,vnt_recurso.subprograma_id, vnt_recurso.codigo, vnt_contrato.year,
            vnt_clientes.razon_social as cliente, vnt_recurso.nombre as proyecto, pmo_sucursales.nombre as sucursal_nombre, vnt_recurso.tipo
            from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id) as y
            group by cliente_id, razon_social, year, proyecto, sucursal_nombre, tipo
            order by razon_social";
        $arreglo_adeudo_gdp = $this->db->query($sql_adeudo_gdp)->fetchAll();

        $sql_adeudo_retail = "SELECT coalesce(sum(facturado_impact), 0) as facturado, coalesce(sum(facturas_abonadas), 0) as facturas_abonadas, year, cliente_id, razon_social 
            FROM (SELECT vnt_remisiones.id, vnt_remisiones_facturas.monto_factura as monto_total,
            vnt_remisiones_facturas.monto_factura as facturado_impact, date_part('year', vnt_remisiones.fecha_venta) as year, vnt_remisiones.cliente_id,
            vnt_clientes.razon_social,
            (select coalesce((select sum(vnt_remisiones_abonos.monto) 
            from vnt_remisiones_abonos
            inner join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones_abonos.factura_id
            and vnt_remisiones_facturas.remision_id = vnt_remisiones.id and vnt_remisiones_facturas.cancelado = false 
            and vnt_remisiones_facturas.amortizacion = false), 0)) as facturas_abonadas, vnt_remisiones_facturas.monto_factura as facturado_total
            from vnt_remisiones
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id and vnt_remisiones.tipo = 'HISTÓRICO' 
            join vnt_remisiones_facturas on vnt_remisiones_facturas.remision_id = vnt_remisiones.id
            and vnt_remisiones_facturas.cancelado = false and vnt_remisiones_facturas.amortizacion = false) as x
            group by year, cliente_id, razon_social
            order by razon_social";
        $arreglo_adeudo_retail = $this->db->query($sql_adeudo_retail)->fetchAll();

        $sql_notas_retail = "SELECT count(x.id) as num, coalesce(sum(x.monto_factura),0) as notas_retail, w.id, w.razon_social, date_part('year', y.fecha_venta) as year
                from vnt_remisiones_facturas as x
                inner join vnt_remisiones on vnt_remisiones.id = x.remision_id and  x.amortizacion = true and x.cancelado = false 
                and x.factura_relacionada is not null 
                inner join vnt_remisiones_facturas on x.factura_relacionada = vnt_remisiones_facturas.id 
                inner join vnt_remisiones as y on y.id = vnt_remisiones_facturas.remision_id
                inner join vnt_clientes as w on y.cliente_id = w.id
                group by w.id, w.razon_social, date_part('year', y.fecha_venta)";
        $arreglo_notas_retail = $this->db->query($sql_notas_retail)->fetchAll();

        $sql_notas_gdp = "SELECT count(x.id) as num, coalesce(sum(x.monto_factura),0) as notas_gdp, w.id, w.razon_social
            from vnt_contratos_facturas as x
            inner join vnt_contrato on vnt_contrato.id = x.contrato_id and x.cancelada = false 
            and x.factura_relacionada is not null 
            inner join vnt_contratos_facturas on x.factura_relacionada = vnt_contratos_facturas.uuid 
            inner join vnt_contrato as y on y.id = vnt_contratos_facturas.contrato_id
            inner join vnt_recurso on vnt_recurso.id = y.recurso_id
            inner join vnt_clientes as w on w.id = vnt_recurso.cliente_id
            group by w.id, w.razon_social";

        $arreglo_notas_gdp = $this->db->query($sql_notas_gdp)->fetchAll();

        $sql_facturas_totales = "SELECT vnt_contratos_facturas.id, contrato_id, document_id, monto_factura, vnt_contratos_facturas.status,  'GDP' as tipo, vnt_recurso.id as proyecto_id, vnt_recurso.nombre as proyecto, cancelada, 
            case when factura_relacionada is not null then true else false end as f_relacionada,
            case when (vnt_contratos_facturas.status = 'DESCONTADO' or factura_relacionada is not null) then true else false end as nota_credito, vnt_clientes.razon_social,
            (select sum (monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id) as abono, vnt_contrato.year, case
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 1 THEN 'ENERO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 2 THEN  'FEBRERO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 3 THEN 'MARZO' 
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 4 THEN 'ABRIL' 
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 5 THEN 'MAYO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 6 THEN 'JUNIO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 7 THEN 'JULIO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 8 THEN 'AGOSTO'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 9 THEN 'SEPTIEMBRE'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 10 THEN 'OCTUBRE'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 11 THEN 'NOVIEMBRE'
            WHEN date_part('month',vnt_contrato.fecha_inicio) = 12 THEN 'DICIEMBRE'
            end as mes, vnt_contratos_facturas.folio_fiscal, vnt_contratos_facturas.uuid, coalesce(vnt_recurso.monto_sin_iva, 0) as monto_sin_iva, subtotal_monto_factura, pmo_sucursales.nombre as sucursal, case when vnt_contratos_facturas.status = 'PAGADO' or vnt_contratos_facturas.status = 'DESCONTADO' then sys_documents.name else ' - ' end as name_factura, vnt_recurso.tipo
                        FROM vnt_contratos_facturas
                        inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
                        inner join vnt_contrato on vnt_contrato.id = vnt_contratos_facturas.contrato_id
                        inner join vnt_recurso on vnt_recurso.id = vnt_contrato.recurso_id
                        inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                        inner join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id

                        union all 
                        
            SELECT vnt_remisiones_facturas.id, 0 as contrato_id, document_id, monto_factura, vnt_remisiones_facturas.status,
            'RETAIL' as tipo, 0 as proyecto_id, null as proyecto, vnt_remisiones_facturas.cancelado as cancelada,
            case when factura_relacionada is not null then true else false end as f_relacionada,                                           
            vnt_remisiones_facturas.amortizacion as nota_credito, vnt_clientes.razon_social,
            (select sum(monto) from vnt_remisiones_abonos where factura_id = vnt_remisiones_facturas.id) as abono, to_char(vnt_remisiones.fecha_venta, 'YYYY') as year, case
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 1 THEN 'ENERO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 2 THEN  'FEBRERO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 3 THEN 'MARZO' 
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 4 THEN 'ABRIL' 
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 5 THEN 'MAYO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 6 THEN 'JUNIO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 7 THEN 'JULIO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 8 THEN 'AGOSTO'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 9 THEN 'SEPTIEMBRE'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 10 THEN 'OCTUBRE'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 11 THEN 'NOVIEMBRE'
            WHEN date_part('month',vnt_remisiones.fecha_venta) = 12 THEN 'DICIEMBRE'
            end as mes, vnt_remisiones_facturas.folio_fiscal, vnt_remisiones_facturas.uuid, 0 as monto_sin_iva, 0 as subtotal_monto_factura, '' as sucursal, case when vnt_remisiones_facturas.status = 'PAGADO' then vnt_remisiones_facturas.name else ' - ' end as name_factura, '' as tipo
            from vnt_remisiones_facturas
            inner join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            inner join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id";

        $arreglo_total_facturas = $this->db->query($sql_facturas_totales)->fetchAll();

        $sql_gastos_desglosados = "SELECT vnt_recurso.id as id_proyecto, vnt_recurso.codigo,vnt_recurso.nombre as nombre_proyecto,pmo_actividades.nombre as actividad,
            pmo_actividades.id as id_actividad, fin_solicitudes.id,
            (select nombre from vnt_concepto where fin_gastos.concepto_id = vnt_concepto.id) as concepto,
            fin_gastos.concepto_id as id_concepto,
            (select nombre from vnt_subconcepto where fin_gastos.subconcepto_id = vnt_subconcepto.id) as subconcepto,
            fin_gastos.subconcepto_id as id_subconcepto, pmo_proveedores.razon_social as proveedor, fin_gastos.proveedor_id,
            pmo_proyecto.id as id_project,pmo_proyecto.nombre as nombre_project,
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
            end as mes,
            (select nombre from pmo_sucursales where id = vnt_recurso.sucursal_id) as sucursal, (select nombre from pmo_proyecto where id = fin_solicitudes.proyecto_comision_id) as proyecto_comision, to_char(fin_solicitudes.fecha_creado, 'YYYY') as year, fin_gastos.monto as subtotal,
            case
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)) + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is not null then (fin_gastos.monto + (fin_gastos.monto * (fin_gastos.comision/100)))
            when fin_solicitudes.iva = true and fin_solicitudes.subempresa_id is null then (fin_gastos.monto + (fin_gastos.monto * .16))
            when fin_solicitudes.iva = false and fin_solicitudes.subempresa_id is null then fin_gastos.monto
            when fin_solicitudes.subempresa_id is null then 0
            end as total_comision, pmo_proyecto.monto_bolsa_iva as monto_bolsa_sin_iva, vnt_recurso.tipo 
            from fin_gastos
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
            inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
            inner join sys_users on fin_solicitudes.creador = sys_users.id 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id
            inner join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            inner join fin_tipos_gastos on fin_tipos_gastos.id = fin_gastos.tipo
            inner join pmo_proveedores on pmo_proveedores.id = fin_gastos.proveedor_id
            inner join vnt_empresa on vnt_empresa.id = fin_solicitudes.empresa_id";
        $arreglo_gastos_desglosados = $this->db->query($sql_gastos_desglosados)->fetchAll();

        $sql_proyectos_resumen = "SELECT vnt_recurso.nombre,
        case WHEN date_part('month',vnt_recurso.created) = 1 THEN 'ENERO'
                    WHEN date_part('month',vnt_recurso.created) = 2 THEN  'FEBRERO'
                    WHEN date_part('month',vnt_recurso.created) = 3 THEN 'MARZO' 
                    WHEN date_part('month',vnt_recurso.created) = 4 THEN 'ABRIL' 
                    WHEN date_part('month',vnt_recurso.created) = 5 THEN 'MAYO'
                    WHEN date_part('month',vnt_recurso.created) = 6 THEN 'JUNIO'
                    WHEN date_part('month',vnt_recurso.created) = 7 THEN 'JULIO'
                    WHEN date_part('month',vnt_recurso.created) = 8 THEN 'AGOSTO'
                    WHEN date_part('month',vnt_recurso.created) = 9 THEN 'SEPTIEMBRE'
                    WHEN date_part('month',vnt_recurso.created) = 10 THEN 'OCTUBRE'
                    WHEN date_part('month',vnt_recurso.created) = 11 THEN 'NOVIEMBRE'
                    WHEN date_part('month',vnt_recurso.created) = 12 THEN 'DICIEMBRE' end as mes,
        (select sum(pmo_actividades.costo) as presupuesto from pmo_actividades 
        inner join pmo_proyecto on pmo_actividades.proyecto_id = pmo_proyecto.id 
         where nivel = '1' and proyecto_id = pmo_proyecto.id and pmo_proyecto.recurso_id = vnt_recurso.id), monto,
         (select sum(fin_solicitudes.total) from fin_solicitudes 
        inner join pmo_proyecto on fin_solicitudes.proyecto_id = pmo_proyecto.id 
         where pmo_proyecto.recurso_id = vnt_recurso.id) as subtotal,
         (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos 
                    inner join vnt_contratos_facturas on vnt_contratos_facturas.id = vnt_facturas_abonos.factura_id 
                    inner join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id and sys_documents.name not like 
                    '%AMORTIZA%' and sys_documents.name not like '%NOTA DE CR%' and vnt_contratos_facturas.cancelada = false
                    inner join vnt_contrato as x on x.id = vnt_contratos_facturas.contrato_id and x.recurso_id = vnt_recurso.id), 0)) as cobrado, tipo
        from vnt_recurso";
        $arreglo_proyecto_resumen = $this->db->query($sql_proyectos_resumen)->fetchAll();

        $array = Array (
            "solicitudes" => $arreglo_solicitudes,
            "proyecto" => $arreglo_proyecto,
            "clientes" => $arreglo_clientes,
            "documentos" => $arreglo_documentos,
            "gastos" => $arreglo_gastos,
            "tabla_proyectos" => $arreglo_proyectos_tabla,
            "dash_proyectos" => $arreglo_dash_proyectos,
            "adeudos_gdp" => $arreglo_adeudo_gdp,
            "adeudos_retail" => $arreglo_adeudo_retail,
            "notas_retail" => $arreglo_notas_retail,
            "notas_gdp" => $arreglo_notas_gdp,
            "facturas_desglosadas" => $arreglo_total_facturas,
            "gastos_desglosados" => $arreglo_gastos_desglosados,
            "proyecto_resumen" => $arreglo_proyecto_resumen
        );
        
        $json_string = json_encode($array);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=datos_json'. '.json');
        $this->response->setContent($json_string);
        $this->response->send();
    }

}
