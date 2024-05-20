<?php

use Phalcon\Mvc\Controller;

class MovimientosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT inv_movimientos.id, inv_movimientos.observaciones, inv_movimientos.destino_id, inv_movimientos.created, inv_movimientos.created_by, inv_movimientos.modified, inv_movimientos.modified_by, inv_movimientos.tipo_id, inv_movimientos.folio, inv_movimientos.almacen_id, inv_movimientos.empresa_id, inv_movimientos.status, inv_tipos_movimientos.nombre as movimiento, inv_almacenes.nombre as almacen, vnt_empresa.razon_social as empresa, inv_movimientos.movimiento_id, inv_movimientos.orden_id, 'R-' || cmp_recepciones.folio as recepcion, cmp_recepciones.id as id_recepcion, pmo_proveedores.nombre_comercial as proveedor, proveedor_id
            FROM inv_movimientos 
            inner join inv_tipos_movimientos on inv_tipos_movimientos.id = inv_movimientos.tipo_id and inv_movimientos.tipo_id != 4
            inner join inv_almacenes on inv_almacenes.id = inv_movimientos.almacen_id
            left join vnt_empresa on vnt_empresa.id = inv_movimientos.empresa_id
            left join cmp_recepciones on cmp_recepciones.id = inv_movimientos.recepcion_id
            left join pmo_proveedores on pmo_proveedores.id = inv_movimientos.proveedor_id
            order by created desc, id asc";
        $movimientos = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($movimientos as $elemento){
            $id_movimiento = $elemento['id'];
            $sql_movimiento = "SELECT inv_movimientos.id, inv_movimientos.created, inv_movimientos.created_by, inv_movimientos.modified, inv_movimientos.modified_by, inv_movimientos.tipo_id, inv_movimientos.folio, inv_movimientos.almacen_id, inv_movimientos.empresa_id, inv_movimientos.status, inv_tipos_movimientos.nombre as movimiento, inv_almacenes.nombre as almacen, vnt_empresa.razon_social as empresa, crm_destinos.nombre_destino as nombre_destino
            FROM inv_movimientos 
            inner join inv_tipos_movimientos on inv_tipos_movimientos.id = inv_movimientos.tipo_id
            inner join inv_almacenes on inv_almacenes.id = inv_movimientos.almacen_id
            inner join crm_destinos  on inv_movimientos.destino_id = crm_destinos.id 
            left join vnt_empresa on vnt_empresa.id = inv_movimientos.empresa_id and inv_movimientos.movimiento_id = $id_movimiento";
            $movimientos2 = $this->db->query($sql_movimiento)->fetchAll();
            $movimiento=(object)array();
            $movimiento->id = $elemento['id'];
            $movimiento->tipo_id = $elemento['tipo_id'];
            $movimiento->folio = $elemento['folio'];
            $movimiento->almacen_id = $elemento['almacen_id'];
            $movimiento->empresa_id = $elemento['empresa_id'];
            $movimiento->status = $elemento['status'];
            if ($elemento['status'] == 'NUEVO') {
                $movimiento->icon = 'add';
                $movimiento->color = 'amber';
            }
            if ($elemento['status'] == 'EJECUTADO') {
                $movimiento->icon = 'done';
                $movimiento->color = 'green';
            }
            if ($elemento['status'] == 'CANCELADO') {
                $movimiento->icon = 'clear';
                $movimiento->color = 'red';
            }
            $movimiento->movimiento = $elemento['movimiento'];
            $movimiento->destino = $elemento['destino_id'];
            $movimiento->observaciones = $elemento['observaciones'];
            $movimiento->almacen = $elemento['almacen'];
            $movimiento->empresa = $elemento['empresa'];
            $movimiento->created = $elemento['created'];
            $movimiento->movimiento_id = $elemento['movimiento_id'];
            $movimiento->orden_id = $elemento['orden_id'];
            $movimiento->recepcion = $elemento['recepcion'];
            $movimiento->proveedor = $elemento['proveedor'];
            $movimiento->proveedor_id = $elemento['proveedor_id'];
            if (sizeof($movimientos2)) {
                $movimiento->empresa_id_2 = $movimientos2[0]['empresa_id'];
                $movimiento->id2 = $movimientos2[0]['id'];
                $movimiento->tipo_id_2 = $movimientos2[0]['tipo_id'];
                $movimiento->folio_2 = $movimientos2[0]['folio'];
                $movimiento->almacen_id_2 = $movimientos2[0]['almacen_id'];
                $movimiento->almacen_id_2 = $movimientos2[0]['nombre_destino'];
            }
            $recepcion = [];
            if ($elemento['id_recepcion'] !== null) {
                $recepcion = $this->getDataRecepcion($elemento['id_recepcion']);
            }
            if (count($recepcion) > 0) {
                $movimiento->existe_recepcion = 'SI';
                $movimiento->razon_social = $recepcion[0]['razon_social'];
                $movimiento->folio_recepcion = $recepcion[0]['folio_recepcion'];
                $movimiento->nickname = $recepcion[0]['nickname'];
                $movimiento->folio_compra = $recepcion[0]['folio_compra'];
                $movimiento->subtotal = number_format($recepcion[0]['subtotal'],2,'.',',');
                $movimiento->total = number_format($recepcion[0]['total'],2,'.',',');
            } else {
                $movimiento->existe_recepcion = 'NO';
                $movimiento->razon_social = null;
                $movimiento->folio_recepcion = null;
                $movimiento->nickname = null;
                $movimiento->folio_compra = null;
                $movimiento->subtotal = null;
                $movimiento->total = null;
            }
            array_push($nuevo,$movimiento);
        }

        $this->content['movimientos'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getDataRecepcion ($id) {
        $sql = "SELECT vnt_empresa.razon_social, cmp_recepciones.folio as folio_recepcion, sys_users.nickname,
            cmp_compras.folio as folio_compra, sum((cmp_compras_detalles.cantidad * cmp_compras_detalles.costo)) as subtotal, sum((cmp_compras_detalles.cantidad * cmp_compras_detalles.costo) * 1.16) as total
            FROM public.cmp_recepciones
            inner join sys_users on sys_users.id = cmp_recepciones.user_id and cmp_recepciones.id = $id
            inner join cmp_compras on cmp_compras.id = cmp_recepciones.compra_id
            inner join vnt_empresa on vnt_empresa.id = cmp_compras.empresa_id
            inner join cmp_compras_detalles on cmp_compras_detalles.compra_id = cmp_compras.id
            group by vnt_empresa.razon_social, folio_recepcion, sys_users.nickname, folio_compra";
        return $this->db->query($sql)->fetchAll();
    }

    public function getFolioConsecutivo ($tipo_movimiento) {
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
        
        $this->content['folio'] = $nuevo_folio;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getFolio ($tipo_movimiento) {
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
            //var_dump($request['destino']);
            $movimientos = new Movimientos();
            $movimientos->setTransaction($tx);
            $validUser = Auth::getUserData($this->config);
            $movimientos->created_by = $validUser->user_id;
            $movimientos->tipo_id = intval($request['tipo_id']);
            $tipo = intval($request['tipo_id']);
            $movimientos->folio = $this->getFolio($tipo);
            $movimientos->almacen_id = intval($request['almacen_id']);
            $movimientos->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
            $movimientos->status = 'NUEVO';
            $movimientos->status = 'NUEVO';
            $movimientos->destino_id = intval($request['destino']);
            $movimientos->observaciones = $request['observaciones'];
            $movimientos->proveedor_id = intval($request['proveedor_id']) > 0 ? intval($request['proveedor_id']) : null;
            if ($movimientos->create()) {
                if (intval($request['tipo_id']) === 5) {
                    $movimientos2 = new Movimientos();
                    $movimientos2->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $movimientos2->created_by = $validUser->user_id;
                    $movimientos2->tipo_id = intval($request['tipo_id_2']);
                    $tipo_2 = intval($request['tipo_id_2']);
                    $movimientos2->folio = $this->getFolio($tipo_2);
                    $movimientos2->almacen_id = intval($request['almacen_id_2']);
                    $movimientos2->empresa_id = intval($request['empresa_id_2']) > 0 ? intval($request['empresa_id_2']) : null;
                    $movimientos2->status = 'NUEVO';
                    $movimientos2->created = date("Y/m/d", strtotime($request['fecha']));
                    $movimientos2->movimiento_id = $movimientos->id;
                    $movimientos2->destino_id = intval($request['destino']);
                    $movimientos2->observaciones = $request['observaciones'];
                    if ($movimientos2->create()) {
                        $this->content['result'] = 'success';
                        $this->content['id'] = $movimientos->id;
                        $this->content['folio'] = $movimientos->folio;
                        $this->content['id2'] = $movimientos2->id;
                        $this->content['folio_2'] = $movimientos2->folio;
                        $this->content['almacen_id_2'] = $movimientos2->almacen_id;
                        $this->content['tipo_id_2'] = $movimientos2->tipo_id;
                        $this->content['destino'] = $movimientos2->destino_id;
                        $this->content['observaciones'] = $movimientos2->observaciones;
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el movimiento.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($movimientos2);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el movimiento.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['result'] = 'success';
                    $this->content['id'] = $movimientos->id;
                    $this->content['folio'] = $movimientos->folio;
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el movimiento.'];
                    $tx->commit();
                }
            } else {
                $this->content['error'] = Helpers::getErrors($movimientos);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el movimiento.'];
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

            $movimientos = Movimientos::findFirst($request['id']);
            $this->content['result'] = 'success';
            if ($movimientos) {
                if ($request['status'] == 'EJECUTADO' && ($request['tipo_id'] == 2 || $request['tipo_id'] == 5)) {
                    $detalles = MovimientosDetalles::find(
                        [
                            'movimiento_id = :movimiento_id:',
                            'bind' => [
                                'movimiento_id' => $movimientos->id
                            ]
                        ]
                    );
                    //
                    $almacen = intval($request['almacen_id']);
                    for ($i=0; $i<count($detalles); $i++) {
                        $cantidades = $this->getexistenciasproducto($almacen, $detalles[$i]->producto_id);
                        if (count($cantidades) > 0) {
                            if ($cantidades[0]['existencia'] > 0 && ($cantidades[0]['existencia'] >= floatval($detalles[$i]->cantidad))) {
                                $resultado = 'ok';
                            } else {
                                $resultado = 'insuficiente';
                                break;
                            }
                        } else {
                            $resultado = 'no existe';
                            break;
                        }
                    }
                    if ($resultado === 'ok') {
                        $this->content['result'] = 'success';
                    }
                    if ($resultado === 'insuficiente') {
                        $nombre_producto = Productos::findFirst($detalles[$i]->producto_id)->nombre;
                        $this->content['result'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'La cantidad de '.$nombre_producto.' a sacar es mayor a la existente, no se ejecutará el movimiento'];
                    }
                    if ($resultado === 'no existe') {
                        $nombre_producto = Productos::findFirst($detalles[$i]->producto_id)->nombre;
                        $this->content['result'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No hay existencias del producto '.$nombre_producto.', no se ejecutará el movimiento'];
                    }
                }
                if ($this->content['result'] === 'success') {
                    $movimientos->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $movimientos->modified_by = $validUser->user_id;
                    $movimientos->tipo_id = intval($request['tipo_id']);
                    $movimientos->folio = $request['folio'];
                    $movimientos->almacen_id = intval($request['almacen_id']);
                    $movimientos->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
                    $movimientos->proveedor_id = intval($request['proveedor_id']) > 0 ? intval($request['proveedor_id']) : null;
                    $movimientos->status = $request['status'];
                    $movimientos->created = date("Y/m/d", strtotime($request['fecha']));
                    if ($request['status'] == 'EJECUTADO') {
                        $movimientos->fecha_ejecutado = date("Y-m-d H:i:s");
                    }
                    if ($movimientos->update()) {
                        if ($request['tipo_id'] == 5) {
                            $movimientos2 = Movimientos::findFirst($request['id2']);
                            if ($movimientos2) {
                                $movimientos2->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $movimientos2->modified_by = $validUser->user_id;
                                $movimientos2->almacen_id = intval($request['almacen_id_2']);
                                $movimientos2->empresa_id = intval($request['empresa_id_2']) > 0 ? intval($request['empresa_id_2']) : null;
                                $movimientos2->status = $request['status'];
                                $movimientos2->modified = date("Y/m/d", strtotime($request['fecha']));
                                if ($movimientos2->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($movimientos2);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el movimiento'];
                                    $tx->rollback();
                                }
                            }
                        }
                        if ($this->content['result'] === 'success') {
                            // $this->getexistenciasproducto();
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el movimiento.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el movimiento'];
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

    private function getexistenciasproducto ($almacen, $producto) {
        $sql = "SELECT * from get_existencias($almacen, $producto, null, null)";
        $existencias = $this->db->query($sql)->fetchAll();
        return $existencias;
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $movimientos = Movimientos::findFirst($request['id']);
                    $movimientos->setTransaction($tx);

                    if ($movimientos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el movimiento.'];
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
