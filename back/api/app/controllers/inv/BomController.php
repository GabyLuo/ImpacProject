<?php

use Phalcon\Mvc\Controller;

class BomController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, producto_id, material_id, cantidad
                FROM inv_bom";
        $this->content['bom'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProducto ($producto_id) {
        $sql = "SELECT inv_bom.id, inv_bom.producto_id, inv_bom.material_id, inv_bom.cantidad, inv_productos.nombre as producto, inv_tipos_articulos.nombre as categoria, inv_lineas.nombre as linea, inv_lineas.id as linea_id,
             inv_tipos_articulos.id as categoria_id, inv_tipos_presentaciones.id as presentacion_id, inv_tipos_presentaciones.nombre as unidad
            FROM inv_bom
            inner join inv_productos on inv_productos.id = inv_bom.material_id
            inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
            inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id
            and inv_bom.producto_id = $producto_id";
        $this->content['bom'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $bom = new Bom();
            $bom->setTransaction($tx);
            $bom->producto_id = intval($request['producto_id']);
            $bom->material_id = intval($request['material_id']);
            $bom->cantidad = floatval($request['cantidad']);
            // $bom->almacen_id = intval($request['almacen_id']);
            if ($bom->create()) {
                $this->content['id'] = $bom->id;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el registro.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($bom);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el registro.'];
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
            $bom = Bom::findFirst($request['id']);
            if ($bom) {
                $bom->setTransaction($tx);
                $bom->producto_id = intval($request['producto_id']);
                $bom->material_id = intval($request['material_id']);
                $bom->cantidad = floatval($request['cantidad']);
                // $bom->almacen_id = intval($request['almacen_id']);
                if ($bom->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el registro.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($bom);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el registro'];
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
                    $bom = Bom::findFirst($request['id']);
                    $bom->setTransaction($tx);
                    if ($bom->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($bom);
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