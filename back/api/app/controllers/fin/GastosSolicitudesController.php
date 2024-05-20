<?php

use Phalcon\Mvc\Controller;

class GastosSolicitudesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT fin_gastos.id,fin_gastos.actividad_id,descripcion,monto,fin_gastos.fecha_ejercido,
                fin_gastos.status,fin_gastos.fecha_solicitado,
                fin_gastos.responsable_id,
                (select sys_users.nickname from sys_users where sys_users.id = fin_gastos.responsable_id) as responsable, fin_gastos.proveedor_id,
                (select pmo_proveedores.nombre_comercial from pmo_proveedores where fin_gastos.proveedor_id = pmo_proveedores.id) as nameproveedor
                FROM fin_gastos
                ORDER BY fin_gastos.fecha_solicitado";
        $this->content['gastos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByActividad($actividad_id)
    {
        $sql = "SELECT fin_gastos.id,fin_gastos.actividad_id,descripcion,monto,monto as montocopia, fin_gastos.fecha_ejercido,
                fin_gastos.status,fin_gastos.fecha_solicitado,
                fin_gastos.responsable_id,
                (select sys_users.nickname from sys_users where sys_users.id = fin_gastos.responsable_id) as responsable, fin_gastos.proveedor_id,
                (select pmo_proveedores.nombre_comercial from pmo_proveedores where fin_gastos.proveedor_id = pmo_proveedores.id) as nameproveedor
                FROM fin_gastos
                where fin_gastos.actividad_id = $actividad_id
                ORDER BY fin_gastos.fecha_solicitado";
        $gastos = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($gastos as $elemento){
            $g=(object)array();
            $g->actividad_id = $elemento['actividad_id'];
            $g->descripcion = $elemento['descripcion'];
            $g->fecha_ejercido = $elemento['fecha_ejercido'];
            $g->fecha_solicitado = $elemento['fecha_solicitado'];
            $g->id = $elemento['id'];
            $g->tipo = $elemento['tipo'];
            $g->monto = $elemento['monto'];
            $g->montocopia = number_format(floatval($elemento['montocopia']),2,'.',',');
            $g->responsable = $elemento['responsable'];
            $g->responsable_id = $elemento['responsable_id'];
            $g->status = $elemento['status'];
            $g->proveedor_id = $elemento['proveedor_id'];
            $g->nameproveedor = $elemento['nameproveedor'];
            array_push($nuevo,$g);
        }
        $this->content['gastos'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getById($id) {
        $sql = "SELECT fin_gastos.id, fin_gastos.actividad_id, fin_gastos.comprobado, fin_gastos.created,
                fin_gastos.monto, fin_gastos.monto as montocopia,fin_gastos.monto * 0.16 as iva,
                fin_gastos.monto + (fin_gastos.monto * 0.16) as total,fin_gastos.solicitud_id, fin_gastos.proveedor_id, 
                fin_gastos.tipo, fin_tipos_gastos.nombre as tipo_gasto, fin_gastos.fecha_ejercido, fin_gastos.descripcion as descripcion_gasto, fin_gastos.sumatoria_factura,
                pmo_proveedores.nombre_comercial, pmo_proveedores.razon_social, pmo_proveedores.rfc, pmo_proveedores.curp, 
                pmo_proveedores.direccion, pmo_proveedores.banco, pmo_proveedores.clabe, pmo_proveedores.banco2, pmo_proveedores.clabe2,pmo_proveedores.banco3, pmo_proveedores.clabe3,pmo_proveedores.banco4, pmo_proveedores.clabe4,pmo_proveedores.tarjeta1, pmo_proveedores.tarjeta2, pmo_proveedores.tarjeta3, pmo_proveedores.tarjeta4,
                pmo_proveedores.telefono, pmo_proveedores.contacto, pmo_proveedores.toka, fin_solicitudes.iva as tiene_iva,
                pmo_actividades.costo, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = pmo_actividades.id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'AUTORIZADO' or fin_solicitudes.status = 'SOLICITADO' or fin_solicitudes.status = 'PAGADO')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = pmo_actividades.id and fin_solicitudes.iva = false and fin_solicitudes.status = 'PAGADO'), 0) as total2))) as cantidad_disponible, pmo_actividades.nombre as nombre_actividad, fin_gastos.pago, fin_gastos.cuenta_pago
                from fin_gastos
                inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
                inner join pmo_proveedores on pmo_proveedores.id = fin_gastos.proveedor_id
                inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id
                inner join fin_tipos_gastos on fin_gastos.tipo = fin_tipos_gastos.id
                where fin_gastos.id = $id";
        $results = $this->db->query($sql)->fetchAll();
        $gastos = [];
        foreach ($results as $gasto) {
            $g=(object)array();
            $g->id = $gasto['id'];
            $g->actividad_id = $gasto['actividad_id'];
            $g->comprobado = $gasto['comprobado'];
            $g->created = $gasto['created'];
            $g->monto = number_format(floatval($gasto['monto']),2,'.',',');
            $g->montocopia = number_format(floatval($gasto['montocopia']),2,'.',',');
            $g->iva = number_format(floatval($gasto['iva']),2,'.',',');
            $g->total = number_format(floatval($gasto['total']),2,'.',',');
            $g->solicitud_id = $gasto['solicitud_id'];
            $g->proveedor_id = $gasto['proveedor_id'];
            $g->tipo = $gasto['tipo'];
            $g->tipo_gasto = $gasto['tipo_gasto'];
            $g->fecha_ejercido = $gasto['fecha_ejercido'];
            $g->descripcion_gasto = $gasto['descripcion_gasto'];
            $g->nombre_comercial = $gasto['nombre_comercial'];
            $g->razon_social = $gasto['razon_social'];
            $g->rfc = $gasto['rfc'];
            $g->curp = $gasto['curp'];
            $g->direccion = $gasto['direccion'];
            $g->banco = $gasto['banco'];
            $g->clabe = $gasto['clabe'];
            $g->banco2 = $gasto['banco2'];
            $g->clabe2 = $gasto['clabe2'];
            $g->banco3 = $gasto['banco3'];
            $g->clabe3 = $gasto['clabe3'];
            $g->banco4 = $gasto['banco4'];
            $g->clabe4 = $gasto['clabe4'];
            $g->tarjeta1 = $gasto['tarjeta1'];
            $g->tarjeta2 = $gasto['tarjeta2'];
            $g->tarjeta3 = $gasto['tarjeta3'];
            $g->tarjeta4 = $gasto['tarjeta4'];
            $g->telefono = $gasto['telefono'];
            $g->contacto = $gasto['contacto'];
            $g->toka = $gasto['toka'];
            $g->costo = number_format(floatval($gasto['costo']),2,'.',',');
            $g->cantidad_disponible = number_format(floatval($gasto['cantidad_disponible']),2,'.',',');
            $g->nombre_actividad = $gasto['nombre_actividad'];
            $g->sumatoria_factura = $gasto['sumatoria_factura'];
            if(intval($gasto['tiene_iva']) === 0){
                $g->monto_total_pagar = number_format(floatval($gasto['monto']),2,'.',',');
            } else {
                $g->monto_total_pagar = number_format(floatval($gasto['total']),2,'.',',');
            } 
            $g->tiene_iva = $gasto['tiene_iva'];
            $g->pago = $gasto['pago'];
            $g->cuenta_pago = $gasto['cuenta_pago'];
            array_push($gastos, $g);
        }
        
        $this->content['gasto'] = $gastos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBySolicitud($id) {
        $sql = "SELECT fin_gastos.id, fin_gastos.id as num, fin_gastos.actividad_id, fin_gastos.comprobado, fin_gastos.monto, fin_gastos.monto as montocopia,fin_gastos.monto * 0.16 as iva,fin_gastos.monto + (monto * 0.16) as total,fin_gastos.solicitud_id, fin_gastos.proveedor_id, fin_gastos.tipo, fin_gastos.fecha_ejercido, fin_gastos.descripcion as descripcion_gasto, fin_gastos.comprobado, fin_gastos.comprobado as cotizacion, fin_gastos.comprobado as factura, fin_gastos.comprobado as comprobante,  fin_tipos_gastos.nombre as tipo_gasto, fin_gastos.sumatoria_factura, fin_gastos.comision,
            (select pmo_actividades.nombre from pmo_actividades where pmo_actividades.id = fin_gastos.actividad_id) as nombre_actividad, 
            (select pmo_proveedores.nombre_comercial from pmo_proveedores where pmo_proveedores.id = fin_gastos.proveedor_id) as nombre_proveedor, fin_solicitudes.iva as boolean_iva, fin_gastos.concepto_id, fin_gastos.subconcepto_id,(select nombre from vnt_concepto where fin_gastos.concepto_id = vnt_concepto.id) as concepto,(select nombre from vnt_subconcepto where fin_gastos.subconcepto_id = vnt_subconcepto.id) as subconcepto, fin_gastos.pago
            from fin_gastos
            inner join fin_tipos_gastos on tipo = fin_tipos_gastos.id
            inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id
            and fin_gastos.solicitud_id = $id
            order by id desc";
        $results = $this->db->query($sql)->fetchAll();

        $gastos = [];
        $numero_gastos_pagados = 0;
        $numero_gastos_comprobados = 0;
        $i = 0;
        foreach ($results as $gasto) {
            $i++;
            $g=(object)array();
            $g->id = $gasto['id'];
            $g->concepto_id = $gasto['concepto_id'];
            $g->subconcepto_id = $gasto['subconcepto_id'];
            $g->concepto = $gasto['concepto'];
            $g->subconcepto = $gasto['subconcepto'];
            $g->indice_gasto = $i;

            $id = $gasto['id'];
            //$cotizaciones = [];
            $sql_cotizaciones = "SELECT sys_documents.id, sys_documents.name, sys_documents.doc_type, fin_cotizacion_gastos.id as cotizacion_id
                            FROM sys_documents
                            inner join fin_cotizacion_gastos 
                            on sys_documents.id = fin_cotizacion_gastos.documento_id 
                            and fin_cotizacion_gastos.gasto_id = $id";
            $cotizaciones = $this->db->query($sql_cotizaciones)->fetchAll();
            $g->cotizacion = $cotizaciones;
            // facturas
            $sql_facturas = "SELECT sys_documents.id, sys_documents.name, sys_documents.doc_type, fin_factura_gastos.id as factura_id
                            FROM sys_documents
                            inner join fin_factura_gastos 
                            on sys_documents.id = fin_factura_gastos.documento_id 
                            and fin_factura_gastos.gasto_id = $id";
            $facturas = $this->db->query($sql_facturas)->fetchAll();
            $g->factura = $facturas;
            // comprobantes
            $sql_comprobantes = "SELECT sys_documents.id, sys_documents.name, sys_documents.doc_type, fin_comprobante_gastos.id as comprobante_id
                            FROM sys_documents
                            inner join fin_comprobante_gastos 
                            on sys_documents.id = fin_comprobante_gastos.documento_id 
                            and fin_comprobante_gastos.gasto_id = $id";
            $comprobantes = $this->db->query($sql_comprobantes)->fetchAll();
            $g->comprobante = $comprobantes;

            $g->actividad_id = $gasto['actividad_id'];
            $g->monto = $gasto['monto'];
            $g->montocopia = number_format(floatval($gasto['montocopia']),2,'.',',');
            $g->iva = number_format(floatval($gasto['iva']),2,'.',',');
            $g->total = number_format(floatval($gasto['total']),2,'.',',');
            $g->totalsf = floatval($gasto['total']); // total sin formato
            $g->solicitud_id = $gasto['solicitud_id'];
            $g->proveedor_id = $gasto['proveedor_id'];
            $g->comision = $gasto['comision'];
            $g->tipo = $gasto['tipo'];
            $g->tipo_gasto = $gasto['tipo_gasto'];
            $g->fecha_ejercido = $gasto['fecha_ejercido'];
            if ($gasto['fecha_ejercido'] !== null) {
                $numero_gastos_pagados++;
            }
            $g->descripcion_gasto = $gasto['descripcion_gasto'];
            $g->nombre_actividad = $gasto['nombre_actividad'];
            $g->nombre_proveedor = $gasto['nombre_proveedor'];
            $g->sumatoria_factura = number_format(floatval($gasto['sumatoria_factura']),2,'.',',');
            if(intval($gasto['comprobado']) === 0){
                $g->comprobado = 'NO';
                $g->comprobado_cadena = 'NO';
            } else {
                $g->comprobado ='SI';
                $g->comprobado_cadena = 'SI';
                $numero_gastos_comprobados++;
            }
            $g->comprobado_cadena2 = $gasto['comprobado'];
            $g->pago = $gasto['pago'];
            if(intval($gasto['boolean_iva']) === 0){
                $g->total_verificar = number_format(floatval($gasto['monto']),2,'.',',');
            } else {
                $g->total_verificar = number_format(floatval($gasto['total']),2,'.',',');
            }
            //$g->num = $num;
            array_push($gastos, $g);
            //$num++;
        }
        
        $this->content['gastos'] = $gastos;
        $this->content['numero_gastos_pagados'] = $numero_gastos_pagados;
        $this->content['numero_gastos_comprobados'] = $numero_gastos_comprobados;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
            if (($solicitud->status === 'CREADO') || ($solicitud->autorizacion_id === 1 && $solicitud->status === 'POR AUTORIZAR') || ($solicitud->autorizacion_id === null && $solicitud->status === 'RECHAZADO')) {
                $gastos = new GastosSolicitudes();
                $gastos->setTransaction($tx);
                $gastos->actividad_id = intval($request['actividad_id']);
                $gastos->monto = floatval($request['monto']);
                $gastos->solicitud_id = intval($request['solicitud_id']);
                $gastos->sumatoria_factura = 0;

                if (trim($request['descripcion_gasto']) === ''){
                    $gastos->descripcion = null;
                } else {
                    $gastos->descripcion = trim(strtoupper($request['descripcion_gasto']));
                }
                if(intval($request['proveedor_id']) === 0){
                    $gastos->proveedor_id = null;
                } else {
                    $gastos->proveedor_id = intval($request['proveedor_id']);
                    $proveedor = Proveedores::findFirst($request['proveedor_id']);
                    if ($request['pago'] === 'banco1') {
                        $gastos->cuenta_pago = $proveedor->clabe;
                    }
                    if ($request['pago'] === 'banco2') {
                        $gastos->cuenta_pago = $proveedor->clabe2;
                    }
                    if ($request['pago'] === 'banco3') {
                        $gastos->cuenta_pago = $proveedor->clabe3;
                    }
                    if ($request['pago'] === 'banco4') {
                        $gastos->cuenta_pago = $proveedor->clabe4;
                    }
                    if ($request['pago'] === 'toka') {
                        $gastos->cuenta_pago = $proveedor->toka;
                    }
                    if ($request['pago'] === 'tarjeta1') {
                        $gastos->cuenta_pago = $proveedor->tarjeta1;
                    }
                    if ($request['pago'] === 'tarjeta2') {
                        $gastos->cuenta_pago = $proveedor->tarjeta2;
                    }
                    if ($request['pago'] === 'tarjeta3') {
                        $gastos->cuenta_pago = $proveedor->tarjeta3;
                    }
                    if ($request['pago'] === 'tarjeta4') {
                        $gastos->cuenta_pago = $proveedor->tarjeta4;
                    }
                }
                if(intval($request['tipo']) === 0){
                    $gastos->tipo = null;
                } else {
                    $gastos->tipo = intval($request['tipo']);
                }
                /*if($request['fecha_ejercido'] === ''){
                    $gastos->fecha_ejercido = null;
                } else {
                    $gastos->fecha_ejercido = date("Y/m/d", strtotime($request['fecha_ejercido']));
                }*/

                if($request['comprobado'] === 'SI'){
                    $gastos->comprobado = true;
                } else {
                    $gastos->comprobado = false;
                }
                $gastos->pago = $request['pago'];
                
                if ($gastos->create()) {
                    $monto = $gastos->monto;
                    $solicitud->setTransaction($tx);
                    $total = $solicitud->total;
                    $solicitud->total = $total + $monto;
                    $solicitud->sobrepasa_presupuesto = 'NO';
                    if($solicitud->update()) {
                        if($request['sobrepasa_presupuesto'] === 'SI') {
                            $validUser = Auth::getUserData($this->config);
                            $notas= new Notas();
                            $notas->setTransaction($tx);
                            $notas->usuario_id = $validUser->user_id;
                            $notas->solicitud_id=$solicitud->id;
                            $notas->nota=trim($request['notas']);
                            $notas->fecha = date("Y-m-d H:i:s");
                            $notas->perfil = 'SOLICITANTE';
                            if($notas->create()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el gasto a la solicitud.'];
                                $this->content['total'] = $solicitud->total;
                                $this->content['totalcopia'] = number_format($solicitud->total,2,'.',',');
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($notas);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el gasto.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el gasto a la solicitud.'];
                            $this->content['total'] = $solicitud->total;
                            $this->content['totalcopia'] = number_format($solicitud->total,2,'.',',');
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($solicitud);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el gasto.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($gastos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el gasto.'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta solicitud ya no puede ser modificada.'];
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
            $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
            if (($solicitud->autorizacion_id === null && $solicitud->status === 'CREADO') || ($solicitud->autorizacion_id === 1 && $solicitud->status === 'POR AUTORIZAR') || ($solicitud->autorizacion_id === null && $solicitud->status === 'RECHAZADO')) {
                $gastos = GastosSolicitudes::findFirst($request['id']);
                if ($gastos) {
                    $gastos->setTransaction($tx);
                    $monto = $gastos->monto;
                    $gastos->actividad_id = intval($request['actividad_id']);
                    $gastos->monto = floatval($request['monto']);
                    $nuevo_monto = floatval($request['monto']);
                    if (trim($request['descripcion_gasto']) === ''){
                        $gastos->descripcion = null;
                    } else {
                        $gastos->descripcion = trim(strtoupper($request['descripcion_gasto']));
                    }
                    if(intval($request['proveedor_id']) === 0){
                        $gastos->proveedor_id = null;
                    } else {
                        $gastos->proveedor_id = intval($request['proveedor_id']);
                        $proveedor = Proveedores::findFirst($request['proveedor_id']);
                        if ($request['pago'] === 'banco1') {
                            $gastos->cuenta_pago = $proveedor->clabe;
                        }
                        if ($request['pago'] === 'banco2') {
                            $gastos->cuenta_pago = $proveedor->clabe2;
                        }
                        if ($request['pago'] === 'banco3') {
                            $gastos->cuenta_pago = $proveedor->clabe3;
                        }
                        if ($request['pago'] === 'banco4') {
                            $gastos->cuenta_pago = $proveedor->clabe4;
                        }
                        if ($request['pago'] === 'toka') {
                            $gastos->cuenta_pago = $proveedor->toka;
                        }
                        if ($request['pago'] === 'tarjeta1') {
                            $gastos->cuenta_pago = $proveedor->tarjeta1;
                        }
                        if ($request['pago'] === 'tarjeta2') {
                            $gastos->cuenta_pago = $proveedor->tarjeta2;
                        }
                        if ($request['pago'] === 'tarjeta3') {
                            $gastos->cuenta_pago = $proveedor->tarjeta3;
                        }
                        if ($request['pago'] === 'tarjeta4') {
                            $gastos->cuenta_pago = $proveedor->tarjeta4;
                        }
                    }
                    if(intval($request['tipo']) === 0){
                        $gastos->tipo = null;
                    } else {
                        $gastos->tipo = intval($request['tipo']);
                    }
                    if($request['comprobado'] === 'SI'){
                        $gastos->comprobado = true;
                    } else {
                        $gastos->comprobado = false;
                    }
                    $gastos->pago = $request['pago'];

                    if ($gastos->update()) {
                        $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
                        if($solicitud){
                             $solicitud->setTransaction($tx);
                             $total = $solicitud->total - $monto;
                             $solicitud->total = $total + $nuevo_monto;
                             if($solicitud->update()){
                                if($request['sobrepasa_presupuesto'] === 'SI') {
                                    $validUser = Auth::getUserData($this->config);
                                    $notas= new Notas();
                                    $notas->setTransaction($tx);
                                    $notas->usuario_id = $validUser->user_id;
                                    $notas->solicitud_id=$solicitud->id;
                                    $notas->nota=trim($request['notas']);
                                    $notas->fecha = date("Y-m-d H:i:s");
                                    $notas->perfil = 'SOLICITANTE';
                                    if($notas->create()) {
                                        $this->content['result'] = 'success';
                                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el gasto.'];
                                        $this->content['total'] = $solicitud->total;
                                        $this->content['totalcopia'] = number_format($solicitud->total,2,'.',',');
                                        // $tx->commit();
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($notas);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el gasto.'];
                                        $tx->rollback();
                                    }
                                } else {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el gasto.'];
                                    $this->content['total'] = $solicitud->total;
                                    $this->content['totalcopia'] = number_format($solicitud->total,2,'.',',');
                                    $tx->commit();
                                }
                             } else {
                             $this->content['result'] = 'error';
                             $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                             $tx->rollback();
                            }
                        } else {
                            $this->content['error'] = Helpers::getErrors($solicitud);;
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la solicitud.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($gastos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el gasto.'];
                        $tx->rollback();
                    }
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta solicitud ya no puede ser modificada.'];
            }

        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function pagar ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
                $gastos = GastosSolicitudes::findFirst($request['id']);
                if ($gastos) {
                    $gastos->setTransaction($tx);
                    $gastos->fecha_ejercido = date("Y-m-d H:i:s");
                    $gastos->comision = floatval($request['comision']);
                    $gastos->cantidad_diferencia = floatval($request['cantidad_diferencia']);
                    if ($gastos->update()) {
                        $actividad_directa_id = $gastos->actividad_id;
                        $actividad_padre = Actividades::findFirst($actividad_directa_id);
                        $nivel = $actividad_padre->nivel;
                        $proyecto_id = $actividad_padre->proyecto_id;
                        //
                        for ($i=$nivel; $i>=1; $i--) {
                            $actividad_padre = Actividades::findFirst($actividad_directa_id);
                            if ($actividad_padre !== false) {
                                $actividad_padre->setTransaction($tx);
                                $actividad_padre->costo = $actividad_padre->costo + floatval($request['cantidad_diferencia']);
                                if ($actividad_padre->update()) {
                                    $this->content['result'] = 'success';
                                    $actividad_directa_id = $actividad_padre->padre_id;
                                } else {
                                    $this->content['error'] = Helpers::getErrors($actividad_padre);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                                    $tx->rollback();
                                }
                            }
                        }

                        if ($this->content['result'] === 'success') {
                                                $proyecto = Proyecto::findFirst($proyecto_id);
                                                if($proyecto){
                                                    $proyecto->setTransaction($tx);
                                                    $proyecto->presupuesto_actual = floatval($proyecto->presupuesto_actual) + floatval($request['cantidad_diferencia']);
                                                    if($proyecto->update()){
                                                        if (trim($request['nota'])!=='') {
                                                            //
                                                            $notas= new Notas();
                                                            $notas->setTransaction($tx);
                                                            $validUser = Auth::getUserData($this->config);
                                                            $notas->usuario_id = $validUser->user_id;
                                                            $notas->solicitud_id=$gastos->solicitud_id;
                                                            $notas->nota=trim($request['nota']);
                                                            $notas->fecha = date("Y-m-d H:i:s");
                                                            $notas->perfil = 'PAGOS';
                                                            if($notas->create()) {
                                                              $this->content['result'] = 'success';
                                                              $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha pagado el gasto'];
                                                              $tx->commit();
                                                            } else {
                                                              $this->content['error'] = Helpers::getErrors($notas);
                                                              $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo pagar el gasto.'];
                                                              $tx->rollback();
                                                            }
                                                            //
                                                        } else {
                                                            $this->content['result'] = 'success';
                                                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha pagado el gasto.'];
                                                            $tx->commit();
                                                        }
                                                    } else {
                                                        $this->content['result'] = 'success';
                                                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No se pudo pagar el gasto, el proyecto no fue encontrado.'];
                                                        $tx->rollback();   
                                                    }
                                                } else {
                                                    $this->content['result'] = 'success';
                                                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'No se pudo pagar el gasto, el proyecto no fue encontrado.'];
                                                    $tx->rollback();
                                                }
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($gastos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo pagar el gasto.'];
                        $tx->rollback();
                    }
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function comprobar ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $gastos = GastosSolicitudes::findFirst($request['id']);
            if ($gastos) {
                $gastos->setTransaction($tx);
                if($request['comprobar_gasto'] === 'SI'){
                    $gastos->comprobado = true;
                } else {
                    $gastos->comprobado = false;
                }
                if ($gastos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el gasto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($gastos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el gasto.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateConcepto ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $gastos = GastosSolicitudes::findFirst($request['id']);
            if ($gastos) {
                $gastos->setTransaction($tx);
                $gastos->concepto_id = intval($request['concepto_id']);
                $gastos->subconcepto_id = intval($request['subconcepto_id']);
                if ($gastos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el gasto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($gastos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el gasto.'];
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
            $gastos = GastosSolicitudes::findFirst($request['id']);
            $gastos->setTransaction($tx);
            $monto = $gastos->monto;

            if ($gastos->delete()) {
                $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
                if($solicitud){
                    $solicitud->setTransaction($tx);
                    $total = $solicitud->total - $monto;
                    $solicitud->total = $total;
                    if ($solicitud->total < 1) {
                        $solicitud->status = 'CREADO';
                        $solicitud->autorizacion_id = null;
                    }
                    if($solicitud->update()){
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado este gasto.'];
                        $this->content['total'] = $solicitud->total;
                        $this->content['totalcopia'] = number_format($solicitud->total,2,'.',',');
                        $tx->commit();
                    } else {
                        $this->content['result'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitud);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }
            } else {
                $this->content['error'] = Helpers::getErrors($gastos);
                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                $tx->rollback();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}