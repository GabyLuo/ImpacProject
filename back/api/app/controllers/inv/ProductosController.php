<?php

use Phalcon\Mvc\Controller;

class ProductosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT inv_productos.id, inv_productos.tipo_id, inv_productos.nombre, inv_productos.codigo, inv_productos.created_by, inv_tipos_articulos.nombre as categoria, inv_productos.linea_id, inv_lineas.nombre as linea, (inv_tipos_articulos.codigo || ' - ' || inv_lineas.codigo || ' - ' || inv_productos.codigo) as codigo_compuesto, inv_productos.presentacion_id, inv_tipos_presentaciones.nombre as presentacion, inv_productos.precio_a, inv_productos.precio_b, inv_productos.precio_c, inv_productos.precio_d, inv_productos.precio_e, inv_productos.clave_producto_id, inv_productos.numero_parte
            FROM inv_productos
            inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
            inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id 
            ORDER BY nombre";
        $this->content['productos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCategoria ($categoria) {
        if (intval($categoria) == 0) {
            $cond = "";
        } else {
            $cond = " and inv_productos.tipo_id = $categoria";
        }
        
        $sql = "SELECT inv_productos.id, inv_productos.tipo_id, inv_productos.nombre, inv_productos.codigo, inv_productos.created_by, inv_tipos_articulos.nombre as categoria, inv_productos.linea_id, inv_lineas.nombre as linea, (inv_tipos_articulos.codigo || ' - ' || inv_lineas.codigo || ' - ' || inv_productos.codigo) as codigo_compuesto, inv_productos.presentacion_id, inv_tipos_presentaciones.nombre as presentacion, inv_productos.precio_a, inv_productos.precio_b, inv_productos.precio_c, inv_productos.precio_d, inv_productos.precio_e, inv_productos.clave_producto_id, inv_productos.numero_parte
            FROM inv_productos
            inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
            inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id".$cond." ORDER BY nombre";
        $this->content['productos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getPresentacionByProducto ($id_producto) {
        $sql = "SELECT inv_productos.id, inv_productos.tipo_id, inv_productos.nombre, inv_productos.codigo, inv_productos.created_by, inv_tipos_articulos.nombre as categoria, inv_productos.linea_id, inv_lineas.nombre as linea, (inv_tipos_articulos.codigo || ' - ' || inv_lineas.codigo || ' - ' || inv_productos.codigo) as codigo_compuesto, inv_productos.presentacion_id, inv_tipos_presentaciones.nombre as presentacion, inv_productos.precio_a, inv_productos.precio_b, inv_productos.precio_c, inv_productos.precio_d, inv_productos.precio_e, inv_productos.clave_producto_id, inv_productos.numero_parte
            FROM inv_productos
            inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
            inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id 
            where inv_productos.id = $id_producto";
        $this->content['productos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getProductoByLinea ($linea) {
        $sql = "SELECT inv_productos.id, inv_productos.nombre, inv_productos.linea_id
            FROM inv_productos
            WHERE inv_productos.linea_id = $linea";
        $this->content['productosOpt'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCompra ($compra_id) {
        $sql = "SELECT inv_productos.id, inv_productos.nombre
            FROM inv_productos
            inner join cmp_compras_detalles on cmp_compras_detalles.producto_id = inv_productos.id
            and cmp_compras_detalles.compra_id = $compra_id";
        $this->content['productos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $productos = Productos::findFirst(
                [
                    'nombre = :nombre: AND tipo_id = :tipo_id: AND codigo = :codigo: AND linea_id = :linea_id: AND presentacion_id = :presentacion_id:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'tipo_id' => intval($request['tipo_id']),
                        'codigo' => strtoupper($request['codigo']),
                        'linea_id' => intval($request['linea_id']),
                        'presentacion_id' => intval($request['presentacion_id'])
                    ]
                ]
            );
            if ($productos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un producto con este nombre'];
            } else {
                $productos = new Productos();
                $productos->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $productos->created_by = $validUser->user_id;
                $productos->nombre = trim(strtoupper($request['nombre']));
                $productos->tipo_id = intval($request['tipo_id']);
                $productos->codigo = trim(strtoupper($request['codigo']));
                $productos->linea_id = intval($request['linea_id']);
                $productos->presentacion_id = intval($request['presentacion_id']);
                $productos->clave_producto_id = intval($request['clave_producto_id']);
                $productos->precio_a = floatval($request['precio_a_validado']);
                $productos->precio_b = floatval($request['precio_b_validado']);
                $productos->precio_c = floatval($request['precio_c_validado']);
                $productos->precio_d = floatval($request['precio_d_validado']);
                $productos->precio_e = floatval($request['precio_e_validado']);
                $productos->numero_parte = trim($request['numero_parte']);
                if ($productos->create()) {
                    $this->content['id'] = $productos->id;
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el producto.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($productos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el producto.'];
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
            $productos = Productos::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND tipo_id = :tipo_id: AND codigo = :codigo: AND linea_id = :linea_id: AND presentacion_id = :presentacion_id:',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'id' => intval($request['id']),
                        'tipo_id' => intval($request['tipo_id']),
                        'codigo' => strtoupper($request['codigo']),
                        'linea_id' => intval($request['linea_id']),
                        'presentacion_id' => intval($request['presentacion_id'])
                    ]
                ]
            );
            if ($productos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un producto con este nombre'];
            } else {
                $productos = Productos::findFirst($request['id']);
                if ($productos) {
                    $productos->setTransaction($tx);
                    $productos->nombre = trim(strtoupper($request['nombre']));
                    $productos->tipo_id = intval($request['tipo_id']);
                    $productos->codigo = trim(strtoupper($request['codigo']));
                    $productos->linea_id = intval($request['linea_id']);
                    $productos->presentacion_id = intval($request['presentacion_id']);
                    $productos->clave_producto_id = intval($request['clave_producto_id']);
                    $productos->precio_a = floatval($request['precio_a_validado']);
                    $productos->precio_b = floatval($request['precio_b_validado']);
                    $productos->precio_c = floatval($request['precio_c_validado']);
                    $productos->precio_d = floatval($request['precio_d_validado']);
                    $productos->precio_e = floatval($request['precio_e_validado']);
                    $productos->numero_parte = trim($request['numero_parte']);
                    if ($productos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el producto.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($productos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el producto'];
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

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $productos = Productos::findFirst($request['id']);
                    $productos->setTransaction($tx);

                    if ($productos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($productos);
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