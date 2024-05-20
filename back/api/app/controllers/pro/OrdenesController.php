<?php

use Phalcon\Mvc\Controller;

class OrdenesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT pro_ordenes.id, pro_ordenes.folio, to_char(pro_ordenes.fecha_produccion, 'DD/MM/YYYY') as fecha_produccion, pro_ordenes.cliente_id, pro_ordenes.status, pro_ordenes.almacen_id_origen, pro_ordenes.almacen_id_destino, pro_ordenes.empresa_id_origen, pro_ordenes.empresa_id_destino, (select razon_social from vnt_clientes where vnt_clientes.id = pro_ordenes.cliente_id), vnt_empresa.razon_social as empresa, pro_ordenes.tipo
                FROM pro_ordenes
                inner join vnt_empresa on vnt_empresa.id = pro_ordenes.empresa_id_destino";
        $nuevo = [];
        $ordenes = $this->db->query($sql)->fetchAll();
        foreach ($ordenes as $orden) {
            $obj_orden = (object)array();
            $obj_orden->id = $orden['id'];
            $obj_orden->folio = $orden['folio'];
            $obj_orden->fecha_produccion = $orden['fecha_produccion'];
            $obj_orden->cliente_id = $orden['cliente_id'];
            if ($orden['status'] == 'NUEVO') {
                $obj_orden->icon = 'add';
                $obj_orden->color = 'amber';
            }
            if ($orden['status'] == 'PRODUCIENDO') {
                $obj_orden->icon = 'done';
                $obj_orden->color = 'purple';
            }
            if ($orden['status'] == 'FINALIZADO') {
                $obj_orden->icon = 'done_all';
                $obj_orden->color = 'green';
            }
            if ($orden['status'] == 'CANCELADO') {
                $obj_orden->icon = 'clear';
                $obj_orden->color = 'red';
            }
            $obj_orden->status = $orden['status'];
            $obj_orden->almacen_id_origen = $orden['almacen_id_origen'];
            $obj_orden->almacen_id_destino = $orden['almacen_id_destino'];
            $obj_orden->empresa_id_origen = $orden['empresa_id_origen'];
            $obj_orden->empresa_id_destino = $orden['empresa_id_destino'];
            $obj_orden->razon_social = $orden['razon_social'];
            $obj_orden->empresa = $orden['empresa'];
            $obj_orden->tipo = $orden['tipo'];
            array_push($nuevo, $obj_orden);
        }
        $this->content['ordenes'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFolioConsecutivo ($tipo) {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM pro_ordenes where folio like '$anio%' and tipo = '$tipo'";
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
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM pro_ordenes where folio like '$anio%' and tipo = '$tipo'";
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

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $ordenes = new Ordenes();
            $ordenes->setTransaction($tx);
            $ordenes->folio = $this->getFolio($request['tipo']);
            if (intval($request['cliente_id']) == 0) {
                $ordenes->cliente_id = null;
            } else {
                $ordenes->cliente_id = intval($request['cliente_id']);
            }
            $ordenes->almacen_id_origen = intval($request['almacen_id_origen']);
            $ordenes->almacen_id_destino = intval($request['almacen_id_destino']);
            $ordenes->empresa_id_origen = intval($request['empresa_id_origen']);
            $ordenes->empresa_id_destino = intval($request['empresa_id_origen']);
            // $ordenes->empresa_id_destino = intval($request['empresa_id_destino']);
            $ordenes->status = 'NUEVO';
            $ordenes->fecha_produccion = date("Y/m/d", strtotime($request['fecha_produccion']));
            $ordenes->tipo = $request['tipo'];
            // $ordenes->almacen_id = intval($request['almacen_id']);
            if ($ordenes->create()) {
                $this->content['id'] = $ordenes->id;
                $this->content['folio'] = $ordenes->folio;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado la orden de producción.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($ordenes);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar la orden de producción.'];
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
            $ordenes = Ordenes::findFirst($request['id']);
            if ($ordenes) {
                $ordenes->setTransaction($tx);
                if (intval($request['cliente_id']) == 0) {
                    $ordenes->cliente_id = null;
                } else {
                    $ordenes->cliente_id = intval($request['cliente_id']);
                }
                $ordenes->almacen_id_origen = intval($request['almacen_id_origen']);
                $ordenes->almacen_id_destino = intval($request['almacen_id_destino']);
                $ordenes->empresa_id_origen = intval($request['empresa_id_origen']);
                $ordenes->empresa_id_destino = intval($request['empresa_id_origen']);
                // $ordenes->empresa_id_destino = intval($request['empresa_id_destino']);
                $ordenes->status = $request['status'];
                $ordenes->fecha_produccion = date("Y/m/d", strtotime($request['fecha_produccion']));
                $ordenes->tipo = $request['tipo'];
                // $ordenes->almacen_id = intval($request['almacen_id']);
                if ($ordenes->update()) {
                    if ($request['status'] == 'PRODUCIENDO' && $request['tipo'] == 'PRODUCCIÓN') {
                        // hay que hacer un traspaso
                        $movimientos = new Movimientos();
                        $movimientos->setTransaction($tx);
                        $validUser = Auth::getUserData($this->config);
                        $movimientos->created_by = $validUser->user_id;
                        $movimientos->tipo_id = 5;
                        $movimientos->folio = $this->getFolioMovimiento(5);
                        $movimientos->almacen_id = intval($request['almacen_id_origen']);
                        $movimientos->empresa_id = intval($request['empresa_id_origen']);
                        $movimientos->status = 'EJECUTADO';
                        $movimientos->created = date("Y/m/d");
                        $movimientos->orden_id = $ordenes->id;
                        $movimientos->fecha_ejecutado = date("Y-m-d H:i:s");
                        if ($movimientos->create()) {
                            $movimientos2 = new Movimientos();
                            $movimientos2->setTransaction($tx);
                            $validUser = Auth::getUserData($this->config);
                            $movimientos2->created_by = $validUser->user_id;
                            $movimientos2->tipo_id = 4;
                            $movimientos2->folio = $this->getFolioMovimiento(4);
                            $movimientos2->almacen_id = intval($request['almacen_id_destino']);
                            $movimientos2->empresa_id = intval($request['empresa_id_origen']);
                            $movimientos2->status = 'EJECUTADO';
                            $movimientos2->created = date("Y/m/d");
                            $movimientos2->movimiento_id = $movimientos->id;
                            $movimientos2->orden_id = $ordenes->id;
                            $movimientos2->fecha_ejecutado = date("Y-m-d H:i:s");
                            if ($movimientos2->create()) {
                                $this->content['result'] = 'success';
                                $lista_materiales = $this->getAllDataMaterial($ordenes->id);
                                foreach ($lista_materiales as $material) {
                                    $detalle = new MovimientosDetalles();
                                    $detalle->setTransaction($tx);
                                    $detalle->created_by = $validUser->user_id;
                                    $detalle->movimiento_id = $movimientos->id;
                                    $detalle->presentacion_id = $material['presentacion_id'];
                                    $detalle->producto_id = $material['material_id'];
                                    $detalle->cantidad = $material['total_necesario'];
                                    $detalle->costo = 0;
                                    if ($detalle->create()) {
                                        $this->content['result'] = 'success';
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($detalle);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo ejecutar la orden.'];
                                        $tx->rollback();
                                    }
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($movimientos2);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo ejecutar la orden.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['error'] = Helpers::getErrors($movimientos);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo ejecutar la orden.'];
                            $tx->rollback();
                        }
                    }
                    if ($request['status'] == 'CANCELADO' && $request['tipo'] == 'PRODUCCIÓN') {
                        $this->content['result'] = 'success';
                        $movimientos_orden = Movimientos::find(
                            [
                                'orden_id = :orden_id:',
                                'bind' => [
                                    'orden_id' => $ordenes->id
                                ]
                            ]
                        );
                        foreach ($movimientos_orden as $mov) {
                            $mov_traspaso = Movimientos::findFirst($mov->id);
                            $mov_traspaso->setTransaction($tx);
                            $mov_traspaso->status = 'CANCELADO';
                            if ($mov_traspaso->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($mov_traspaso);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                                $tx->rollback();
                            }
                        }
                    }
                    if ($request['status'] == 'FINALIZADO') {
                        $movimientos = new Movimientos();
                        $movimientos->setTransaction($tx);
                        $validUser = Auth::getUserData($this->config);
                        $movimientos->created_by = $validUser->user_id;
                        $movimientos->tipo_id = 2;
                        $movimientos->folio = $this->getFolioMovimiento(2);
                        $movimientos->almacen_id = intval($request['almacen_id_destino']);
                        $movimientos->empresa_id = intval($request['empresa_id_origen']);
                        $movimientos->status = 'EJECUTADO';
                        $movimientos->created = date("Y/m/d");
                        $movimientos->orden_id = $ordenes->id;
                        $movimientos->fecha_ejecutado = date("Y-m-d H:i:s");
                        if ($movimientos->create()) {
                            $this->content['result'] = 'success';
                            $lista_materiales = $this->getAllDataMaterial($ordenes->id);
                            foreach ($lista_materiales as $material) {
                                $detalle = new MovimientosDetalles();
                                $detalle->setTransaction($tx);
                                $detalle->created_by = $validUser->user_id;
                                $detalle->movimiento_id = $movimientos->id;
                                $detalle->presentacion_id = $material['presentacion_id'];
                                $detalle->producto_id = $material['material_id'];
                                $detalle->cantidad = $material['total_necesario'];
                                $detalle->costo = 0;
                                if ($detalle->create()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($detalle);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                                    $tx->rollback();
                                }
                            }
                            $producto_terminado = $this->getAlmacenProductoTerminado($movimientos->empresa_id);
                            if ($producto_terminado === 0) {
                                $this->content['error'] = Helpers::getErrors($detalle);
                                $this->content['result'] = 'error';
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden, no existe almacén de producto terminado'];
                                $tx->rollback();
                            } else {
                                if ($this->content['result'] === 'success') {
                                    $movimientos_entrada = new Movimientos();
                                    $movimientos_entrada->setTransaction($tx);
                                    $validUser = Auth::getUserData($this->config);
                                    $movimientos_entrada->created_by = $validUser->user_id;
                                    $movimientos_entrada->tipo_id = 1;
                                    $movimientos_entrada->folio = $this->getFolioMovimiento(1);
                                    $movimientos_entrada->almacen_id = $producto_terminado;
                                    $movimientos_entrada->empresa_id = intval($request['empresa_id_origen']);
                                    $movimientos_entrada->status = 'EJECUTADO';
                                    $movimientos_entrada->created = date("Y/m/d");
                                    $movimientos_entrada->orden_id = $ordenes->id;
                                    $movimientos_entrada->fecha_ejecutado = date("Y-m-d H:i:s");
                                    if ($movimientos_entrada->create()) {
                                        $this->content['result'] = 'success';
                                        $lista_productos_terminados = $this->getProductosTerminados($ordenes->id);
                                        foreach ($lista_productos_terminados as $producto_term) {
                                            $detalle_terminado = new MovimientosDetalles();
                                            $detalle_terminado->setTransaction($tx);
                                            $detalle_terminado->created_by = $validUser->user_id;
                                            $detalle_terminado->movimiento_id = $movimientos_entrada->id;
                                            $detalle_terminado->presentacion_id = $producto_term['presentacion_id'];
                                            $detalle_terminado->producto_id = $producto_term['producto_id'];
                                            $detalle_terminado->cantidad = $producto_term['cantidad'];
                                            $detalle_terminado->costo = 0;
                                            if ($detalle_terminado->create()) {
                                                $this->content['result'] = 'success';
                                            } else {
                                                $this->content['error'] = Helpers::getErrors($detalle_terminado);
                                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                                                $tx->rollback();
                                            }
                                        }
                                        //
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($movimientos_entrada);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                                        $tx->rollback();
                                    }
                                }
                            }
                        } else {
                            $this->content['error'] = Helpers::getErrors($movimientos);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                            $tx->rollback();
                        }
                    }
                    if ($request['status'] == 'NUEVO') {
                        $this->content['result'] = 'success';
                    }
                    if ($request['status'] == 'PRODUCIENDO' && $request['tipo'] == 'EMPAQUETADO') {
                        $this->content['result'] = 'success';
                    }
                    if ($this->content['result'] === 'success') {
                        if ($request['tipo'] == 'PRODUCCIÓN') {
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la orden de producción.'];
                        }
                        if ($request['tipo'] == 'EMPAQUETADO') {
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la orden de empaquetado.'];
                        }
                        $tx->commit();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($ordenes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la orden'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getAllDataMaterial ($orden_id) {
        $sql_compacto = "SELECT inv_bom.material_id, sum(inv_bom.cantidad), x.nombre as nombre, 
            inv_tipos_articulos.nombre as categoria, inv_lineas.nombre as linea, inv_lineas.id as linea_id,
            inv_tipos_articulos.id as categoria_id, inv_tipos_presentaciones.id as presentacion_id, 
            inv_tipos_presentaciones.nombre as unidad, sum(pro_ordenes_detalles.cantidad), sum(inv_bom.cantidad * pro_ordenes_detalles.cantidad) as total_necesario
            FROM inv_bom
            inner join inv_productos as x on x.id = inv_bom.material_id
            inner join inv_tipos_articulos on inv_tipos_articulos.id = x.tipo_id
            inner join inv_lineas on inv_lineas.id = x.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = x.presentacion_id
            inner join inv_productos on inv_productos.id = inv_bom.producto_id
            inner join pro_ordenes_detalles on pro_ordenes_detalles.producto_id = inv_productos.id
            inner join pro_ordenes on pro_ordenes.id = pro_ordenes_detalles.orden_id
            and pro_ordenes.id = $orden_id
            group by inv_bom.material_id,x.nombre,inv_tipos_articulos.nombre,inv_lineas.nombre,
            inv_lineas.id,inv_tipos_articulos.id,inv_tipos_presentaciones.id";
        $data_compacto = $this->db->query($sql_compacto)->fetchAll();
        return $data_compacto;
    }

    private function getAlmacenProductoTerminado ($empresa_id) {
        $sql = "SELECT inv_almacenes.id, inv_almacenes.created, inv_almacenes.created_by, inv_almacenes.modified, inv_almacenes.modified_by, inv_almacenes.empresa_id, inv_almacenes.nombre, inv_almacenes.clave, inv_almacenes.calle, inv_almacenes.codigo_postal, inv_almacenes.municipio_id, inv_almacenes.colonia, inv_almacenes.contacto, inv_almacenes.telefono, inv_almacenes.estado_id, inv_almacenes.tipo, vnt_empresa.razon_social
            FROM inv_almacenes inner join vnt_empresa on vnt_empresa.id = inv_almacenes.empresa_id
            where inv_almacenes.empresa_id = $empresa_id
            ORDER BY nombre";
        $almacenes = $this->db->query($sql)->fetchAll();
        $producto_terminado = 0;
        foreach ($almacenes as $almacen) {
            if ($almacen['tipo'] === 'PRODUCTO TERMINADO') {
                $producto_terminado = $almacen['id'];
            }
        }
        return $producto_terminado;
    }

    private function getProductosTerminados ($orden_id) {
        $sql = "SELECT pro_ordenes_detalles.id, pro_ordenes_detalles.orden_id, pro_ordenes_detalles.producto_id, 
                pro_ordenes_detalles.cantidad, inv_tipos_presentaciones.id as presentacion_id
                FROM public.pro_ordenes_detalles
                inner join inv_productos on inv_productos.id = pro_ordenes_detalles.producto_id
                inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id and  orden_id = $orden_id";
        $data = $this->db->query($sql)->fetchAll();
        return $data;
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $ordenes = Ordenes::findFirst($request['id']);
                    $ordenes->setTransaction($tx);
                    if ($ordenes->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($ordenes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el registro.'];
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