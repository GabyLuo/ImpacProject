<?php

use Phalcon\Mvc\Controller;

class ComisionesAbonosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * from com_comisiones_abono";
        $this->content['abonos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function historialAbonos ($comision_id) {
        $sql = "SELECT com_comisiones_abono.id, com_comisiones_abono.comision_id, com_comisiones_abono.fecha, com_comisiones_abono.monto, com_comisiones_abono.transaccion, sys_users.nickname, com_comisiones_abono.metodo_pago, com_comisiones_abono.observaciones
            FROM com_comisiones_abono inner join sys_users on sys_users.id = com_comisiones_abono.user_id and com_comisiones_abono.comision_id = $comision_id order by com_comisiones_abono.fecha asc";
        $data = $this->db->query($sql)->fetchAll();
        foreach ($data as $key => $historial) {
            $sql_documentos = "SELECT * from com_abonos_files where abono_id=" . $historial['id'];
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $array_documentos = [];
            foreach ($documentos as $doc_elemento){
            $doc=(object)array();
            $doc->abono_id = $doc_elemento['abono_id'];
            $doc->nombre = $doc_elemento['nombre'];
            $doc->extension = $doc_elemento['extension'];
            $doc->id = $doc_elemento['id'];
            if ($doc_elemento['extension'] === 'docx') {
                  $doc->color = 'blue-9';
                  $doc->icon = 'fas fa-file-word';
                } else if ($doc_elemento['extension'] === 'pdf' || $doc_elemento['extension'] === 'PDF') {
                  $doc->color = 'red-10';
                  $doc->icon = 'fas fa-file-pdf';
                } else if ($doc_elemento['extension'] === 'xml' || $doc_elemento['extension'] === 'XML') {
                  $doc->color = 'light-green';
                  $doc->icon = 'fas fa-file-code';
                } else if ($doc_elemento['extension'] === 'jpg' || $doc_elemento['extension'] === 'jpeg' || $doc_elemento['extension'] === 'png' || $doc_elemento['extension'] === 'JPG' || $doc_elemento['extension'] === 'JPEG' || $doc_elemento['extension'] === 'PNG') {
                  $doc->color = 'amber';
                  $doc->icon = 'fas fa-file-image';
                }
                array_push($array_documentos,$doc);
            }
            $data[$key]['documentos'] = $array_documentos;
        }
        $this->content['abonos'] = $data;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getComisiones ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $contrato_id, $vendedor_id) {
        $proyecto_id = intval($proyecto_id);
        $aliado_id = intval($aliado_id);
        $vendedor_id = intval($vendedor_id);
        $fi = 0;
        $ff = 0;
        $where = "";
        $or = "";
        $and = "";
        $campos = "";
        $left = "";
        if ($proyecto_id != 0) {
            $where = $where." and com_comisiones.proyecto_id = $proyecto_id";
        }
        if ($aliado_id != 0 && $vendedor_id != 0) {
            $or = " and (com_comisiones.aliado_id = $aliado_id or com_comisiones.vendedor_id = $vendedor_id)";
        } else {
            if ($aliado_id != 0) {
                $where = $where." and com_comisiones.aliado_id = $aliado_id";
            }
            if ($vendedor_id != 0) {
                $where = $where." and com_comisiones.vendedor_id = $vendedor_id";
            }
        }
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio)) . ' 00:00:00';
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin)) . ' 23:59:59';
            $fi = 1;
            $ff = 1;
            $and = " and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'";
            // $where = $where . " and (EXISTS ((SELECT id from com_comisiones_abono where comision_id = com_comisiones.id and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin')))";
            $campos = " com_comisiones_abono.monto as single_abonado, com_comisiones_abono.fecha, ";
            $left = " inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id";
        } else {
            $campos = " 0 as single_abonado, null as fecha, ";
        }
        if ($contrato_id != 0) {
            $and = $and." and com_comisiones.contrato_id = $contrato_id";
        }
        $sql = "SELECT com_comisiones.id, com_comisiones.proyecto_id, com_comisiones.aliado_id, com_comisiones.tipo, com_comisiones.porcentaje, com_comisiones.monto, com_comisiones.metodo_pago, com_comisiones.contrato_id, com_comisiones.iva, com_comisiones.aplicado, com_comisiones.tiempo_pago, com_comisiones.observaciones, com_comisiones.status, com_comisiones.asignado, case when com_comisiones.asignado = 'ALIADO' then (select nombre from com_aliados 
            where id = com_comisiones.aliado_id) else (select sys_users.nickname from sys_users 
            where id = com_comisiones.vendedor_id) end as aliado, case when com_comisiones.tipo = 'PORCENTAJE' then com_comisiones.porcentaje 
            else com_comisiones.monto end as cantidad_com, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_total_abonado, {$campos}
            vnt_recurso.codigo, vnt_recurso.nombre as proyecto,case when com_comisiones.aplicado = 'A LA UTILIDAD' then vnt_recurso.monto else (select vnt_contrato.monto_total from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) end as monto_bolsa, (select coalesce(sum(cambios.suma), 0) as recurso_ejercido
            from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y 
            on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),
            (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
            and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1'),case when com_comisiones.aplicado = 'AL CONTRATO' then (select vnt_contrato.num_contrato from 
            vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) else 'NO APLICA' end as contrato, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_abonado_mes, (SELECT to_char(fecha,'dd/mm/yyyy') as fecha FROM com_comisiones_abono where comision_id = com_comisiones.id {$and} order by fecha desc limit 1) as ultimo_abono
            FROM com_comisiones inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id"
            .$where." $or
            -- inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            {$left} {$and}
            ORDER BY com_comisiones.id";
            // var_dump($sql);
        $comisiones = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($comisiones as $elemento){
            $l=(object)array();
            $l->id = $elemento['id'];
            $id = $elemento['id'];
            $sql_fechas = "SELECT to_char(fecha,'dd/mm/yyyy') as fecha, monto FROM com_comisiones_abono where comision_id = $id";
            $data_fechas = $this->db->query($sql_fechas)->fetchAll();
            $array_fechas = [];
            foreach ($data_fechas as $fecha) {
                $f=(object)array();
                $f->abono = $fecha['fecha'] . '    $' . number_format($fecha['monto'],2,'.',',');
                array_push($array_fechas, $f);
            }
            $l->array_abonos = $array_fechas;
            $l->proyecto_id = $elemento['proyecto_id'];
            $l->asignado = $elemento['asignado'];
            $l->aliado_id = $elemento['aliado_id'];
            $l->tipo = $elemento['tipo'];
            $l->porcentaje = $elemento['porcentaje'];
            $l->monto = $elemento['monto'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            $l->metodo_pago = $elemento['metodo_pago'];
            $l->contrato_id = $elemento['contrato_id'];
            $l->iva = $elemento['iva'];
            $l->aplicado = $elemento['aplicado'];
            $l->tiempo_pago = $elemento['tiempo_pago'];
            $l->observaciones = $elemento['observaciones'];
            $l->status = $elemento['status'];
            $l->aliado = $elemento['aliado'];
            $l->contrato = $elemento['contrato'];
            $l->cantidad_com = $elemento['cantidad_com'];
            $l->monto_total_abonado = $elemento['monto_total_abonado'];
            $l->codigo = $elemento['codigo'];
            $l->codigo_compuesto = $elemento['codigo'] . ' , ' . $elemento['proyecto'];
            $l->proyecto = $elemento['proyecto'];
            $l->recurso_ejercido = $elemento['recurso_ejercido'];
            $l->presupuesto_actividad_principal = $elemento['presupuesto_actividad_principal'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            if ($fecha_inicio !== null && $fecha_fin !== null) {
                $l->fecha = $elemento['fecha'];
                $l->single_abonado = $elemento['single_abonado'];
            }
            if ($elemento['iva'] === 'SUBTOTAL') {
                $monto_base = $elemento['monto_bolsa'] / 1.16;
            } else {
                $monto_base = $elemento['monto_bolsa'];
            }
            $l->subtotal_monto_bolsa = number_format(floatval(round($elemento['monto_bolsa'] / 1.16, 2)),2,'.',',');
            $l->utilidad = $monto_base - $elemento['presupuesto_actividad_principal'];
            if ($elemento['aplicado'] == 'AL CONTRATO') {
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($elemento['monto_bolsa'] > 0) {
                        $l->monto_subtotal_comision = ($monto_base * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            if ($elemento['aplicado'] == 'A LA UTILIDAD') {
                $utility = $monto_base - $elemento['presupuesto_actividad_principal'];
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($utility > 0) {
                        $l->monto_subtotal_comision = ($utility * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            // if ($elemento['iva'] == 'SUBTOTAL') {
                $l->iva = number_format(floatval($elemento['monto_bolsa'] * .16),2,'.',',');
            /* } else {
                $l->iva = 0;
            } */
            $l->monto_total_comision = $l->monto_subtotal_comision;
            $l->monto_restante = round(($l->monto_total_comision - $l->monto_total_abonado),2);
            $l->monto_restante_mostrar = number_format(floatval($l->monto_restante),2,'.',',');
            if (($l->monto_total_comision - $l->monto_total_abonado) < 0.09 && ($l->monto_total_comision - $l->monto_total_abonado) > 0.00) {
                $l->monto_restante = 0.00;
            }
            //
            array_push($nuevo,$l);
        }
        return $nuevo;
    }

    private function getComisiones2 ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $contrato_id, $vendedor_id) {
        $proyecto_id = intval($proyecto_id);
        $aliado_id = intval($aliado_id);
        $vendedor_id = intval($vendedor_id);
        $fi = 0;
        $ff = 0;
        $where = "";
        $and = "";
        $campos = "";
        $left = "";
        $or = "";
        if ($proyecto_id != 0) {
            $where = $where." and com_comisiones.proyecto_id = $proyecto_id";
        }
        if ($aliado_id != 0 && $vendedor_id != 0) {
            $or = " and (com_comisiones.aliado_id = $aliado_id or com_comisiones.vendedor_id = $vendedor_id)";
        } else {
            if ($aliado_id != 0) {
                $where = $where." and com_comisiones.aliado_id = $aliado_id";
            }
            if ($vendedor_id != 0) {
                $where = $where." and com_comisiones.vendedor_id = $vendedor_id";
            }
        }
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio)) . ' 00:00:00';
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin)) . ' 23:59:59';
            $fi = 1;
            $ff = 1;
            $and = " and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'";
            $where = $where . " and (EXISTS ((SELECT id from com_comisiones_abono where comision_id = com_comisiones.id and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin')))";
            $campos = " com_comisiones_abono.monto as single_abonado, com_comisiones_abono.fecha, ";
            $left = " inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id";
        } else {
            $campos = " 0 as single_abonado, null as fecha, ";
        }
        if ($contrato_id != 0) {
            $and = $and." and com_comisiones.contrato_id = $contrato_id";
        } else {
            $and = $and." and com_comisiones.contrato_id = 0";
        }
        $sql = "SELECT com_comisiones.id, com_comisiones.proyecto_id, com_comisiones.aliado_id, com_comisiones.tipo, com_comisiones.porcentaje, com_comisiones.monto, com_comisiones.metodo_pago, com_comisiones.contrato_id, com_comisiones.iva, com_comisiones.aplicado, com_comisiones.tiempo_pago, com_comisiones.observaciones, com_comisiones.status, com_comisiones.asignado, case when com_comisiones.asignado = 'ALIADO' then (select nombre from com_aliados 
            where id = com_comisiones.aliado_id) else (select sys_users.nickname from sys_users 
            where id = com_comisiones.vendedor_id) end as aliado, case when com_comisiones.tipo = 'PORCENTAJE' then com_comisiones.porcentaje 
            else com_comisiones.monto end as cantidad_com, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_total_abonado, {$campos}
            vnt_recurso.codigo, vnt_recurso.nombre as proyecto,case when com_comisiones.aplicado = 'A LA UTILIDAD' then vnt_recurso.monto else (select vnt_contrato.monto_total from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) end as monto_bolsa, (select coalesce(sum(cambios.suma), 0) as recurso_ejercido
            from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y 
            on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),
            (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
            and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1'),case when com_comisiones.aplicado = 'AL CONTRATO' then (select vnt_contrato.num_contrato from 
            vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) else 'NO APLICA' end as contrato, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_abonado_mes, (SELECT to_char(fecha,'dd/mm/yyyy') as fecha FROM com_comisiones_abono where comision_id = com_comisiones.id {$and} order by fecha desc limit 1) as ultimo_abono
            FROM com_comisiones inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id"
            .$where."
            -- inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            {$left} {$and}
            ORDER BY com_comisiones.id";
            // var_dump($sql);
        $comisiones = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($comisiones as $elemento){
            $l=(object)array();
            $l->id = $elemento['id'];
            $id = $elemento['id'];
            $sql_fechas = "SELECT to_char(fecha,'dd/mm/yyyy') as fecha, monto FROM com_comisiones_abono where comision_id = $id";
            $data_fechas = $this->db->query($sql_fechas)->fetchAll();
            $array_fechas = [];
            foreach ($data_fechas as $fecha) {
                $f=(object)array();
                $f->abono = $fecha['fecha'] . '    $' . number_format($fecha['monto'],2,'.',',');
                array_push($array_fechas, $f);
            }
            $l->array_abonos = $array_fechas;
            $l->proyecto_id = $elemento['proyecto_id'];
            $l->asignado = $elemento['asignado'];
            $l->aliado_id = $elemento['aliado_id'];
            $l->tipo = $elemento['tipo'];
            $l->porcentaje = $elemento['porcentaje'];
            $l->monto = $elemento['monto'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            $l->metodo_pago = $elemento['metodo_pago'];
            $l->contrato_id = $elemento['contrato_id'];
            $l->iva = $elemento['iva'];
            $l->aplicado = $elemento['aplicado'];
            $l->tiempo_pago = $elemento['tiempo_pago'];
            $l->observaciones = $elemento['observaciones'];
            $l->status = $elemento['status'];
            $l->aliado = $elemento['aliado'];
            $l->contrato = $elemento['contrato'];
            $l->cantidad_com = $elemento['cantidad_com'];
            $l->monto_total_abonado = $elemento['monto_total_abonado'];
            $l->codigo = $elemento['codigo'];
            $l->codigo_compuesto = $elemento['codigo'] . ' , ' . $elemento['proyecto'];
            $l->proyecto = $elemento['proyecto'];
            $l->recurso_ejercido = $elemento['recurso_ejercido'];
            $l->presupuesto_actividad_principal = $elemento['presupuesto_actividad_principal'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            if ($fecha_inicio !== null && $fecha_fin !== null) {
                $l->fecha = $elemento['fecha'];
                $l->single_abonado = $elemento['single_abonado'];
            }
            if ($elemento['iva'] === 'SUBTOTAL') {
                $monto_base = $elemento['monto_bolsa'] / 1.16;
            } else {
                $monto_base = $elemento['monto_bolsa'];
            }
            $l->subtotal_monto_bolsa = number_format(floatval(round($elemento['monto_bolsa'] / 1.16, 2)),2,'.',',');
            $l->utilidad = $monto_base - $elemento['presupuesto_actividad_principal'];
            if ($elemento['aplicado'] == 'AL CONTRATO') {
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($elemento['monto_bolsa'] > 0) {
                        $l->monto_subtotal_comision = ($monto_base * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            if ($elemento['aplicado'] == 'A LA UTILIDAD') {
                $utility = $monto_base - $elemento['presupuesto_actividad_principal'];
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($utility > 0) {
                        $l->monto_subtotal_comision = ($utility * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            // if ($elemento['iva'] == 'SUBTOTAL') {
                $l->iva = number_format(floatval($elemento['monto_bolsa'] * .16),2,'.',',');
            /* } else {
                $l->iva = 0;
            } */
            $l->monto_total_comision = $l->monto_subtotal_comision;
            $l->monto_restante = round(($l->monto_total_comision - $l->monto_total_abonado),2);
            $l->monto_restante_mostrar = number_format(floatval($l->monto_restante),2,'.',',');
            if (($l->monto_total_comision - $l->monto_total_abonado) < 0.09 && ($l->monto_total_comision - $l->monto_total_abonado) > 0.00) {
                $l->monto_restante = 0.00;
            }
            //
            array_push($nuevo,$l);
        }
        return $nuevo;
    }

    private function getComisionesResumen ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin) {
        $proyecto_id = intval($proyecto_id);
        $aliado_id = intval($aliado_id);
        $fi = 0;
        $ff = 0;
        $where = "";
        $and = "";
        $campos = "";
        $left = "";
        if ($proyecto_id != 0) {
            $where = $where." and com_comisiones.proyecto_id = $proyecto_id";
        }
        if ($aliado_id != 0) {
            $where = $where." and com_comisiones.aliado_id = $aliado_id";
        }
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio)) . ' 00:00:00';
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin)) . ' 23:59:59';
            $fi = 1;
            $ff = 1;
            $and = " and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'";
            // $where = $where . " and (EXISTS ((SELECT id from com_comisiones_abono where comision_id = com_comisiones.id and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin')))";
            $campos = " com_comisiones_abono.monto as single_abonado, com_comisiones_abono.fecha, ";
            $left = " inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id";
        } else {
            $campos = " 0 as single_abonado, null as fecha, ";
        }
        $sql = "SELECT com_comisiones.id, com_comisiones.proyecto_id, com_comisiones.aliado_id, com_comisiones.tipo, com_comisiones.porcentaje, com_comisiones.monto, com_comisiones.metodo_pago, com_comisiones.contrato_id, com_comisiones.iva, com_comisiones.aplicado, com_comisiones.tiempo_pago, com_comisiones.observaciones, com_comisiones.status, com_comisiones.asignado, case when com_comisiones.asignado = 'ALIADO' then (select  nombre from com_aliados 
            where id = com_comisiones.aliado_id) else (select sys_users.nickname from sys_users 
            where id = com_comisiones.vendedor_id) end as aliado, case when com_comisiones.tipo = 'PORCENTAJE' then com_comisiones.porcentaje 
            else com_comisiones.monto end as cantidad_com, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_total_abonado, {$campos}
            vnt_recurso.codigo, vnt_recurso.nombre as proyecto,case when com_comisiones.aplicado = 'A LA UTILIDAD' then vnt_recurso.monto else (select vnt_contrato.monto_total from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) end as monto_bolsa, (select coalesce(sum(cambios.suma), 0) as recurso_ejercido
            from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y 
            on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),
            (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
            and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1'),case when com_comisiones.aplicado = 'AL CONTRATO' then (select vnt_contrato.num_contrato from 
            vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) else 'NO APLICA' end as contrato, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_abonado_mes, (SELECT to_char(fecha,'dd/mm/yyyy') as fecha FROM com_comisiones_abono where comision_id = com_comisiones.id {$and} order by fecha desc limit 1) as ultimo_abono
            FROM com_comisiones inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id"
            .$where."
            -- inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            {$left} {$and}
            ORDER BY com_comisiones.id";
            // var_dump($sql);
        $comisiones = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($comisiones as $elemento){
            $l=(object)array();
            $l->id = $elemento['id'];
            $id = $elemento['id'];
            $sql_fechas = "SELECT to_char(fecha,'dd/mm/yyyy') as fecha, monto FROM com_comisiones_abono where comision_id = $id";
            $data_fechas = $this->db->query($sql_fechas)->fetchAll();
            $array_fechas = [];
            foreach ($data_fechas as $fecha) {
                $f=(object)array();
                $f->abono = $fecha['fecha'] . '    $' . number_format($fecha['monto'],2,'.',',');
                array_push($array_fechas, $f);
            }
            $l->array_abonos = $array_fechas;
            $l->proyecto_id = $elemento['proyecto_id'];
            $l->asignado = $elemento['asignado'];
            $l->aliado_id = $elemento['aliado_id'];
            $l->tipo = $elemento['tipo'];
            $l->porcentaje = $elemento['porcentaje'];
            $l->monto = $elemento['monto'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            $l->metodo_pago = $elemento['metodo_pago'];
            $l->contrato_id = $elemento['contrato_id'];
            $l->iva = $elemento['iva'];
            $l->aplicado = $elemento['aplicado'];
            $l->tiempo_pago = $elemento['tiempo_pago'];
            $l->observaciones = $elemento['observaciones'];
            $l->status = $elemento['status'];
            $l->aliado = $elemento['aliado'];
            $l->contrato = $elemento['contrato'];
            $l->cantidad_com = $elemento['cantidad_com'];
            $l->monto_total_abonado = $elemento['monto_total_abonado'];
            $l->codigo = $elemento['codigo'];
            $l->codigo_compuesto = $elemento['codigo'] . ' , ' . $elemento['proyecto'];
            $l->proyecto = $elemento['proyecto'];
            $l->recurso_ejercido = $elemento['recurso_ejercido'];
            $l->presupuesto_actividad_principal = $elemento['presupuesto_actividad_principal'];
            $l->monto_bolsa = $elemento['monto_bolsa'];
            if ($fecha_inicio !== null && $fecha_fin !== null) {
                $l->fecha = $elemento['fecha'];
                $l->single_abonado = $elemento['single_abonado'];
            }
            if ($elemento['iva'] === 'SUBTOTAL') {
                $monto_base = $elemento['monto_bolsa'] / 1.16;
            } else {
                $monto_base = $elemento['monto_bolsa'];
            }
            $l->subtotal_monto_bolsa = number_format(floatval(round($elemento['monto_bolsa'] / 1.16, 2)),2,'.',',');
            $l->utilidad = $monto_base - $elemento['presupuesto_actividad_principal'];
            if ($elemento['aplicado'] == 'AL CONTRATO') {
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($elemento['monto_bolsa'] > 0) {
                        $l->monto_subtotal_comision = ($monto_base * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            if ($elemento['aplicado'] == 'A LA UTILIDAD') {
                $utility = $monto_base - $elemento['presupuesto_actividad_principal'];
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($utility > 0) {
                        $l->monto_subtotal_comision = ($utility * ($elemento['porcentaje'] / 100));
                    } else {
                        $l->monto_subtotal_comision = 0;
                    }
                } else {
                    $l->monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            // if ($elemento['iva'] == 'SUBTOTAL') {
                $l->iva = number_format(floatval($elemento['monto_bolsa'] * .16),2,'.',',');
            /* } else {
                $l->iva = 0;
            } */
            $l->monto_total_comision = $l->monto_subtotal_comision;
            $l->monto_restante = round(($l->monto_total_comision - $l->monto_total_abonado),2);
            $l->monto_restante_mostrar = number_format(floatval($l->monto_restante),2,'.',',');
            if (($l->monto_total_comision - $l->monto_total_abonado) < 0.09 && ($l->monto_total_comision - $l->monto_total_abonado) > 0.00) {
                $l->monto_restante = 0.00;
            }
            array_push($nuevo,$l);
        }
        return $nuevo;
    }

    public function filtrar ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $vendedor_id) {
        $nuevo = $this->getComisiones($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, 0, $vendedor_id);

        $this->content['comisiones'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturado ($contrato_id) {
        $sql = "SELECT coalesce((sum(monto_factura)), 0) as facturado 
            from vnt_contratos_facturas 
            where contrato_id = $contrato_id and cancelada = false and descripcion not like 'NOTA DE CR' and descripcion not like 'AMORTIZA'";
        $data = $this->db->query($sql)->fetchAll();
        return $data[0]['facturado'];
    }

    public function getCobrado ($contrato_id) {
        $sql = "SELECT coalesce((sum(monto)), 0) as cobrado 
            from vnt_contratos_facturas 
            inner join vnt_facturas_abonos on vnt_facturas_abonos.factura_id = vnt_contratos_facturas.id and
            vnt_contratos_facturas.contrato_id = $contrato_id and cancelada = false and descripcion not like 'NOTA DE CR'
            and descripcion not like 'AMORTIZA'";
        $data = $this->db->query($sql)->fetchAll();
        return $data[0]['cobrado'];
    }

    public function getNotas ($contrato_id) {
        $sql = "SELECT coalesce((sum(monto_factura)), 0) as notas from vnt_contratos_facturas where contrato_id = $contrato_id and cancelada = false and (descripcion like 'NOTA DE CR' or descripcion like 'AMORTIZA')";
        $data = $this->db->query($sql)->fetchAll();
        return $data[0]['notas'];
    }

    public function exportCSV_comisiones ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $vendedor_id) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        if ($fecha_inicio !== null && $fecha_fin !== null) {
            fputcsv($fp, array('Código', 'Proyecto', 'Contrato', 'Asignado', 'Aliado/Vendedor', 'Método de pago', 'Tipo', 'Cantidad/porcentaje', 'Monto total', 'Total abonado', 'Monto abonado', 'Fecha del abono', 'Monto restante', 'Status', 'Observaciones'), ',');
        } else {
            fputcsv($fp, array('Código', 'Proyecto', 'Contrato', 'Asignado', 'Aliado/Vendedor', 'Método de pago', 'Tipo', 'Cantidad/porcentaje', 'Monto total', 'Monto abonado', 'Monto restante', 'Status', 'Observaciones'), ',');
        }

        $proyecto_id = intval($proyecto_id);
        $aliado_id = intval($aliado_id);
        $vendedor_id = intval($vendedor_id);
        $where = "";
        $or = "";
        if ($proyecto_id !== 0) {
            $where = " and com_comisiones.proyecto_id = $proyecto_id";
        }
        if ($aliado_id != 0 && $vendedor_id != 0) {
            $or = " and (com_comisiones.aliado_id = $aliado_id or com_comisiones.vendedor_id = $vendedor_id)";
        } else {
            if ($aliado_id != 0) {
                $where = $where." and com_comisiones.aliado_id = $aliado_id";
            }
            if ($vendedor_id != 0) {
                $where = $where." and com_comisiones.vendedor_id = $vendedor_id";
            }
        }
        $fi = 0;
        $ff = 0;
        $and = "";
        $campos = "";
        $left = "";
        if($fecha_inicio !== 'null' && $fecha_fin !== 'null') {
            $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio)) . ' 00:00:00';
            $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin)) . ' 23:59:59';
            $fi = 1;
            $ff = 1;
            $and = " and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'";
            $where = $where . " and (EXISTS ((SELECT id from com_comisiones_abono where comision_id = com_comisiones.id and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin')))";
            $campos = " com_comisiones_abono.monto as single_abonado, com_comisiones_abono.fecha, ";
            $left = " inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id";
        } else {
            $campos = " 0 as single_abonado, null as fecha, ";
        }
        $sql = "SELECT com_comisiones.id, com_comisiones.proyecto_id, com_comisiones.aliado_id, com_comisiones.tipo, com_comisiones.porcentaje, com_comisiones.monto, com_comisiones.metodo_pago, com_comisiones.contrato_id, com_comisiones.iva, com_comisiones.aplicado, com_comisiones.tiempo_pago, com_comisiones.observaciones, com_comisiones.status, com_comisiones.asignado, case when com_comisiones.asignado = 'ALIADO' then (select  nombre from com_aliados 
            where id = com_comisiones.aliado_id) else (select sys_users.nickname from sys_users 
            where id = com_comisiones.vendedor_id) end as aliado, case when com_comisiones.tipo = 'PORCENTAJE' then com_comisiones.porcentaje 
            else com_comisiones.monto end as cantidad_com, (select COALESCE((SELECT sum(monto) from com_comisiones_abono where comision_id = com_comisiones.id), 0)) as monto_total_abonado, {$campos}
            vnt_recurso.codigo, vnt_recurso.nombre as proyecto,case when com_comisiones.aplicado = 'A LA UTILIDAD' then vnt_recurso.monto else (select vnt_contrato.monto_total from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) end as monto_bolsa, (select coalesce(sum(cambios.suma), 0) as recurso_ejercido 
            from (select case when iva = true then (total + (total * .16)) else total end as suma from fin_solicitudes 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id inner join vnt_recurso as y 
            on y.id = pmo_proyecto.recurso_id and y.id = vnt_recurso.id and fin_solicitudes.status = 'PAGADO') as cambios),
            (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
            and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1'),case when com_comisiones.aplicado = 'AL CONTRATO' then (select vnt_contrato.num_contrato from 
            vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) else 'NO APLICA' end as contrato
            FROM com_comisiones inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id $or"
            .$where."
            {$left} {$and}
            -- inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            ORDER BY com_comisiones.id";
            // var_dump($sql);
        $comisiones = $this->db->query($sql)->fetchAll();

        foreach($comisiones as $elemento){
            $id = $elemento['id'];
            $sql_fechas = "SELECT fecha, monto FROM com_comisiones_abono where comision_id = $id";
            $data_fechas = $this->db->query($sql_fechas)->fetchAll();
            $array_fechas = "";
            foreach ($data_fechas as $fecha) {
                $array_fechas = $array_fechas . $fecha['fecha'] . '    $' . number_format($fecha['monto'],2,'.',',') . PHP_EOL;
            }
            if ($elemento['tipo'] === 'PORCENTAJE') {
                $cantidad_com = $elemento['cantidad_com'] . '%';
            } else {
                $cantidad_com = number_format(floatval($elemento['cantidad_com']),2,'.',',');
            }
            //
            $monto_bolsa = $elemento['monto_bolsa'];
            if ($elemento['iva'] === 'SUBTOTAL') {
                $monto_base = $elemento['monto_bolsa'] / 1.16;
            } else {
                $monto_base = $elemento['monto_bolsa'];
            }
            $subtotal_monto_bolsa = number_format(floatval(round($monto_base, 2)),2,'.',',');
            $utilidad = $monto_base - $elemento['presupuesto_actividad_principal'];
            if ($elemento['aplicado'] == 'AL CONTRATO') {
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($elemento['monto_bolsa'] > 0) {
                        $monto_subtotal_comision = ($monto_base * ($elemento['porcentaje'] / 100));
                    } else {
                        $monto_subtotal_comision = 0;
                    }
                } else {
                    $monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            if ($elemento['aplicado'] == 'A LA UTILIDAD') {
                $utility = $monto_base - $elemento['presupuesto_actividad_principal'];
                if ($elemento['tipo'] == 'PORCENTAJE') {
                    if ($utility > 0) {
                        $monto_subtotal_comision = ($utility * ($elemento['porcentaje'] / 100));
                    } else {
                        $monto_subtotal_comision = 0;
                    }
                } else {
                    $monto_subtotal_comision = $elemento['cantidad_com'];
                }
            }
            // if ($elemento['iva'] == 'SUBTOTAL') {
                $iva = number_format(floatval($elemento['monto_bolsa'] * .16),2,'.',',');
            /* } else {
                $l->iva = 0;
            } */
            $monto_total_comision = $monto_subtotal_comision;
            $monto_restante = round(($monto_total_comision - $elemento['monto_total_abonado']),2);
            $monto_restante_mostrar = number_format(floatval($monto_restante),2,'.',',');
            if (($monto_total_comision - $elemento['monto_total_abonado']) < 0.09 && ($monto_total_comision - $elemento['monto_total_abonado']) > 0.00) {
                $monto_restante = 0.00;
            }
            if ($fecha_inicio !== null && $fecha_fin !== null) {
                $fecha = $elemento['fecha'];
                $single_abonado = $elemento['single_abonado'];
            }

            if ($fecha_inicio !== null && $fecha_fin !== null) {
                fputcsv($fp, [
                $elemento['codigo'],
                $elemento['proyecto'],
                $elemento['contrato'],
                $elemento['asignado'],
                ' '. $elemento['aliado'],
                $elemento['metodo_pago'],
                $elemento['tipo'],
                $cantidad_com,
                number_format(floatval($monto_total_comision),2,'.',','),
                number_format(floatval($elemento['monto_total_abonado']),2,'.',','),
                number_format(floatval($single_abonado),2,'.',','),
                $fecha,
                number_format(floatval($monto_restante),2,'.',','),
                $elemento['status'],
                $elemento['observaciones']//,
                // number_format(floatval($elemento['facturado_impact']),2,'.',','),
                // number_format(floatval($elemento['facturado_notas']),2,'.',','),
                // number_format(floatval($elemento['facturado_impact'] - $elemento['facturado_notas']),2,'.',','),
                // 0,
                // number_format(floatval($falta_cobrar),2,'.',',')
                ], ',');
            } else {
                fputcsv($fp, [
                $elemento['codigo'],
                $elemento['proyecto'],
                $elemento['contrato'],
                $elemento['asignado'],
                ' '. $elemento['aliado'],
                $elemento['metodo_pago'],
                $elemento['tipo'],
                $cantidad_com,
                number_format(floatval($monto_total_comision),2,'.',','),
                number_format(floatval($elemento['monto_total_abonado']),2,'.',','),
                number_format(floatval($monto_restante),2,'.',','),
                $elemento['status'],
                $elemento['observaciones']//,
                // number_format(floatval($elemento['facturado_impact']),2,'.',','),
                // number_format(floatval($elemento['facturado_notas']),2,'.',','),
                // number_format(floatval($elemento['facturado_impact'] - $elemento['facturado_notas']),2,'.',','),
                // 0,
                // number_format(floatval($falta_cobrar),2,'.',',')
                ], ',');
            }
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_comisiones'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    private function getProyectos ($proyecto, $aliado, $fecha_inicio, $fecha_fin, $vendedor_id) {
        $fecha_inicio = ''.date('Y-m-d',strtotime($fecha_inicio)) . ' 00:00:00';
        $fecha_fin = ''.date('Y-m-d',strtotime($fecha_fin)) . ' 23:59:59';
        $and = "";
        if (intval($proyecto) != 0) {
            $and = " and vnt_recurso.id = $proyecto";
        }

        $sql = "SELECT distinct vnt_recurso.id, vnt_recurso.nombre, vnt_contrato.id as contrato_id, vnt_contrato.num_contrato
            FROM public.com_comisiones
            inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id
            inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id
            inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'
            and com_comisiones.aplicado = 'AL CONTRATO' $and

            union all

            SELECT distinct vnt_recurso.id, vnt_recurso.nombre, 0 as contrato_id, '' as num_contrato
            FROM public.com_comisiones
            inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id
            inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id
            -- inner join vnt_contrato on vnt_contrato.id = com_comisiones.contrato_id
            and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin'
            and com_comisiones.aplicado != 'AL CONTRATO' $and order by nombre;
            
            --SELECT distinct vnt_recurso.id, vnt_recurso.nombre
            --FROM public.com_comisiones
            --inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id
            --inner join com_comisiones_abono on com_comisiones_abono.comision_id = com_comisiones.id
            --and com_comisiones_abono.fecha between '$fecha_inicio' and '$fecha_fin' order by vnt_recurso.nombre";
        $data = $this->db->query($sql)->fetchAll();
        $el_mayor = 0;
        // var_dump($data);
        foreach ($data as $key => $proyecto) {
            $proyecto_id = $proyecto['id'];
            $contrato_id = $proyecto['contrato_id'];
            $i = 1;
            if (intval($contrato_id) > 0) {
                $comisiones = $this->getComisiones2($proyecto_id, $aliado, $fecha_inicio, $fecha_fin, $contrato_id, $vendedor_id);
            } else {
                $comisiones = $this->getComisiones2($proyecto_id, $aliado, 'null', 'null', 0, $vendedor_id);
            }
            
            // var_dump($comisiones);
            $total_comisiones = count($comisiones);
            if ($total_comisiones > $el_mayor) {
                $el_mayor = $total_comisiones;
            }
        }
        $contador = 1;
        foreach ($data as $key => $proyecto) {
            $proyecto_id = $proyecto['id'];
            $contrato_id = $proyecto['contrato_id'];
            $i = 1;
            if ($contrato_id != 0) {
                $facturado = floatval($this->getFacturado($contrato_id));
                $notas = floatval($this->getNotas($contrato_id));
                $cobrado = floatval($this->getCobrado($contrato_id));
                $data[$key]['facturado'] = number_format(($facturado - $notas),2,'.',',');
                $data[$key]['cobrado'] = number_format($cobrado,2,'.',',');
            } else {
                $data[$key]['facturado'] = 'NO APLICA';
                $data[$key]['cobrado'] = 'NO APLICA';
            }
            $data[$key]['id'] = $contador;
            $contador++;
            if (intval($contrato_id) > 0) {
                $comisiones = $this->getComisiones2($proyecto_id, $aliado, $fecha_inicio, $fecha_fin, $contrato_id, $vendedor_id);
            } else {
                $comisiones = $this->getComisiones2($proyecto_id, $aliado, 'null', 'null', $contrato_id, $vendedor_id);
            }
            foreach ($comisiones as $com) {
                $data[$key]['aliado'.$i] = ' '. $com->aliado;
                $data[$key]['cantidad'.$i] = number_format($com->monto_total_comision,2,'.',',');
                $i++;
            }
            for ($j = $i; $j<$el_mayor+1; $j++) {
                $data[$key]['aliado'.$j] = '';
                $data[$key]['cantidad'.$j] = '';
            }
            $i = 1;
            foreach ($comisiones as $com) {
                $data[$key]['abonado'.$i] = number_format($com->single_abonado,2,'.',',');
                $i++;
            }
            for ($j = $i; $j<$el_mayor+1; $j++) {
                $data[$key]['abonado'.$j] = '';
            }
            $i = 1;
            foreach ($comisiones as $com) {
                $data[$key]['restante'.$i] = number_format($com->monto_restante,2,'.',',');
                $i++;
            }
            for ($j = $i; $j<$el_mayor+1; $j++) {
                $data[$key]['restante'.$j] = '';
            }
            $data[$key]['total_count'] = $el_mayor;
        }
        return $data;
    }

    public function exportCSV_pagos ($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $vendedor_id) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        if ($fecha_inicio !== null && $fecha_fin !== null) {
        }
        $data = $this->getProyectos($proyecto_id, $aliado_id, $fecha_inicio, $fecha_fin, $vendedor_id);
        $total = 0;
        if (count($data) > 0) {
            $total = $data[0]['total_count'];
        }
        $l=array();
        $l['num'] = '#';
        $l['pro'] = 'Proyecto';
        $l['con'] = 'Contrato';
        $l['fac'] = 'Facturado';
        $l['cob'] = 'Cobrado';
        for($j = 1; $j < $total + 1; $j++) {
            $l['aliado'.$j] = 'Aliado '.$j;
            $l['cantidad'.$j] = '%/Cantidad';
        }
        for($j = 1; $j < $total + 1; $j++) {
            $l['alidos'.$j] = 'Aliado '.$j;
        }
        for($j = 1; $j < $total + 1; $j++) {
            $l['aliaados'.$j] = 'Aliado '.$j;
        }
        fputcsv($fp, $l, ',');

        $i = 1;
        // var_dump($data);
        foreach($data as $elemento) {
            unset($elemento['total_count']);
            unset($elemento['contrato_id']);
            fputcsv($fp, $elemento, ',');
            $i++;
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Reporte_alidos_pagos'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $validUser = Auth::getUserData($this->config);
            $id = $validUser->user_id;
            
                $abonos = new AbonosComisiones();
                $abonos->setTransaction($tx);
                $abonos->comision_id = strtoupper($request['comision_id']);
                $abonos->fecha = date("Y/m/d", strtotime($request['fecha_abono']));
                $abonos->monto = floatval($request['monto_abonar_validado']);
                $abonos->metodo_pago = strtoupper($request['metodo_pago_comision']);
                $abonos->observaciones = trim(strtoupper($request['observaciones_abono']));
                if (trim($request['transaccion']) === '') {
                    $abonos->transaccion = null;
                } else {
                    $abonos->transaccion = trim($request['transaccion']);
                }
                $abonos->user_id = $id;
                $suma_abonos = floatval($request['monto_abonar_validado']) + floatval($request['monto_total_abonado']);
                if ($abonos->create()) {
                    $comision = Comisiones::findFirst($request['comision_id']);
                    if ($comision) {
                        $comision->setTransaction($tx);
                        if ((floatval($request['monto_total_comision']) - floatval($suma_abonos)) < 0.1) {
                            $comision->status='PAGADO';
                        } else {
                            $comision->status='ABONADO';
                        }
                        if ($comision->update()) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el abono de la factura.'];
                            $tx->commit();
                        } else {
                            $this->content['error'] = Helpers::getErrors($comision);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = 'Error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la factura.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($abonos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
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

                $abonos = AbonosComisiones::findFirst($request['id_abono']);
                if ($abonos) {
                    $abonos->setTransaction($tx);
                    $monto_inicial = $abonos->monto;
                    $abonos->comision_id = strtoupper($request['comision_id']);
                    $abonos->fecha = date("Y/m/d", strtotime($request['fecha_abono']));
                    $abonos->monto = floatval($request['monto_abonar_validado']);
                    $abonos->metodo_pago = strtoupper($request['metodo_pago_comision']);
                    $abonos->observaciones = trim(strtoupper($request['observaciones_abono']));
                    if (trim($request['transaccion']) === '') {
                        $abonos->transaccion = null;
                    } else {
                        $abonos->transaccion = trim($request['transaccion']);
                    }
                    $suma_abonos = floatval($request['monto_abonar_validado']) + (floatval($request['monto_total_abonado']) - $monto_inicial);
                    if ($abonos->update()) {
                        $comision = Comisiones::findFirst($request['comision_id']);
                        if ($comision) {
                            $comision->setTransaction($tx);
                            if ((floatval($request['monto_total_comision']) - floatval($suma_abonos)) < 0.1) {
                                $comision->status='PAGADO';
                            } else {
                                $comision->status='ABONADO';
                            }
                            if ($comision->update()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el abono de la factura.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($comision);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el abono de la factura.'];
                                $tx->rollback();
                            }
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($abonos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el abono de la factura.'];
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
                    $abonos = AbonosComisiones::findFirst($request['id']);
                    $abonos->setTransaction($tx);

                    if ($abonos->delete()) {
                        $comision_id = $abonos->comision_id;
                        $montos = AbonosComisiones::find([
                            'comision_id = :comision_id: ',
                                'bind' => [
                                    'comision_id' => $comision_id
                                ]
                            ]
                        );
                        $comision = Comisiones::findFirst($comision_id);
                        if ($comision) {
                            $comision->setTransaction($tx);
                            if (sizeof($montos) > 0) {
                                if ($comision->status === 'PAGADO') {
                                    $comision->status='ABONADO';
                                }
                            } else {
                                $comision->status='NUEVO';
                            }
                            if ($comision->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($comision);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['error'] = 'Error';
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la factura'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($abonos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el abono.'];
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