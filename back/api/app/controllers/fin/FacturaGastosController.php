<?php

use Phalcon\Mvc\Controller;

class FacturaGastosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll() {
        $sql = "SELECT *
                FROM fin_factura_gastos";
        $this->content['facturas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/facturas/' .$documento->id.'.'.$ext;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, "\"".$documento->name."\"");
        $response->send();
        return $response;
    }

    public function deleteFormato() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['factura_id']) {
                    $factura = FacturaGastos::findFirst($request['factura_id']);
                    $factura->setTransaction($tx);

                    $id = $factura->documento_id;
                    $monto = $factura->monto;
                    $gasto_id = intval($factura->gasto_id);
                    if ($factura->delete()) {
                        $documento = SysDocuments::findFirst($request['id']);
                        $documento->setTransaction($tx);
                        $xml = $documento->doc_type;
                        if($documento->name !== null){
                            $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/facturas/'.$documento->id.'.'.$documento->doc_type;
                            unlink($nombre_fichero);
                            if ($documento->delete()) {
                                //print_r("documento");
                                if ($xml === 'xml' || $xml === 'XML') {
                                    $gastos = GastosSolicitudes::findFirst($gasto_id);
                                    if ($gastos) {
                                        $gastos->setTransaction($tx);
                                        $gastos->sumatoria_factura = $gastos->sumatoria_factura - floatval($monto);
                                        //print_r("gasto");            
                                        if ($gastos->update()) {
                                            $this->content['result'] = 'success';
                                            $this->content['message'] = ['title' => 'Éxito', 'content' => 'Se ha eliminado la factura.'];
                                            $tx->commit();
                                        } else {
                                            $this->content['error1'] = Helpers::getErrors($gastos);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el monto facturado del gasto.'];
                                            $tx->rollback();
                                        }
                                    } else {
                                        $this->content['error2'] = 'Error';
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el monto facturado del gasto.'];
                                        // $tx->rollback();
                                    }
                                } else {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la factura.'];
                                    $tx->commit();
                                }
                                //
                            } else {
                                $this->content['error3'] = Helpers::getErrors($documento);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la factura.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error4'] = Helpers::getErrors($factura);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }       
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function uploadArchivo()
    {
        $uuid = '';
        $message = '';

        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($this->request->hasFiles()) {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/facturas/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                $uuid = '';
                $message = '';
                $rfc_receptor = '';
                $empresa = false;
                $rfc_emisor = '';
                $empresa2 = false;
                $comprobacion_empresa = false;
                $flag_pertenencia = false;

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx' || strtolower($file->getExtension())==='txt') {
                                $img = $request['nombre'];
                                $nombre = $request['nombre'];

                                if(intval($request['formato_requisito_id']) === 0) {

                                    $documento = new SysDocuments();
                                    $documento->setTransaction($tx);
                                    $validUser = Auth::getUserData($this->config);
                                    $documento->account_id = $validUser->account_id;
                                    $documento->doc_type = $file->getExtension();
                                    $documento->name=$img;

                                    if ($documento->create()) {
                                        $id = $documento->id;
                                        $nombre_con_id = $id .'.'. $file->getExtension();
                                        // aqui empieza lo de guardar el documento con el numero de id
                                        foreach (glob($upload_dir.$nombre_con_id.'.*') as $nombre_fichero) {
                                            unlink($nombre_fichero);
                                        }
                                        $file->moveTo($upload_dir . $nombre_con_id);
                                        if (file_exists($upload_dir . $nombre_con_id)) {
                                            chmod($upload_dir . $nombre_con_id, 0777);
                                        }
                                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg') {
                                            $path=$upload_dir . $nombre_con_id;
                                            // $this->orientation($path);
                                        }

                                        $uuid = '';
                                        libxml_use_internal_errors(true);
                                        
                                        if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                                            $xml = simplexml_load_file($upload_dir.$nombre_con_id);
                                            $ns = $xml->getNamespaces(true);
                                            $xml->registerXPathNamespace('c', $ns['cfdi']);
                                            $xml->registerXPathNamespace('t', $ns['tfd']);

                                            $total_factura = 0;          
                                            foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                                                $total_factura = $cfdiComprobante['Total']; 
                                            }
                                            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                                                // $factura->uuid = ''.$tfd['UUID'];
                                                $uuid = ''.$tfd['UUID'];
                                                // $factura->monto = floatval($total_factura);
                                            }
                                            foreach ($xml->xpath('//cfdi:Receptor') as $cfdiReceptor) { 
                                                $rfc_receptor = $cfdiReceptor['Rfc'];
                                            }
                                            foreach ($xml->xpath('//cfdi:Emisor') as $cfdiEmisor) { 
                                                $rfc_emisor = $cfdiEmisor['Rfc'];
                                            }
                                            if ($rfc_receptor !== '') {
                                                $empresa = Empresa::findFirst("rfc = '{$rfc_receptor}'");
                                            }
                                            if ($rfc_emisor !== '') {
                                                $empresa2 = Empresa::findFirst("rfc = '{$rfc_emisor}'");
                                            }
                                        }
                                        if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                                        $existe_factura = false;
                                        if ($uuid !== '' && $uuid != 'UUID') {
                                            $existe_factura = FacturaGastos::findFirst(
                                                [
                                                    'uuid = :uuid:',
                                                    'bind' => [
                                                        'uuid' => $uuid
                                                    ]
                                                ]
                                            );
                                            if ($existe_factura !== false) {
                                                $id_gasto_factura = $existe_factura->gasto_id;
                                                $gasto_factura = GastosSolicitudes::findFirst($id_gasto_factura
                                                );
                                                $content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en la solicitud #'. $gasto_factura->solicitud_id . '.'];
                                                // $tx->rollback();
                                                // $message = 'Esta factura ya existe en la solicitud #'. $gasto_factura->solicitud_id . '.';
                                                // break;
                                            }
                                        }

                                        if (!$empresa && !$empresa2) {
                                                        $comprobacion_empresa = false;
                                                        
                                                        $content['message'] = ['title' => '¡Error!', 'content' => 'El rfc del receptor y/o emisor no corresponde a la empresa vinculada a esta solicitud, no se guardarán cambios.'];
                                                    

                                                    }

                                        if ($empresa || $empresa2) {
                                            $gasto = GastosSolicitudes::findFirst($request['gasto_id']);
                                            if ($gasto) {
                                                $solicitud = SolicitudesO::findFirst($gasto->solicitud_id);
                                                if ($solicitud) {
                                                    if ($empresa) {
                                                        if ($solicitud->empresa_id == $empresa->id) {
                                                            $comprobacion_empresa = true;
                                                            $flag_pertenencia = true;
                                                        }
                                                    }
                                                    if ($empresa2) {
                                                        if ($solicitud->empresa_id == $empresa2->id) {
                                                            $comprobacion_empresa = true;
                                                            $flag_pertenencia = true;
                                                        }
                                                    }
                                                    if (!$empresa && !$empresa2) {
                                                        $comprobacion_empresa = false;
                                                        
                                                        $content['message'] = ['title' => '¡Error!', 'content' => 'El rfc del receptor y/o emisor no corresponde a la empresa vinculada a esta solicitud, no se guardarán cambios.'];
                                                    

                                                    }
                                                }
                                            }
                                        } else if ($empresa == false && ($file->getExtension() === 'xml' || $file->getExtension() === 'XML')) {
                                            
                                                // code...
                                            $comprobacion_empresa = false;
                                            $content['message'] = ['title' => '¡Error!', 'content' => 'El rfc del receptor y/o emisor no corresponde a ninguna empresa registrada, revise el catálogo de empresas.'];
                                            
                                            
                                           
                                        }
                                    }
                                    if ($file->getExtension() != 'xml' && $file->getExtension() != 'XML') {
                                        $existe_factura = false;
                                        $comprobacion_empresa = true;
                                        $flag_pertenencia = true;
                                    }
                                        if ($existe_factura == false && $comprobacion_empresa == true && $flag_pertenencia == true) {
                                            $factura = new FacturaGastos();
                                            $factura->setTransaction($tx);
                                            $factura->documento_id = $id;
                                            $factura->gasto_id = $request['gasto_id'];

                                            if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                                                $factura->uuid = $uuid;
                                                $factura->monto = floatval($total_factura);
                                            }

                                            if ($factura->create()) {
                                                
                                                if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {

                                                    $gastos = GastosSolicitudes::findFirst($request['gasto_id']);
                                                    if ($gastos) {
                                                        $gastos->setTransaction($tx);
                                                        $gastos->sumatoria_factura = $gastos->sumatoria_factura + floatval($total_factura);
                                                        
                                                        if ($gastos->update()) {
                                                            $content['result'] = 'success';
                                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                                            $tx->commit();
                                                        } else {
                                                            $content['error'] = Helpers::getErrors($gastos);
                                                            $content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                                            $tx->rollback();
                                                        }
                                                    } else {
                                                        $content['error'] = 'Error';
                                                        $content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                                        $tx->rollback();
                                                    }
                                                } else {
                                                    $content['result'] = 'success';
                                                    $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                                    $tx->commit();
                                                }
                                            } else {
                                                $content['error'] = Helpers::getErrors($factura);
                                                $content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                                $tx->rollback();
                                            }
                                        }
                                        if ($flag_pertenencia == false && ($file->getExtension() === 'xml' || $file->getExtension() === 'XML')) {
                                            $content['message'] = ['title' => '¡Error!', 'content' => 'El rfc del emisor o receptor no coincide con el de la solicitud.'];
                                        }
                                    }
                                }
                        } else {
                            $content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
                        }
                    }
                }
            } else {
                $content['message'] = 'No hay archivos.';
            }
        } catch (Throwable $e) {
            // var_dump($e);
            $content['result'] = 'error';
            // var_dump($message);
            if ($message !== '') {
                $content['message'] = ['title' => '¡Error!', 'content' => $message];
            }
            $content['message'] = ['title' => '¡Error!', 'content' => get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL];
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    private function orientation($ruta) {
        try {
            $exif = @exif_read_data($ruta);
            $image = imagecreatefromjpeg($ruta);
            if(isset($exif) && array_key_exists('Orientation',$exif)) {
                switch($exif['Orientation']){
                    case 8:
                        $image = imagerotate($image,90,0);
                        break;
                    case 3:
                        $image = imagerotate($image,180,0);
                        break;
                    case 6:
                        $image = imagerotate($image,-90,0);
                        break;
                }
            }
            imagejpeg($image, $ruta);
        } catch (Exception $e){
        }
    }
}