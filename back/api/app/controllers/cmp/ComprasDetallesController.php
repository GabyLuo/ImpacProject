<?php

use Phalcon\Mvc\Controller;

class ComprasDetallesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT cmp_compras_detalles.id, cmp_compras_detalles.created, cmp_compras_detalles.created_by, cmp_compras_detalles.modified, cmp_compras_detalles.modified_by, cmp_compras_detalles.compra_id, cmp_compras_detalles.presentacion_id, cmp_compras_detalles.cantidad, cmp_compras_detalles.costo, cmp_compras_detalles.producto_id, cmp_compras_detalles.descripcion, inv_productos.nombre as producto
            FROM cmp_compras_detalles
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = cmp_compras_detalles.presentacion_id
            inner join inv_productos on inv_productos.id = cmp_compras_detalles.producto_id";
        $this->content['movimientos_detalles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function get_total_recibido ($compra_id, $producto_id) {

        $sql = "SELECT coalesce((select sum(cmp_recepciones_detalles.cantidad)
                from cmp_recepciones_detalles
                inner join cmp_recepciones on cmp_recepciones.id = cmp_recepciones_detalles.recepcion_id
                and cmp_recepciones.status = 'EJECUTADO'
                inner join inv_productos on inv_productos.id = cmp_recepciones_detalles.producto_id 
                and inv_productos.id = $producto_id
                inner join cmp_compras on cmp_compras.id = cmp_recepciones.compra_id 
                and cmp_recepciones.compra_id = $compra_id), 0) as suma";

        $data = $this->db->query($sql)->fetchAll();
        return $data[0]['suma'];
    }

    public function getByCompra ($compra_id)
    {
        $sql = "SELECT cmp_compras_detalles.id, cmp_compras_detalles.created, cmp_compras_detalles.created_by, cmp_compras_detalles.modified, cmp_compras_detalles.modified_by, cmp_compras_detalles.compra_id, cmp_compras_detalles.presentacion_id, cmp_compras_detalles.cantidad, cmp_compras_detalles.costo, cmp_compras_detalles.producto_id, cmp_compras_detalles.descripcion, inv_productos.nombre as producto, (cmp_compras_detalles.cantidad * cmp_compras_detalles.costo) as importe
            FROM cmp_compras_detalles
            inner join inv_productos on inv_productos.id = cmp_compras_detalles.producto_id and cmp_compras_detalles.compra_id = $compra_id";
        $detalles = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($detalles as $elemento){
            $total_recibido = $this->get_total_recibido($elemento['compra_id'], $elemento['producto_id']);
            $detalle=(object)array();
            $detalle->id = $elemento['id'];
            $detalle->compra_id = $elemento['compra_id'];
            $detalle->presentacion_id = intval($elemento['presentacion_id']);
            $detalle->cantidad = floatval($elemento['cantidad']);
            $detalle->costo = number_format(floatval($elemento['costo']),2,'.',',');
            $detalle->producto_id = $elemento['producto_id'];
            $detalle->descripcion = $elemento['descripcion'];
            $detalle->producto = $elemento['producto'];
            $detalle->importe = number_format(floatval($elemento['importe']),2,'.',',');
            $detalle->created = date("Y/m/d", strtotime($elemento['created']));
            $detalle->total_recibido = $total_recibido;
            array_push($nuevo,$detalle);
        }
        $this->content['movimientos_detalles'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV()
    {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('PRODUCTO', 'CANTIDAD', 'COSTO UNITARIO', 'DESCRIPCION'), ',');

        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv');
        $this->response->setHeader(
            'Content-Disposition', 'attachment; filename=Plantilla_detalles_compra.csv'
        );
        $this->response->setContent($output);
        $this->response->send();
    }

    private function detect_sjis_win($filename){
        $file = null;
        if  (($file = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                foreach ($row as $r)  {
                    if(strtoupper(mb_detect_encoding($r, "ASCII, UTF-8, UNICODE, sjis-win, ISO-8859-1")) === strtoupper('SJIS-win')){
                        fclose($file);
                        return true;
                    }
                }
            }
            fclose($file);
        }
        return false;
    }

    private function detect_utf_8_bom($filename) {
        $str = file_get_contents($filename);
        $bom = pack("CCC", 0xef, 0xbb, 0xbf);
        if (0 === strncmp($str, $bom, 3)) {
            return true;
        }
        return false;
    }

    public function cargarCSV () {
        try {
            $content = $this->content;
            $isCsv = true;
            $request = $this->request->getPost();
            $content['err'] = '';

            if ($this->request->hasFiles())  {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/compras_detalles/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                    chmod($upload_dir, 0777);
                }
                $fullPath = '';
                foreach ($this->request->getUploadedFiles() as $file) {
                    if  ($file->getExtension() !== 'csv')  {
                        $isCsv = false;
                        break;
                    }
                    $fullPath = $upload_dir .'detalle_compra'.'.'.$file->getExtension();
                    if  (file_exists($fullPath))  {
                       @unlink($fullPath);
                    }
                    $file->moveTo($fullPath);
                }

                $file = null;
                $archivo_utf_8=$this->detect_utf_8_bom($fullPath);
                $archivo_sjis_win=$this->detect_sjis_win($fullPath);

                if($archivo_utf_8 || $archivo_sjis_win) {

                    if  (($file = fopen($fullPath, 'r')) !== FALSE) {
                        $csvData = [];
                        $filterData = [];
                        while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                            $aux = [];
                            if($archivo_utf_8 && $archivo_sjis_win === false) {
                                foreach ($row as $r)  {
                                    $aux[] = $r;
                                }
                                $csvData[] = $aux;
                            } else if ($archivo_sjis_win && $archivo_utf_8 === false) {
                                foreach ($row as $r) {
                                    $aux[] = utf8_encode($r);
                                }
                                $csvData[] = $aux;
                            }
                        }
                        fclose($file);
                        array_shift($csvData);
                        
                        $rowNum = 2;

                        if(array_key_exists(0,$csvData)) {
                            if(array_key_exists(4,$csvData[0])) {
                                $content['err'] =  'No  es  un  .csv  válido, por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información1.';
                            } else if (array_key_exists(3,$csvData[0])) {
                                foreach ($csvData  as  $row)  {
                                    $_1 = trim(strtoupper($row[0]));
                                    $_2 = trim(strtoupper($row[1]));
                                    $_3 = trim(strtoupper($row[2]));
                                    $_4 = trim(strtoupper($row[3]));

                                    $auxToFilter  =  [
                                        'producto' => 0,
                                        'cantidad' => 0,
                                        'costo' => 0,
                                        'descripcion' => '',
                                        'linea' => ''
                                    ];

                                    $auxToFilter['linea'] = $rowNum;
                                    
                                    if($_1 === '') {
                                        $content['err'] = 'El campo PRODUCTO esta vacío A:{'.$rowNum.'}';
                                    } else {
                                        $student = Productos::findFirst("nombre = "."'".$_1."'");
                                        if ($student) {
                                            $auxToFilter['producto'] = $_1;
                                        } else {
                                            $content['err'] = 'El campo PRODUCTO no pertenece a ninguno registrado A:{'.$rowNum.'}';
                                        }
                                    }
                                    if($_2 === '') {
                                        $content['err'] = 'El campo CANTIDAD esta vacío B:{'.$rowNum.'}';
                                    } else {
                                        if (is_numeric($_2)) {
                                            $auxToFilter['cantidad'] = $_2;
                                        } else {
                                            $content['err'] = 'El campo CANTIDAD no es numérico B:{'.$rowNum.'}';
                                        }
                                    }
                                    if(strpos($_3,'$') === false) {
                                        if(floatval($_3)<=1000000000) {
                                            $auxToFilter['costo'] = floatval($_3);
                                        } else{
                                            $content['err'] = 'El valor máximo para el campo costo es $1000000000.00, revise la fila C:{'.$rowNum.'}';
                                        }
                                    } else {
                                        if(floatval($_3)<=1000000000) {
                                            $auxToFilter['costo'] = floatval(str_replace('$','',str_replace(',','',$_3)));
                                        } else {
                                            $content['err'] = 'El valor máximo para el campo costo es $1000000000.00, revise la fila C:{'.$rowNum.'}';
                                        }
                                    }
                                    if (trim(strtoupper($_4)) == '') {
                                        $auxToFilter['descripcion'] = null;
                                    } else {
                                        $auxToFilter['descripcion'] = trim(strtoupper($_4));
                                    }    
                                    $filterData[]  =  $auxToFilter;
                                    $rowNum++;
                                }
                            } else {
                                $content['err'] =  'Número de columnas incorrecto';
                            }
                        } else {
                            $content['err'] =  'No es un .csv , por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información2.';
                        }
                        
                        if  ($content['err']  ===  '')  {
                            $tx = $this->transactions->get();
                            $content['result']  =  '';
                            // var_dump($filterData);

                            if  (count($filterData)  >  0)  {
                                foreach  ($filterData  as  $single) {
                                    $producto = Productos::findFirst("nombre = '{$single['producto']}'");
                                    if ($producto) {
                                        $detalle = new ComprasDetalles();
                                        $detalle->setTransaction($tx);
                                        $validUser = Auth::getUserData($this->config);
                                        $detalle->created_by = $validUser->user_id;
                                        $detalle->compra_id = intval($request['compra_id']);
                                        $detalle->presentacion_id = $producto->presentacion_id;
                                        $detalle->producto_id = $producto->id;
                                        $detalle->cantidad = floatval($single['cantidad']);
                                        $detalle->costo = floatval($single['costo']);
                                        $detalle->descripcion = $single['descripcion'];

                                        if ($detalle->create()) {
                                            $content['result'] = true;
                                        } else {
                                            $content['error'] = Helpers::getErrors($detalle);
                                            $content['err'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el detalle.'];
                                            $tx->rollback();
                                        }
                                    }
                                }
                                if ($content['result'] == true) {
                                    $content['result'] = 'success';
                                    $content['message']  =  'Se  han  cargado  todos  los  productos.';
                                    $tx->commit();
                                }
                            } else  {
                                $content['err']  =  'Sin  datos  a  insertar.';           
                            }
                        }
                        if  (!$isCsv)  {
                            $content['err']  =  'No  es  un  .csv  válido!';
                        }
                    }
                } else {
                    $content['err'] =  'No  es  un  .csv  válido, por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información3.';
                }
            } 
        }catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $detalle = new ComprasDetalles();
            $detalle->setTransaction($tx);
            $validUser = Auth::getUserData($this->config);
            $detalle->created_by = $validUser->user_id;
            $detalle->compra_id = intval($request['compra_id']);
            $detalle->presentacion_id = intval($request['presentacion_id']);
            $detalle->producto_id = intval($request['producto_id']);
            $detalle->cantidad = floatval($request['cantidad']);
            $detalle->costo = floatval($request['costo']);
            $detalle->descripcion = trim(strtoupper($request['descripcion']));
            if ($detalle->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han guardado los cambios.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($detalle);
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
            $detalle = ComprasDetalles::findFirst($request['id']);
            if ($detalle) {
                $detalle->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $detalle->modified_by = $validUser->user_id;
                $detalle->compra_id = intval($request['compra_id']);
                $detalle->producto_id = intval($request['producto_id']);
                $detalle->presentacion_id = intval($request['presentacion_id']);
                $detalle->cantidad = floatval($request['cantidad']);
                $detalle->costo = floatval($request['costo']);
                $detalle->descripcion = trim(strtoupper($request['descripcion']));
                if ($detalle->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han guardado los cambios.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($detalle);
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
                    $detalle = ComprasDetalles::findFirst($request['id']);
                    $detalle->setTransaction($tx);

                    if ($detalle->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($detalle);
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

    public function deleteByCompra ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['compra_id']) {
                    $detalles = ComprasDetalles::find('compra_id = '.$request['compra_id']);
                    foreach ($detalles as $detalle) {
                        $detalle->setTransaction($tx);
                        if ($detalle->delete()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($detalle);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }
                    // var_dump($detalles);
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han eliminado los detalles.'];
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