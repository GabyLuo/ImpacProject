<?php

use Phalcon\Mvc\Controller;

class OrdenesDetallesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, orden_id, producto_id, cantidad FROM pro_ordenes_detalles";
        $this->content['detalles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByOrden ($orden_id,$almacen_origen) {
        $almacen_origen = intval($almacen_origen);
        $sql = "SELECT pro_ordenes_detalles.id, pro_ordenes_detalles.orden_id, pro_ordenes_detalles.producto_id,pro_ordenes_detalles.cantidad, inv_productos.nombre, inv_tipos_articulos.id as categoria_id, inv_lineas.id as linea_id, inv_tipos_presentaciones.id as presentacion_id
                FROM pro_ordenes_detalles
                inner join inv_productos on inv_productos.id = pro_ordenes_detalles.producto_id
                inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
                inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
                inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id
                and pro_ordenes_detalles.orden_id = $orden_id";
        $detalles = $this->db->query($sql)->fetchAll();

        $detalles_orden = [];
        foreach ($detalles as $detalle) {
            $d=(object)array();
            $d->id = $detalle['id'];
            $d->orden_id = $detalle['orden_id'];
            $d->producto_id = $detalle['producto_id'];
            $producto_id = $detalle['producto_id'];
            $d->cantidad_a_producir = $detalle['cantidad'];
            $d->cantidad_por_pieza = '';
            $d->existencia = '';
            $d->total_necesario = '';
            $cantidad_a_producir = $detalle['cantidad'];
            $d->nombre = $detalle['nombre'];
            $d->categoria_id = $detalle['categoria_id'];
            $d->linea_id = $detalle['linea_id'];
            $d->presentacion_id = $detalle['presentacion_id'];
            $d->expend = false;
            $d->color = 'teal';
            $sql_producto = "SELECT inv_bom.id, inv_bom.producto_id, inv_bom.material_id, inv_bom.cantidad, inv_productos.nombre as producto, inv_tipos_articulos.nombre as categoria, inv_lineas.nombre as linea, inv_lineas.id as linea_id,
                inv_tipos_articulos.id as categoria_id, inv_tipos_presentaciones.id as presentacion_id, inv_tipos_presentaciones.nombre as unidad
                FROM inv_bom
                inner join inv_productos on inv_productos.id = inv_bom.material_id
                inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
                inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
                inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id
                and inv_bom.producto_id = $producto_id";
            $bom = $this->db->query($sql_producto)->fetchAll();
            $bom_by_producto = [];
            foreach ($bom as $bom_producto) {
                $b=(object)array();
                $b->id = $bom_producto['id'];
                $b->producto_id = $bom_producto['producto_id'];
                $producto_existencia = $bom_producto['material_id'];
                $b->material_id = $bom_producto['material_id'];
                $b->cantidad_a_producir = '';
                $b->cantidad_por_pieza = $bom_producto['cantidad'];
                $b->nombre = $bom_producto['producto'];
                $b->linea = $bom_producto['linea'];
                $b->unidad = $bom_producto['unidad'];
                $sql_existencias = "SELECT * from get_existencias($almacen_origen, $producto_existencia, null, null)";
                $existencia = $this->db->query($sql_existencias)->fetchAll();
                if (count($existencia) > 0) {
                    $b->existencia = $existencia[0]['existencia'];
                } else {
                    $b->existencia = 0;
                }
                $b->expend = false;
                $b->color = 'orange-5';
                // var_dump($existencia);
                $b->total_necesario = round($bom_producto['cantidad'] * $cantidad_a_producir,3);
                $d->bom = [];
                array_push($bom_by_producto, $b);
            }
            $d->bom = $bom_by_producto;
            array_push($detalles_orden, $d);
        }

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
        $array_compacto = [];
        $cantidades_suficientes = 'si';
        $producto = '';
        foreach ($data_compacto as $compacto) {
            $c=(object)array();
            $c->nombre = $compacto['nombre'];
            $c->total_necesario = round($compacto['total_necesario'],3);
            $producto_existencia = $compacto['material_id'];
            $sql_existencias = "SELECT * from get_existencias($almacen_origen, $producto_existencia, null, null)";
            $existencia = $this->db->query($sql_existencias)->fetchAll();
            if (count($existencia) > 0) {
                $c->existencia = $existencia[0]['existencia'];
            } else {
                $c->existencia = 0;
            }
            if ($compacto['total_necesario'] > $c->existencia) {
                $cantidades_suficientes = 'no';
                $producto = Productos::findFirst($producto_existencia)->nombre;
            }
            $c->expend = false;
            $c->color = 'orange-5';
            array_push($array_compacto, $c);
        }

        $this->content['detalles'] = $detalles;
        $this->content['detalles_orden'] = $detalles_orden;
        $this->content['detalles_compacto'] = $array_compacto;
        $this->content['cantidad_suficiente'] = $cantidades_suficientes;
        $this->content['producto'] = $producto;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $detalles = new OrdenesDetalles();
            $detalles->setTransaction($tx);
            $detalles->producto_id = intval($request['producto_id']);
            $detalles->orden_id = intval($request['orden_id']);
            $detalles->cantidad = intval($request['cantidad']);
            // $detalles->almacen_id = intval($request['almacen_id']);
            if ($detalles->create()) {
                $this->content['id'] = $detalles->id;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el registro.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($detalles);
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
            $detalles = OrdenesDetalles::findFirst($request['id']);
            if ($detalles) {
                $detalles->setTransaction($tx);
                $detalles->producto_id = intval($request['producto_id']);
                $detalles->cantidad = intval($request['cantidad']);
                // $detalles->almacen_id = intval($request['almacen_id']);
                if ($detalles->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el registro.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($detalles);
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
                    $detalles = OrdenesDetalles::findFirst($request['id']);
                    $detalles->setTransaction($tx);
                    if ($detalles->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($detalles);
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