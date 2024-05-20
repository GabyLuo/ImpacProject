<?php

use Phalcon\Mvc\Controller;

class MovimientosDetallesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT inv_movimientos_detalles.id, inv_movimientos_detalles.created, inv_movimientos_detalles.created_by, inv_movimientos_detalles.modified, inv_movimientos_detalles.modified_by, inv_movimientos_detalles.movimiento_id, inv_movimientos_detalles.presentacion_id, inv_movimientos_detalles.cantidad, inv_movimientos_detalles.costo, inv_movimientos_detalles.producto_id, inv_productos.nombre as producto
            FROM inv_movimientos_detalles
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_movimientos_detalles.presentacion_id
            inner join inv_productos on inv_productos.id = inv_movimientos_detalles.producto_id";
        $this->content['movimientos_detalles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByMovimiento ($movimiento_id)
    {
        $sql = "SELECT inv_movimientos_detalles.id, inv_movimientos_detalles.created, inv_movimientos_detalles.created_by, inv_movimientos_detalles.modified, inv_movimientos_detalles.modified_by, inv_movimientos_detalles.movimiento_id, inv_movimientos_detalles.presentacion_id, inv_movimientos_detalles.cantidad, inv_movimientos_detalles.costo, inv_movimientos_detalles.producto_id, inv_productos.nombre as producto, (inv_movimientos_detalles.cantidad * inv_movimientos_detalles.costo) as importe
            FROM inv_movimientos_detalles
            inner join inv_productos on inv_productos.id = inv_movimientos_detalles.producto_id and inv_movimientos_detalles.movimiento_id = $movimiento_id";
        $detalles = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($detalles as $elemento){
            $detalle=(object)array();
            $detalle->id = $elemento['id'];
            $detalle->movimiento_id = $elemento['movimiento_id'];
            $detalle->presentacion_id = intval($elemento['presentacion_id']);
            $detalle->cantidad = floatval($elemento['cantidad']);
            $detalle->costo = number_format(floatval($elemento['costo']),2,'.',',');
            $detalle->producto_id = $elemento['producto_id'];
            $detalle->producto = $elemento['producto'];
            $detalle->importe = number_format(floatval($elemento['importe']),2,'.',',');
            $detalle->created = date("Y/m/d", strtotime($elemento['created']));
            array_push($nuevo,$detalle);
        }
        $this->content['movimientos_detalles'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $movimientos = new MovimientosDetalles();
            $movimientos->setTransaction($tx);
            $validUser = Auth::getUserData($this->config);
            $movimientos->created_by = $validUser->user_id;
            $movimientos->movimiento_id = intval($request['movimiento_id']);
            $movimientos->presentacion_id = intval($request['presentacion_id']);
            $movimientos->producto_id = intval($request['producto_id']);
            $movimientos->cantidad = floatval($request['cantidad']);
            $movimientos->costo = floatval($request['costo']);
            if ($movimientos->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han guardado los cambios.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($movimientos);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar cambios.'];
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
            $movimientos = MovimientosDetalles::findFirst($request['id']);
            if ($movimientos) {
                $movimientos->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $movimientos->modified_by = $validUser->user_id;
                $movimientos->movimiento_id = intval($request['movimiento_id']);
                $movimientos->producto_id = intval($request['producto_id']);
                $movimientos->presentacion_id = intval($request['presentacion_id']);
                $movimientos->cantidad = floatval($request['cantidad']);
                $movimientos->costo = floatval($request['costo']);
                if ($movimientos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han guardado los cambios.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($movimientos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar cambios'];
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
                    $movimientos = MovimientosDetalles::findFirst($request['id']);
                    $movimientos->setTransaction($tx);

                    if ($movimientos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el producto.'];
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