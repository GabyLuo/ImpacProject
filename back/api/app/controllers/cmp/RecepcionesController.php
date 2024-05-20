<?php

use Phalcon\Mvc\Controller;

class RecepcionesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT cmp_recepciones.*, to_char(cmp_recepciones.fecha, 'DD-MM-YYYY') as fecha_recepcion, cmp_recepciones.folio
            FROM cmp_recepciones
            inner join cmp_compras on cmp_compras.id = cmp_recepciones.compra_id
            ORDER BY id";
        $this->content['recepciones'] = $this->db->query($sql)->fetchAll();
        foreach ($this->content['recepciones'] as $key => $recepcion) {
            $recepcion_id = $recepcion['id'];
            if ($recepcion['status'] == 'NUEVO') {
                $this->content['recepciones'][$key]['icon'] = 'add';
                $this->content['recepciones'][$key]['color'] = 'amber';
            }
            if ($recepcion['status'] == 'EJECUTADO') {
                $this->content['recepciones'][$key]['icon'] = 'fas fa-check-circle';
                $this->content['recepciones'][$key]['color'] = 'cyan';
            }
            if ($recepcion['status'] == 'CANCELADO') {
                $this->content['recepciones'][$key]['icon'] = 'clear';
                $this->content['recepciones'][$key]['color'] = 'orange-8';
            }
        }
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCompra ($compra_id)
    {
        $sql = "SELECT cmp_recepciones.*, to_char(cmp_recepciones.fecha, 'DD-MM-YYYY') as fecha_recepcion, 'R-' || cmp_recepciones.folio as folio, (select sys_users.nickname as name_user from sys_users where sys_users.id = cmp_recepciones.user_id)
            FROM cmp_recepciones
            inner join cmp_compras on cmp_compras.id = cmp_recepciones.compra_id and cmp_compras.id = $compra_id
            ORDER BY id";
        $this->content['recepciones'] = $this->db->query($sql)->fetchAll();
        foreach ($this->content['recepciones'] as $key => $recepcion) {
            $recepcion_id = $recepcion['id'];
            if ($recepcion['status'] == 'NUEVO') {
                $this->content['recepciones'][$key]['icon'] = 'add';
                $this->content['recepciones'][$key]['color'] = 'amber';
            }
            if ($recepcion['status'] == 'EJECUTADO') {
                $this->content['recepciones'][$key]['icon'] = 'fas fa-check-circle';
                $this->content['recepciones'][$key]['color'] = 'cyan';
            }
            if ($recepcion['status'] == 'CANCELADO') {
                $this->content['recepciones'][$key]['icon'] = 'clear';
                $this->content['recepciones'][$key]['color'] = 'orange-8';
            }
        }
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFolioConsecutivo () {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM cmp_recepciones where folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = 'R-'.(intval($folio) + 1);
        } else {
            $nuevo_folio = 'R-'.(date('Y') . '00001');
        }
        
        $this->content['folio'] = $nuevo_folio;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getFolio () {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM cmp_recepciones where folio like '$anio%'";
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
            
            $recepciones = Recepciones::findFirst("status = 'NUEVO'");
            if ($recepciones) {
                $compra = Compras::findFirst($recepciones->compra_id);
                $this->content['result'] = 'Error';
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Aún hay una recepción pendiente en la compra con folio '.$compra->folio];
            } else {
                $recepcion = new Recepciones();
                $recepcion->compra_id = intval($request['compra_id']);
                $recepcion->fecha = date("Y/m/d", strtotime($request['fecha'])) . ' ' . date("H:i:s");
                $recepcion->status = 'NUEVO';
                $recepcion->folio = $this->getFolio();
                $recepcion->user_id = intval($request['user_id']) > 0 ? intval($request['user_id']) : null;
                $recepcion->compra_id = intval($request['compra_id']) > 0 ? intval($request['compra_id']) : null;
                if ($recepcion->create()) {
                    $this->content['id'] = $recepcion->id;
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la recepción.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($recepcion);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la recepción.'];
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

    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $recepcion = Recepciones::findFirst($request['id']);
            $recepcion->setTransaction($tx);
            $recepcion->status = $request['status'];
            $recepcion->fecha = date("Y/m/d", strtotime($request['fecha'])) . ' ' . date("H:i:s");
            $recepcion->user_id = intval($request['user_id']) > 0 ? intval($request['user_id']) : null;
            if ($recepcion->update()) {
                if ($request['status'] == 'EJECUTADO') {
                    $movimientos = new Movimientos();
                    $movimientos->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $movimientos->created_by = $validUser->user_id;
                    $movimientos->tipo_id = 1;
                    $movimientos->folio = $this->getFolioMovimiento(1);
                    $almacen = Almacenes::findFirst("tipo = "."'CENTRAL'");
                    $movimientos->almacen_id = $almacen->id;
                    $movimientos->empresa_id = null;
                    $movimientos->status = 'EJECUTADO';
                    $movimientos->created = date("Y/m/d");
                    $movimientos->recepcion_id = $recepcion->id;
                    $movimientos->fecha_ejecutado = $recepcion->fecha;
                    if ($movimientos->create()) {
                        $lista_productos = RecepcionesDetalles::find('recepcion_id = '.$recepcion->id);
                        foreach ($lista_productos as $producto) {
                            $presentacion_id = Productos::findFirst($producto->producto_id)->presentacion_id;
                            $detalle = new MovimientosDetalles();
                            $detalle->setTransaction($tx);
                            $detalle->created_by = $validUser->user_id;
                            $detalle->movimiento_id = $movimientos->id;
                            $detalle->presentacion_id = $presentacion_id;
                            $detalle->producto_id = $producto->producto_id;
                            $detalle->cantidad = $producto->cantidad;
                            $detalle->costo = 0;
                            if ($detalle->create()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($detalle);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la recepción'];
                                $tx->rollback();
                            }
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la recepción'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['result'] = 'success';
                }
            } else {
                $this->content['error'] = Helpers::getErrors($recepcion);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la recepción'];
                $tx->rollback();
            }
            if ($this->content['result'] === 'success') {
                $this->content['status'] = $recepcion->status;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la recepción.'];
                $tx->commit();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $recepcion = Recepciones::findFirst($request['id']);
                    $recepcion->setTransaction($tx);

                    if ($recepcion->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($recepcion);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la recepción.'];
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