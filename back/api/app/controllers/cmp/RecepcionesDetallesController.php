<?php

use Phalcon\Mvc\Controller;

class RecepcionesDetallesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT cmp_recepciones_detalles.*, inv_productos.nombre as producto
            FROM cmp_recepciones_detalles
            inner join inv_productos on inv_productos.id = cmp_recepciones_detalles.producto_id
            ORDER BY id";
        $this->content['recepciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByRecepcion ($recepcion_id)
    {
        $sql = "SELECT cmp_recepciones_detalles.*, inv_productos.nombre as producto
            FROM cmp_recepciones_detalles
            inner join inv_productos on inv_productos.id = cmp_recepciones_detalles.producto_id
            inner join cmp_recepciones on cmp_recepciones.id = cmp_recepciones_detalles.recepcion_id and cmp_recepciones.id = $recepcion_id
            ORDER BY id";
        $this->content['recepciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function get_cantidad ($recepcion_id, $producto_id) {
        $recepcion = Recepciones::findFirst(intval($recepcion_id));
        $compra = Compras::findFirst($recepcion->compra_id);
        $compra_detalles = ComprasDetalles::find('compra_id = '.$compra->id.' AND producto_id = '.intval($producto_id));
        $total_producto = 0;
        foreach ($compra_detalles as $detalle) {
            $total_producto = $total_producto + $detalle->cantidad;
        }
        return $total_producto;
    }

    private function get_total_recibido ($recepcion_id, $producto_id) {
        $recepcion = Recepciones::findFirst(intval($recepcion_id));
        $compra = $recepcion->compra_id;

        $sql = "SELECT coalesce((select sum(cmp_recepciones_detalles.cantidad)
                from cmp_recepciones_detalles
                inner join cmp_recepciones on cmp_recepciones.id = cmp_recepciones_detalles.recepcion_id
                and cmp_recepciones.status != 'CANCELADO'
                inner join inv_productos on inv_productos.id = cmp_recepciones_detalles.producto_id 
                and inv_productos.id = $producto_id
                inner join cmp_compras on cmp_compras.id = cmp_recepciones.compra_id 
                and cmp_recepciones.compra_id = $compra), 0) as suma";

        $data = $this->db->query($sql)->fetchAll();
        return $data[0]['suma'];
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            
            $total_producto = $this->get_cantidad($request['recepcion_id'], $request['producto_id']);
            $recibido = $this->get_total_recibido($request['recepcion_id'], $request['producto_id']);
            $restante = $total_producto - $recibido;

            if (intval($request['cantidad']) <= $restante) {
                $recepcion_detalle = new RecepcionesDetalles();
                $recepcion_detalle->recepcion_id = intval($request['recepcion_id']);
                $recepcion_detalle->producto_id = intval($request['producto_id']);
                $recepcion_detalle->cantidad = intval($request['cantidad']);
                if ($recepcion_detalle->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el detalle.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($recepcion_detalle);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el detalle.'];
                    $tx->rollback();
                }
            } else {
                $this->content['result'] = 'Error';
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La cantidad de producto a recibir es mayor a la restante.'];
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
            $recepcion = RecepcionesDetalles::findFirst($request['id']);
            $recepcion->setTransaction($tx);
            $recepcion->recepcion_id = intval($request['recepcion_id']);
            $recepcion->producto_id = intval($request['producto_id']);
            $recepcion->cantidad = intval($request['cantidad']);
            if ($recepcion->update()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el detalle.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($recepcion);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el detalle'];
                $tx->rollback();
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
                    $recepcion = RecepcionesDetalles::findFirst($request['id']);
                    $recepcion->setTransaction($tx);

                    if ($recepcion->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($recepcion);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el detalle.'];
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