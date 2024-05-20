<?php

use Phalcon\Mvc\Controller;

class RemisionesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];
    public $batuta_url = '';
    
    public function onConstruct()
    {
        $this->batuta_url = ($_SERVER['SERVER_NAME'] == 'api.impact.antfarm.mx' ? 'batuta.antfarm.mx' : 'batuta.beta.antfarm.mx');
    }

    public function getAll ()
    {
        $sql = "SELECT vnt_remisiones.id, empresa_id, cliente_id, to_char(vnt_remisiones.fecha_venta, 'DD/MM/YYYY') as fecha_venta, vnt_remisiones.status, vnt_remisiones.created, vnt_empresa.razon_social as empresa, vnt_clientes.razon_social as cliente, status_timbrado, message, id_request, uuid, vnt_remisiones.tipo, vnt_remisiones.folio, (select COALESCE((SELECT sum(monto_factura) from vnt_remisiones_facturas where remision_id = vnt_remisiones.id), 0)) as monto_total, status_cobranza
            from vnt_remisiones
            join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id
            order by vnt_remisiones.id desc";

        $data = $this->db->query($sql)->fetchAll();
        foreach ($data as $key => $remision) {
            $data[$key]['monto_total'] = number_format(floatval($data[$key]['monto_total']),2,'.',',');
            if ($data[$key]['status_cobranza'] == 'EMITIDO') {
              $data[$key]['color'] = 'amber';
              $data[$key]['icon'] = 'fas fa-arrow-circle-right';
            } else if ($data[$key]['status_cobranza'] == 'ABONADO') {
              $data[$key]['color'] = 'blue';
              $data[$key]['icon'] = 'fas fa-check-circle';
            } else if ($data[$key]['status_cobranza'] == 'PAGADO') {
              $data[$key]['color'] = 'green';
              $data[$key]['icon'] = 'done_all';
            } else if ($data[$key]['status_cobranza'] == 'DESCONTADO') {
              $data[$key]['color'] = 'lime';
              $data[$key]['icon'] = 'fas fa-dollar-sign';
            }
        }
        $this->content['remisiones'] = $data;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy ($empresa_id, $cliente_id, $status)
    {
        intval($empresa_id) > 0 ? $emp = " and vnt_remisiones.empresa_id = ".intval($empresa_id) : $emp = "";
        intval($cliente_id) > 0 ? $cli = " and vnt_remisiones.cliente_id = ".intval($cliente_id) : $cli = "";
        $status != 'all' ? $sta = " and status_cobranza ="."'$status'" : $sta = "";
        $sql = "SELECT vnt_remisiones.id, vnt_remisiones.empresa_id, vnt_remisiones.cliente_id, to_char(vnt_remisiones.fecha_venta, 'DD/MM/YYYY') as fecha_venta, vnt_remisiones.status, to_char(vnt_remisiones.created, 'DD/MM/YYYY HH24:MI') as created, vnt_empresa.razon_social as empresa, vnt_clientes.razon_social as cliente, vnt_remisiones.status_timbrado, vnt_remisiones.message, vnt_remisiones.id_request, vnt_remisiones.uuid, vnt_remisiones.tipo, vnt_remisiones.folio, (select COALESCE((SELECT sum(monto_factura) from vnt_remisiones_facturas where remision_id = vnt_remisiones.id), 0)) as monto_total, vnt_remisiones.status_cobranza,vnt_remisiones.cancelado, vnt_remisiones_facturas.uuid as folio_fiscal,vnt_remisiones_facturas.folio_xml as folio_interno
            from vnt_remisiones
            join vnt_remisiones_facturas 
            on vnt_remisiones.id = vnt_remisiones_facturas.remision_id
            join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id {$emp}
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id {$cli} {$sta}
            order by vnt_remisiones.id desc";

        $data = $this->db->query($sql)->fetchAll();
        foreach ($data as $key => $remision) {
            $data[$key]['monto_total'] = number_format(floatval($data[$key]['monto_total']),2,'.',',');
            if ($data[$key]['status_cobranza'] == 'EMITIDO') {
              $data[$key]['color'] = 'amber';
              $data[$key]['icon'] = 'fas fa-arrow-circle-right';
            } else if ($data[$key]['status_cobranza'] == 'ABONADO') {
              $data[$key]['color'] = 'blue';
              $data[$key]['icon'] = 'fas fa-check-circle';
            } else if ($data[$key]['status_cobranza'] == 'PAGADO') {
              $data[$key]['color'] = 'green';
              $data[$key]['icon'] = 'done_all';
            } else if ($data[$key]['status_cobranza'] == 'DESCONTADO') {
              $data[$key]['color'] = 'lime';
              $data[$key]['icon'] = 'fas fa-dollar-sign';
            }
        }
        $this->content['remisiones'] = $data;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function get ($id)
    {
        $sql = "SELECT vnt_remisiones.id, empresa_id, cliente_id, to_char(vnt_remisiones.fecha_venta, 'DD/MM/YYYY') as fecha_venta, vnt_remisiones.status, to_char(vnt_remisiones.created, 'YYYY-MM-DD HH:MI:SS') as created, vnt_empresa.razon_social as empresa, vnt_clientes.razon_social as cliente, status_timbrado, message, id_request, uuid, vnt_remisiones.tipo, vnt_remisiones.folio
            from vnt_remisiones
            join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id
            join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id
            where vnt_remisiones.id = {$id}
            order by vnt_remisiones.id desc";
        $this->content['remision'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getDetalles ($remision_id)
    {
        $sql = "SELECT inv_productos.nombre as producto_nombre,
                    concat(inv_tipos_presentaciones.codigo_sat, ' - ', inv_tipos_presentaciones.nombre) as tipo_unidad,
                    vnt_remision_detalles.*,
                    vnt_remision_detalles.cantidad * vnt_remision_detalles.precio_unitario as subtotal,
                    vnt_remision_detalles.cantidad * vnt_remision_detalles.precio_unitario + vnt_remision_detalles.impuesto_importe as total
                from vnt_remision_detalles
                join inv_productos on inv_productos.id = vnt_remision_detalles.producto_id
                join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id 
                where vnt_remision_detalles.remision_id = {$remision_id}
                order by inv_productos.nombre desc";
        $this->content['detalles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getDatosFiscales ($remision_id)
    {
        $sql = "SELECT folio_fiscal, serie, fecha_factura, tipo_comprobante, metodo_pago, forma_pago, uso_cfdi, razon_social, rfc, lugar_expedicion, folio
                from vnt_remisiones
                join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id
                where vnt_remisiones.id = {$remision_id}
                limit 1";
        $this->content['remision'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getPagos ($remision_id)
    {
        $sql = "SELECT * from vnt_remision_pagos where remision_id = {$remision_id} order by num_parcialidad";
        $this->content['pagos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function keepChecking ($remision_id)
    {
        $sql = "SELECT * from vnt_remision_pagos where remision_id = {$remision_id} and (status_timbrado = 3 or status_timbrado = 4 or status_timbrado = 5) order by num_parcialidad";
        $check = $this->db->query($sql)->fetchAll();
        if ($check) {
            $this->content['result'] = true;
        } else {
            $this->content['result'] = false;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getNextFolio ($remision_id, $serie)
    {
        $folio = 1;
        $remision = Remisiones::findFirst($remision_id);
        if ($remision) {
            $nextFolio = Remisiones::findFirst("empresa_id = {$remision->empresa_id} and serie = '".$serie."' and id != {$remision->id} order by id desc");
            if ($nextFolio) {
                $folio = $nextFolio->folio_fiscal + 1;
            }
        }
        $this->content['folio_fiscal'] = $folio;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFolioConsecutivo ($tipo) {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM vnt_remisiones where tipo = '$tipo' and folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = date('Y') . '00001';
        }
        
        $this->content['folio'] = $nuevo_folio;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getFolio ($tipo) {
        if ($tipo == 'HISTÓRICO') {
            $anio = '2020';
        } else {
            $anio = date('Y');
        }
        $sql = "SELECT max(folio) as folio FROM vnt_remisiones where tipo = '$tipo' and folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = $anio . '00001';
        }
        return $nuevo_folio;
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $remisiones = new Remisiones();
            $remisiones->setTransaction($tx);
            $remisiones->fecha_venta = $request['fecha_venta'];
            $remisiones->empresa_id = intval($request['empresa_id']);
            $remisiones->cliente_id = intval($request['cliente_id']);
            $remisiones->tipo = $request['tipo'];
            $remisiones->status = 'NUEVO';
            $remisiones->folio = $this->getFolio($request['tipo']);
            if ($request['tipo'] == 'HISTÓRICO') {
                $remisiones->metodo_pago = !empty($request['metodo_pago']) ? $request['metodo_pago'] : NULL;
                if ($request['metodo_pago'] == 'PUE') {
                    $remisiones->status_cobranza = 'EMITIDO';
                } else {
                    $remisiones->status_cobranza = 'EMITIDO';
                }
            }
            if ($remisiones->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la remisión '.$remisiones->folio];
                $this->content['insert'] = $remisiones;
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($remisiones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
                $tx->rollback();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function saveWithFactura ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $remisiones = new Remisiones();
            $remisiones->setTransaction($tx);
            $remisiones->fecha_venta = $request['fecha_venta'];
            $remisiones->empresa_id = intval($request['empresa_id']);
            $remisiones->cliente_id = intval($request['cliente_id']);
            $remisiones->tipo = $request['tipo'];
            $remisiones->status = 'NUEVO';
            $remisiones->folio = $this->getFolio($request['tipo']);
            if ($request['tipo'] == 'HISTÓRICO') {
                $remisiones->metodo_pago = !empty($request['metodo_pago']) ? $request['metodo_pago'] : NULL;
                if ($request['metodo_pago'] == 'PUE') {
                    $remisiones->status_cobranza = 'EMITIDO';
                } else {
                    $remisiones->status_cobranza = 'EMITIDO';
                }
                if ($request['amortizacion'] == 'true') {
                    $remisiones->status_cobranza = 'PAGADO';
                }
            }
            if ($remisiones->create()) {
                $remisionesFacturas = RemisionesFacturas::findFirst($request['factura_id']);
                if ($remisionesFacturas) {
                    $remisionesFacturas->setTransaction($tx);
                    $remisionesFacturas->remision_id = $remisiones->id;
                    $remisionesFacturas->name = trim($request['nombre']);
                    if ($remisionesFacturas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la remisión '.$remisiones->folio];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($remisionesFacturas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
                        $tx->rollback();
                    }
                }
            } else {
                $this->content['error'] = Helpers::getErrors($remisiones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
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
            $remisiones = Remisiones::findFirst($request['id']);
            if ($remisiones) {
                $remisiones->fecha_venta = $request['fecha_venta'];
                $remisiones->empresa_id = intval($request['empresa_id']);
                $remisiones->cliente_id = intval($request['cliente_id']);
                $remisiones->tipo = $request['tipo'];
                if ($remisiones->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la remision.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($remisiones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la remision.'];
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
                    $remisiones = Remisiones::findFirst(intval($request['id']));
                    $remisiones->setTransaction($tx);
                    $tipo = $remisiones->tipo;
                    if ($remisiones->delete()) {
                        $rDetalles = RemisionDetalles::findFirst('remision_id = '.$request['id']);
                        if ($rDetalles) {
                            $rDetalles->setTransaction($tx);
                            if ($rDetalles->delete()) {
                                if ($tipo == 'HISTÓRICO') {
                                    $facturas = RemisionesFacturas::findFirst('remision_id ='.$request['id']);
                                    if ($facturas) {
                                        $facturas->setTransaction($tx);
                                        if ($facturas->delete()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($facturas);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    $this->content['result'] = 'success';
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($rDetalles);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($remisiones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la remision.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function saveProduct ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $producto = Productos::findFirst($request['producto_id']);
            if ($producto) {
                $remision = Remisiones::findFirst($request['remision_id']);
                $cliente = VntClientes::findFirst($remision->cliente_id);
                $varPrecio = (!empty($cliente->precio_lista)) ? 'precio_' . $cliente->precio_lista : 'precio_a';
                $rDetalles = new RemisionDetalles();
                $rDetalles->setTransaction($tx);
                $rDetalles->producto_id = intval($request['producto_id']);
                $rDetalles->remision_id = intval($request['remision_id']);
                $rDetalles->cantidad = intval($request['cantidad']);
                $rDetalles->descripcion = !empty($request['descripcion']) ? $request['descripcion'] : NULL;
                $rDetalles->precio_unitario = $producto->$varPrecio;
                if ($request['impuesto'] == 'true') {
                    $rDetalles->impuesto = true;
                    $rDetalles->impuesto_importe = intval($request['cantidad']) * $producto->$varPrecio * 0.16;
                }
                if ($rDetalles->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha agregado el producto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($rDetalles);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No ha agregado el producto.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            //$this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    /* public function updateProduct ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $rDetalles = RemisionDetalles::findFirst($request['id']);
            if ($rDetalles) {
                $rDetalles->setTransaction($tx);
                $rDetalles->cantidad = $request['cantidad'];
                $rDetalles->descripcion = $request['descripcion'];
                if ($request['impuesto'] == 'true') {
                    $rDetalles->impuesto = true;
                    $rDetalles->impuesto_importe = intval($request['cantidad']) * $rDetalles->$precio_unitario * 0.16;
                } else {
                    $rDetalles->impuesto = false;
                    $rDetalles->impuesto_importe = 0;  
                }
                if ($rDetalles->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el producto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($rDetalles);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha actualizado el producto.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    } */

    public function deleteProduct ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $rDetalles = RemisionDetalles::findFirst($request['id']);
                if ($rDetalles) {
                    $rDetalles->setTransaction($tx);
                    if ($rDetalles->delete()) {    
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el concepto.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($rDetalles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No ha borrado el concepto.'];
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

    public function updateFiscal ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $fiscal = Remisiones::findFirst($request['id']);
            if ($fiscal) {
                $fiscal->setTransaction($tx);
                $fiscal->folio_fiscal = !empty($request['folio_fiscal']) ? $request['folio_fiscal'] : NULL;
                $fiscal->serie = !empty($request['serie']) ? $request['serie'] : NULL;
                $fiscal->fecha_factura = !empty($request['fecha_factura']) ? $request['fecha_factura'] : NULL;
                $fiscal->lugar_expedicion = !empty($request['lugar_expedicion']) ? $request['lugar_expedicion'] : NULL;
                $fiscal->tipo_comprobante = !empty($request['tipo_comprobante']) ? $request['tipo_comprobante'] : NULL;
                $fiscal->metodo_pago = !empty($request['metodo_pago']) ? $request['metodo_pago'] : NULL;
                $fiscal->forma_pago = !empty($request['forma_pago']) ? $request['forma_pago'] : NULL;
                $fiscal->uso_cfdi = !empty($request['uso_cfdi']) ? $request['uso_cfdi'] : NULL;
                if ($fiscal->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se han actualizado los datos fiscales.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($fiscal);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se han actualizado los datos fiscales.'];
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

    private function getexistenciasproducto ($almacen, $producto) {
        $sql = "SELECT * from get_existencias($almacen, $producto, null, null)";
        $existencias = $this->db->query($sql)->fetchAll();
        return $existencias;
    }

    private function getFolioMovimiento ($tipo_movimiento) {
        $tipo_movimiento = intval($tipo_movimiento);
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM inv_movimientos where tipo_id = $tipo_movimiento and folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = date('Y') . '00001';
        }
        return $nuevo_folio;
    }

    private function getAlmacenProductoTerminado ($empresa_id) {
        $almacen = 0;
        $almacen_producto_terminado = Almacenes::findFirst("empresa_id = " . $empresa_id . " AND tipo = " . "'PRODUCTO TERMINADO'");
        if ($almacen_producto_terminado) {
            $almacen = $almacen_producto_terminado->id;
        }
        return $almacen;
    }

    private function getDetailsRemision ($remision_id, $modo) {
        if ($modo == 'compacto') {
            $sql = "SELECT producto_id, sum(cantidad) as cantidad from vnt_remision_detalles where remision_id = $remision_id group by producto_id";
        } else {
            $sql = "SELECT producto_id, cantidad, precio_unitario from vnt_remision_detalles where remision_id = $remision_id";
        }
        
        $details = $this->db->query($sql)->fetchAll();
        return $details;
    }

    public function changeStatus ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $remisiones = Remisiones::findFirst($request['remision_id']);
            $this->content['result'] = 'success';
            if ($remisiones) {
                $remisiones->setTransaction($tx);
                $remisiones->status = $request['status'];
                if ($remisiones->update()) {
                    if ($request['status'] == 'REMISIONADO' && $request['tipo'] == 'VENTA') {
                        $rDetalles = $this->getDetailsRemision($remisiones->id, 'compacto');
                        $almacen = $this->getAlmacenProductoTerminado($remisiones->empresa_id);
                        if ($almacen == 0) {
                            $this->content['result'] = 'Error';
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status, no hay almacén de producto terminado para verificar existencias.'];
                            $tx->rollback();
                        }
                        foreach ($rDetalles as $detalle) {
                            $cantidades = $this->getexistenciasproducto($almacen, $detalle['producto_id']);
                            if (count($cantidades) > 0) {
                                if ($cantidades[0]['existencia'] == 0 || ($cantidades[0]['existencia'] < intval($detalle['cantidad']))) {
                                    $this->content['result'] = 'Error';
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status, no hay existencias del producto o éstas son insuficientes.'];
                                    $tx->rollback();
                                }
                            } else {
                                $this->content['result'] = 'Error';
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status, no hay existencias del producto en el almacén.'];
                                $tx->rollback();
                            }
                        }
                        // crear la salida
                        $rDetalles = $this->getDetailsRemision($remisiones->id, 'detalle');
                        $movimientos = new Movimientos();
                        $movimientos->setTransaction($tx);
                        $validUser = Auth::getUserData($this->config);
                        $movimientos->created_by = $validUser->user_id;
                        $movimientos->tipo_id = 2;
                        $movimientos->folio = $this->getFolioMovimiento(2);
                        $movimientos->almacen_id = $almacen;
                        $movimientos->empresa_id = $remisiones->empresa_id;
                        $movimientos->status = 'EJECUTADO';
                        $movimientos->created = date("Y/m/d");
                        $movimientos->remision_id = $remisiones->id;
                        if ($movimientos->create()) {
                            foreach ($rDetalles as $detail) {
                                $detalle = new MovimientosDetalles();
                                $detalle->setTransaction($tx);
                                $detalle->created_by = $validUser->user_id;
                                $detalle->movimiento_id = $movimientos->id;
                                $detalle->presentacion_id = Productos::findFirst($detail['producto_id'])->presentacion_id;
                                $detalle->producto_id = $detail['producto_id'];
                                $detalle->cantidad = $detail['cantidad'];
                                $detalle->costo = $detail['precio_unitario'];
                                if ($detalle->create()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($detalle);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                                    $tx->rollback();
                                }
                            }
                        }
                    } else {
                        $this->content['result'] == 'success';
                    }
                }
                if ($this->content['result'] == 'success') {
                    // $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el status.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($remisiones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function timbrar ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $remision = Remisiones::findFirst($request['remision_id']);
            $remision->setTransaction($tx);
            $cliente = VntClientes::findFirst($remision->cliente_id);
            $rDetalles = RemisionDetalles::find('remision_id = ' . $remision->id);
            $empresa = Empresa::findFirst($remision->empresa_id);
            if (is_null($empresa->batuta_id)) {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La empresa no cuenta con un ID para timbrar.'];
                $tx->rollback();
            }
            $subtotal = 0;
            $total = 0;
            $totalImpuestosTrasladados = 0;
            $cabezera = $cuerpo = '';
            $fecha = str_replace(' ', 'T', $remision->fecha_factura);
            foreach ($rDetalles as $key => $detalle) {
                
                $cuerpo =  $cuerpo.'
                                <concepto>
                                    <cantidad>'.$detalle->cantidad.'</cantidad>
                                    <unidad>'.$detalle->Productos->TiposPresentaciones->nombre.'</unidad>
                                    <claveUnidad>'.$detalle->Productos->TiposPresentaciones->codigo_sat.'</claveUnidad>
                                    <claveProdServ>'.$detalle->Productos->clave_producto_id.'</claveProdServ>
                                    <noIdentificacion>'.$detalle->Productos->codigo.'</noIdentificacion>
                                    <descripcion>'.$detalle->descripcion.'</descripcion>
                                    <valorUnitario>'.$detalle->precio_unitario.'</valorUnitario>
                                    <importe>'.number_format($detalle->precio_unitario * $detalle->cantidad,2,'.','').'</importe>';
                $subtotal += number_format($detalle->precio_unitario * $detalle->cantidad,2,'.',''); 
                if ($detalle->impuesto) {
                    $totalImpuestosTrasladados += $detalle->impuesto_importe;
                    $cuerpo .= '<impuestos><traslados><traslado>
                                    <base>'.number_format($detalle->precio_unitario * $detalle->cantidad,2,'.','').'</base>
                                    <impuesto>002</impuesto>
                                    <tipofactor>Tasa</tipofactor>
                                    <tasa>0.160000</tasa>
                                    <importe>'.$detalle->impuesto_importe.'</importe>
                                </traslado></traslados></impuestos>';
                }
                $cuerpo = $cuerpo . '</concepto>';
            }
            $cuerpo = $cuerpo.'</conceptos>';
            if ($totalImpuestosTrasladados > 0) {
                $cuerpo .= '<impuestos>
                                <traslados>
                                    <traslado>
                                        <impuesto>002</impuesto>
                                        <tipofactor>Tasa</tipofactor>
                                        <tasa>0.160000</tasa>
                                        <importe>'.$totalImpuestosTrasladados.'</importe>
                                    </traslado>
                                </traslados>
                                <totalImpuestosTrasladados>'.$totalImpuestosTrasladados.'</totalImpuestosTrasladados>
                                </impuestos>';
            }
            $cuerpo = $cuerpo.'</factura></emision>';
            $total = $subtotal + $totalImpuestosTrasladados;
            $cabezera = '<emision>
                <cliente>'.$empresa->batuta_id.'</cliente>
                <factura>
                    <data>
                        <serie>'.$remision->serie.'</serie>
                        <folio>'.$remision->folio_fiscal.'</folio>
                        <fecha>'.$fecha.'</fecha>
                        <formaDePago>'.$remision->SatPaymentForms->clave.'</formaDePago>
                        <subtotal>'.number_format($subtotal,2,'.','').'</subtotal>
                        <total>'.number_format($total,2,'.','').'</total>
                        <metodoDePago>'.$remision->metodo_pago.'</metodoDePago>
                        <tipoDeComprobante>'.$remision->tipo_comprobante.'</tipoDeComprobante>
                        <lugarDeExpedicion>'.$remision->lugar_expedicion.'</lugarDeExpedicion>
                        <moneda>MXN</moneda>
                    </data>
                    <receptor>
                            <rfc>'.$cliente->rfc.'</rfc>
                            <nombre>'.$cliente->razon_social.'</nombre>
                            <usoCFDI>'.$remision->SatUsoCFDI->clave.'</usoCFDI>
                    </receptor>
                    <conceptos>';
            $emision = $cabezera . $cuerpo;

            $serviceURL = 'http://'.$this->batuta_url.'/api/get_emision';
            $curl = curl_init($serviceURL);
            $curlPostData = [
                'data' => $emision,
                'filename' => $remision->folio_fiscal . $remision->serie . '_' . $remision->empresa_id . '-' . $remision->cliente_id . '.xml',
                'flavour' => 'Facturacion',
                'scheduled' => ''
            ];
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);
            $curlResponse = curl_exec($curl);
            
            $data = json_decode($curlResponse);
            curl_close($curl);
            if ($data->result) {
                $remision->status_timbrado = 4;
                $remision->id_request = $data->uuid;
                $remision->message = $data->message;
                $remision->update();
                $this->content['result'] = 'success';
            } else {
                $remision->message = $data->message;
                $remision->update();
            }
            $tx->commit();
            $this->content['emision'] = $emision;
            $this->content['data'] = $data;
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function cancelInvoice ()
    {   
        $tx = $this->transactions->get();
        $request = $this->request->getPut();
        try {
            $remision = Remisiones::findFirst($request['id']);
            if ($remision) {
                $empresa = Empresa::findFirst($remision->empresa_id);
                $CancelTimbrado = "<cancelarTimbrado>
                    <client>".$empresa->batuta_id."</client>
                    <uuid>{$remision->uuid}</uuid>
                    <type>cancelarTimbrado</type>
                    </cancelarTimbrado>";

                $service_url = 'http://'.$this->batuta_url.'/api/get_emision';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    'data' => $CancelTimbrado,
                    'filename' => 'ctimbrado_' . $remision->uuid . '.xml',
                    'flavour' => 'CancelarFacturaNew',
                    'scheduled' => '',
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                if ($curl_response !== false) {
                    $response = json_decode($curl_response);
                    $remision->setTransaction($tx);
                    if ($remision) {
                        if ($response->result) {
                            $remision->status_timbrado = 3;
                            $remision->id_cancelacion = $response->uuid;
                            $remision->message_cancelacion = $response->message;
                            $remision->update();
                            $this->content['result'] = 'success';
                        } else {
                            $remision->message_cancelacion = $response->message;
                            $remision->update();
                        }
                    }
                }

                if ($this->content['result'] == 'success') {
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }
        $this->content['response'] = $response;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function cancelPago ()
    {   
        $tx = $this->transactions->get();
        $request = $this->request->getPut();
        try {
            $pago = RemisionPagos::findFirst($request['id']);
            if ($pago) {
                $CancelTimbrado = "<cancelarTimbrado>
                    <client>1</client>
                    <uuid>{$pago->uuid}</uuid>
                    <type>cancelarTimbrado</type>
                    </cancelarTimbrado>";

                $service_url = 'http://'.$this->batuta_url.'/api/get_emision';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    'data' => $CancelTimbrado,
                    'filename' => 'ctimbrado_' . $pago->uuid . '.xml',
                    'flavour' => 'CancelarFacturaNew',
                    'scheduled' => '',
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                if ($curl_response !== false) {
                    $response = json_decode($curl_response);
                    $pago->setTransaction($tx);
                    if ($pago) {
                        if ($response->result) {
                            $pago->status_timbrado = 3;
                            $pago->id_cancelacion = $response->uuid;
                            $pago->message_cancelacion = $response->message;
                            $pago->update();
                            $this->content['result'] = 'success';
                        } else {
                            $pago->message_cancelacion = $response->message;
                            $pago->update();
                        }
                    }
                }

                if ($this->content['result'] == 'success') {
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }
        $this->content['response'] = $response;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function checkInvoice ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $remisiones = Remisiones::findFirst($request['id']);
            if ($remisiones && $remisiones->status_timbrado == 4) {
                $remisiones->setTransaction($tx);
                $service_url = 'http://'.$this->batuta_url.'/api/info_factura';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    'uuid' => $remisiones->id_request,
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                if ($curl_response === false) {
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    die('error occured during curl exec. Additioanl info: ' . var_export($info));
                } else {
                    $response = json_decode($curl_response);
                    if ($response->status == 'done') {
                        $service_url = 'http://'.$this->batuta_url.'/api/get_uuid';
                        $curl = curl_init($service_url);
                        $curl_post_data = array(
                            'uuid' => $remisiones->id_request,
                        );
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                        $curl_response2 = curl_exec($curl);
                        if ($curl_response2 === false) {
                            $info = curl_getinfo($curl);
                            curl_close($curl);
                            die('error occured during curl exec. Additioanl info: ' . var_export($info));
                        } else {
                            $uuid_factura = $curl_response2;
                            $remisiones->status_timbrado = 1;
                            $remisiones->uuid = $uuid_factura;
                            $remisiones->message = $response->message;
                            if ($remisiones->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($remisiones);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        }
                    } else if ($response->status == 'incoming' || $response->status == 'in progress' || $response->status == 'new') {
                        $remisiones->message = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    } else  if ($response->status == 'Error' || $response->status == 'error')  {
                        $remisiones->status_timbrado = 6;
                        $remisiones->message = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }
                }
                if ($this->content['result'] == 'success') {
                    $tx->commit();
                }
            } else if ($remisiones && $remisiones->status_timbrado == 3) {
                $remisiones->setTransaction($tx);
                $service_url = 'http://'.$this->batuta_url.'/api/info_general';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    'uuid' => $remisiones->id_cancelacion,
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                if ($curl_response === false) {
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    die('error occured during curl exec. Additioanl info: ' . var_export($info));
                } else {
                    $response = json_decode($curl_response);
                    if ($response->status == 'done') {
                        $remisiones->status_timbrado = 5;
                        $remisiones->id_cancelacion_asc = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    } else if ($response->status == 'incoming' || $response->status == 'in progress' || $response->status == 'new') {
                        $remisiones->message_cancelacion = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    } else  if ($response->status == 'Error' || $response->status == 'error')  {
                        $remisiones->status_timbrado = 1;
                        $remisiones->message_cancelacion = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }
                }
                if ($this->content['result'] == 'success') {
                    $tx->commit();
                }
            } else if ($remisiones && $remisiones->status_timbrado == 5) {
                $service_url = 'http://'.$this->batuta_url.'/api/get_status_cancelacion';
                $curl = curl_init($service_url);
                $curl_post_data = array(
                    'uuid' => $remisiones->id_cancelacion_asc,
                );
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                $curl_response = curl_exec($curl);
                if ($curl_response === false) {
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    die('error occured during curl exec. Additioanl info: ' . var_export($info));
                } else {
                    $response = json_decode($curl_response);
                    if ($response->ret_status == 200) {
                        if ($response->status) {
                            $remisiones->status_timbrado = 2;
                            $remisiones->message_cancelacion = $response->message;
                            $remisiones->acuseSat_cancelacion = $response->acuseSat;
                            if ($remisiones->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($remisiones);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $status = explode('|', str_replace(' ', '', $response->message))[0];
                            if ($status == 211) {
                                $remisiones->message_cancelacion = $response->message;
                                if ($remisiones->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($remisiones);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            } else {
                                $remisiones->status_timbrado = 7;
                                $remisiones->message_cancelacion = $response->message;
                                $remisiones->acuseSat_cancelacion = $response->acuseSat;
                                if ($remisiones->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($remisiones);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }
                        }
                    } else if ($response->ret_status == 211) {
                        $remisiones->message_cancelacion = $response->message;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }else {
                        $remisiones->status_timbrado = 7;
                        $remisiones->message_cancelacion = $response->message;
                        $remisiones->acuseSat_cancelacion = $response->acuseSat;
                        if ($remisiones->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($remisiones);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }
                }
                if ($this->content['result'] == 'success') {
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->content['response'] = $response;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function checkPagos ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $pagos = RemisionPagos::find('status_timbrado != 1 and remision_id = '.$request['remision_id']);
            if ($pagos) {
                foreach ($pagos as $key => $pago) {
                    if ($pago && $pago->status_timbrado == 4) {
                        $pago->setTransaction($tx);
                        $service_url = 'http://'.$this->batuta_url.'/api/info_factura';
                        $curl = curl_init($service_url);
                        $curl_post_data = array(
                            'uuid' => $pago->id_request,
                        );
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                        $curl_response = curl_exec($curl);
                        if ($curl_response === false) {
                            $info = curl_getinfo($curl);
                            curl_close($curl);
                            die('error occured during curl exec. Additioanl info: ' . var_export($info));
                        } else {
                            $response = json_decode($curl_response);
                            if ($response->status == 'done') {
                                $service_url = 'http://'.$this->batuta_url.'/api/get_uuid';
                                $curl = curl_init($service_url);
                                $curl_post_data = array(
                                    'uuid' => $pago->id_request,
                                );
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($curl, CURLOPT_POST, true);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                                $curl_response2 = curl_exec($curl);
                                if ($curl_response2 === false) {
                                    $info = curl_getinfo($curl);
                                    curl_close($curl);
                                    die('error occured during curl exec. Additioanl info: ' . var_export($info));
                                } else {
                                    $uuid_factura = $curl_response2;
                                    $pago->status_timbrado = 1;
                                    $pago->uuid = $uuid_factura;
                                    $pago->message = $response->message;
                                    if ($pago->update()) {
                                        $this->content['result'] = 'success';
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($pago);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                        $tx->rollback();
                                    }
                                }
                            } else if ($response->status == 'incoming' || $response->status == 'in progress' || $response->status == 'new') {
                                $pago->message = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            } else  if ($response->status == 'Error' || $response->status == 'error')  {
                                $pago->status_timbrado = 6;
                                $pago->message = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }
                        }
                        if ($this->content['result'] == 'success') {
                            $tx->commit();
                        }
                    } else if ($pago && $pago->status_timbrado == 3) {
                        $pago->setTransaction($tx);
                        $service_url = 'http://'.$this->batuta_url.'/api/info_general';
                        $curl = curl_init($service_url);
                        $curl_post_data = array(
                            'uuid' => $pago->id_cancelacion,
                        );
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                        $curl_response = curl_exec($curl);
                        if ($curl_response === false) {
                            $info = curl_getinfo($curl);
                            curl_close($curl);
                            die('error occured during curl exec. Additioanl info: ' . var_export($info));
                        } else {
                            $response = json_decode($curl_response);
                            if ($response->status == 'done') {
                                $pago->status_timbrado = 5;
                                $pago->id_cancelacion_asc = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            } else if ($response->status == 'incoming' || $response->status == 'in progress' || $response->status == 'new') {
                                $pago->message_cancelacion = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            } else  if ($response->status == 'Error' || $response->status == 'error')  {
                                $pago->status_timbrado = 1;
                                $pago->message_cancelacion = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }
                        }
                        if ($this->content['result'] == 'success') {
                            $tx->commit();
                        }
                    } else if ($pago && $pago->status_timbrado == 5) {
                        $service_url = 'http://'.$this->batuta_url.'/api/get_status_cancelacion';
                        $curl = curl_init($service_url);
                        $curl_post_data = array(
                            'uuid' => $pago->id_cancelacion_asc,
                        );
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
                        $curl_response = curl_exec($curl);
                        if ($curl_response === false) {
                            $info = curl_getinfo($curl);
                            curl_close($curl);
                            die('error occured during curl exec. Additioanl info: ' . var_export($info));
                        } else {
                            $response = json_decode($curl_response);
                            if ($response->ret_status == 200) {
                                if ($response->status) {
                                    $pago->status_timbrado = 2;
                                    $pago->message_cancelacion = $response->message;
                                    $pago->acuseSat_cancelacion = $response->acuseSat;
                                    if ($pago->update()) {
                                        $this->content['result'] = 'success';
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($pago);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                        $tx->rollback();
                                    }
                                } else {
                                    $status = explode('|', str_replace(' ', '', $response->message))[0];
                                    if ($status == 211) {
                                        $pago->message_cancelacion = $response->message;
                                        if ($pago->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($pago);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                            $tx->rollback();
                                        }
                                    } else {
                                        $pago->status_timbrado = 7;
                                        $pago->message_cancelacion = $response->message;
                                        $pago->acuseSat_cancelacion = $response->acuseSat;
                                        if ($pago->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($pago);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                            $tx->rollback();
                                        }
                                    }
                                }
                            } else if ($response->ret_status == 211) {
                                $pago->message_cancelacion = $response->message;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }else {
                                $pago->status_timbrado = 7;
                                $pago->message_cancelacion = $response->message;
                                $pago->acuseSat_cancelacion = $response->acuseSat;
                                if ($pago->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($pago);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }
                        }
                        if ($this->content['result'] == 'success') {
                            $tx->commit();
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->content['response'] = $response;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function savePago ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $pagos = RemisionPagos::find("remision_id = {$request['remision_id']} order by num_parcialidad desc");
            $rDetalles = RemisionDetalles::find('remision_id = ' . $request['remision_id']);
            $subtotal = 0;
            $totalImpuestosTrasladados = 0;
            $totalFactura = 0;
            $totalPagos = 0;
            foreach ($rDetalles as $key => $detalle) {
                $subtotal += number_format($detalle->precio_unitario * $detalle->cantidad,2,'.',''); 
                if ($detalle->impuesto) {
                    $totalImpuestosTrasladados += $detalle->impuesto_importe;
                }
            }
            foreach ($pagos as $key => $p) {
                $totalPagos += $p->total;
            }
            $totalFactura = $subtotal + $totalImpuestosTrasladados;
            $totalActual = $totalFactura - $totalPagos;
            if ($totalActual >= $request['total']) {
                $pago = new RemisionPagos();
                $pago->setTransaction($tx);
                $pago->remision_id = intval($request['remision_id']);
                $pago->fecha_pago = $request['fecha_pago'];
                $pago->forma_pago = $request['forma_pago'];
                $pago->num_parcialidad = !empty($pagos[0]->num_parcialidad) ? $pagos[0]->num_parcialidad + 1 : 1;
                $pago->total = floatval($request['total']);
                if ($pago->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el pago.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($pago);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el pago.'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'El valor del pago es mayor al saldo restante de la factura'];
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function deletePayment ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $pago = RemisionPagos::findFirst($request['id']);
                if ($pago) {
                    $pago->setTransaction($tx);
                    if ($pago->delete()) {    
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el pago.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($pago);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No ha borrado el pago.'];
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

    public function timbrarPayment ()
    {   
        $tx = $this->transactions->get();
        $request = $this->request->getPut();
        $rp = RemisionPagos::findFirst($request['id']);
        $rp->setTransaction($tx);
        $allRemisiones = RemisionPagos::find('remision_id = ' . $rp->remision_id . ' and status_timbrado = 1');
        $saldoPagado = 0;
        if ($allRemisiones) {
            foreach ($allRemisiones as $key => $r) {
                $saldoPagado += $r->total;
            }
        }
        $remision = Remisiones::findFirst($rp->remision_id);
        $empresa = Empresa::findFirst($remision->empresa_id);
        $cliente = VntClientes::findFirst($remision->cliente_id);
        $rDetalles = RemisionDetalles::find('remision_id = ' . $remision->id);
        $subtotal = 0;
        $totalFactura = 0;
        $totalImpuestosTrasladados = 0;
        $cabezera = $cuerpo = '';
        $fecha = str_replace(' ', 'T', $remision->fecha_factura);
        $fecha_pago = str_replace(' ', 'T', $rp->fecha_pago);
        foreach ($rDetalles as $key => $detalle) {
            $subtotal += number_format($detalle->precio_unitario * $detalle->cantidad,2,'.',''); 
            if ($detalle->impuesto) {
                $totalImpuestosTrasladados += $detalle->impuesto_importe;
            }
        }
        $totalFactura = $subtotal + $totalImpuestosTrasladados;
        $saldoAnterior = abs($totalFactura - $saldoPagado);
        $saldoInsoluto = abs($saldoAnterior - $rp->total);
        $complemento = '<complemento>';
        $complemento = $complemento . '
            <pago10>
                <pago>
                    <fechaPago>' . $fecha_pago. '</fechaPago>
                    <monedaP>MXN</monedaP>' .
                    '<formaDePagoP>' . $rp->SatPaymentForms->clave . '</formaDePagoP>';

        $complemento = $complemento . '<monto>' . number_format($rp->total, 2, '.', '') . '</monto></pago>';
        $complemento = $complemento . '
            <relaciones>
                <relacion>
                    <uuid>' . $remision->uuid . '</uuid>' .
                    '<folio>' . $remision->folio_fiscal . '</folio>
                    <metodoPago>PPD</metodoPago>
                    <numParcialidad>' . $rp->num_parcialidad . '</numParcialidad>    
                    <moneda>MXN</moneda>
                    <saldoAnterior>' . number_format($saldoAnterior, 2, '.', '') . '</saldoAnterior>
                    <impPagado>'.number_format($rp->total, 2, '.', '') .'</impPagado>
                    <saldoinsoluto>' . number_format($saldoInsoluto, 2, '.', '') . '</saldoinsoluto>
                </relacion>
            </relaciones>
        </pago10></complemento></factura></emision>';
        $cabezera = '<emision>
            <cliente>'.$empresa->batuta_id.'</cliente>
            <factura>
                <data>
                    <serie>'.$remision->serie.'</serie>
                    <folio>'.$remision->folio_fiscal.'</folio>
                    <fecha>'.$fecha.'</fecha>
                    <subtotal>0.00</subtotal>
                    <total>0.00</total>
                    <tipoDeComprobante>P</tipoDeComprobante>
                    <lugarDeExpedicion>'.$remision->lugar_expedicion.'</lugarDeExpedicion>
                    <moneda>XXX</moneda>
                </data>
                <receptor>
                        <rfc>'.$cliente->rfc.'</rfc>
                        <nombre>'.$cliente->razon_social.'</nombre>
                        <usoCFDI>P01</usoCFDI>
                </receptor>';
        $cuerpo =  '<conceptos>
                        <concepto>
                            <cantidad>1</cantidad>
                            <claveUnidad>ACT</claveUnidad>
                            <claveProdServ>84111506</claveProdServ>
                            <descripcion>Pago</descripcion>
                            <valorUnitario>0</valorUnitario>
                            <importe>0</importe>
                        </concepto>
                    </conceptos>';
        $emision = $cabezera . $cuerpo . $complemento;
        $serviceURL = 'http://'.$this->batuta_url.'/api/get_emision';
        $curl = curl_init($serviceURL);
        $curlPostData = [
            'data' => $emision,
            'filename' => $remision->folio_fiscal . $remision->serie . '_' . $remision->empresa_id . '-' . $remision->cliente_id . 'P' . $rp->num_parcialidad .'.xml',
            'flavour' => 'Facturacion',
            'scheduled' => ''
        ];
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPostData);
        $curlResponse = curl_exec($curl);
        
        $data = json_decode($curlResponse);
        curl_close($curl);
        if ($data->result) {
            $rp->status_timbrado = 4;
            $rp->id_request = $data->uuid;
            $rp->message = $data->message;
            $rp->update();
            $this->content['result'] = 'success';
        } else {
            $rp->message = $data->message;
            $rp->update();
        }
        $tx->commit();
        $this->content['data'] = $data;
        $this->content['emision'] = $emision;
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function cancelar ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            if ($request['id']) {
                $remision = Remisiones::findFirst($request['id']);
                if ($remision) {
                    $remision->setTransaction($tx);
                    if ($remision->cancelado == true) {
                        $remision->cancelado = false;
                    } else {
                        $remision->cancelado = true;
                    }
                    if ($remision->update()) {    
                        $this->content['result'] = 'success';
                        $remisionesFacturas = RemisionesFacturas::find('remision_id ='.$request['id']);
                        if ($remisionesFacturas) {
                            foreach ($remisionesFacturas as $rem) {
                                $rem->setTransaction($tx);
                                if ($rem->cancelado == true) {
                                    $rem->cancelado = false;
                                } else {
                                    $rem->cancelado = true;
                                }
                                if ($rem->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($rem);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                    $tx->rollback();
                                }
                            }
                        }
                        if ($this->content['result'] = 'success') {
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha cancelado la remision y su factura.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($remision);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No ha borrado el remision.'];
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
}