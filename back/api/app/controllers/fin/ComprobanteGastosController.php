<?php

use Phalcon\Mvc\Controller;

class ComprobanteGastosController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll() {
        $sql = "SELECT *
                FROM fin_comprobante_gastos";
        $this->content['comprobantes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/comprobantes/' .$documento->id.'.'.$documento->doc_type;
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
            if ($request['comprobante_id']) {
                    $comprobante = ComprobanteGastos::findFirst($request['comprobante_id']);
                    $comprobante->setTransaction($tx);

                    $id = $comprobante->documento_id;
                    if ($comprobante->delete()) {
                        $documento = SysDocuments::findFirst($request['id']);
                        $documento->setTransaction($tx);
                        if($documento->name !== null){
                            $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/comprobantes/'.$documento->id.'.'.$documento->doc_type;
                            unlink($nombre_fichero);
                            if ($documento->delete()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el comprobante.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($documento);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el comprobante.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($comprobante);
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
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/comprobantes/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='' || strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx' || strtolower($file->getExtension())==='00' || strtolower($file->getExtension())==='txt'){
                            
                            if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                $nombrep = $request['nombre'];
                                $nombreo = explode(".", $nombrep);
                                $img = $nombreo[0];
                                $nombre = $nombreo[0];
                                // print_r("hola");
                            } else {
                                $img = $request['nombre'];
                                $nombre = $request['nombre'];
                                // print_r("hello");
                            }

                            if(intval($request['formato_requisito_id']) === 0){

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                    $documento->doc_type = 'pdf';
                                } else {
                                    $documento->doc_type = $file->getExtension();
                                }
                                $documento->name=$img;

                                if ($documento->create()) {
                                    $id = $documento->id;
                                    if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                        $nombre_con_id = $id .'.'. 'pdf';
                                    } else {
                                        $nombre_con_id = $id .'.'. $file->getExtension();
                                    }
                                    // aqui empieza lo de guardar el documento con el numero de id
                                    foreach (glob($upload_dir.$nombre_con_id.'.*') as $nombre_fichero) {
                                    unlink($nombre_fichero);
                                    }
                                    $file->moveTo($upload_dir . $nombre_con_id);
                                    if (file_exists($upload_dir . $nombre_con_id)) {
                                        chmod($upload_dir . $nombre_con_id, 0777);
                                    }
                                    if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                        $path=$upload_dir . $nombre_con_id;
                                        // $this->orientation($path);
                                    }

                                    $comprobante = new ComprobanteGastos();
                                    $comprobante->setTransaction($tx);
                                    $comprobante->documento_id = $id;
                                    $comprobante->gasto_id = $request['gasto_id'];
                                        if ($comprobante->create()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($comprobante);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                        $tx->rollback();
                                        }
                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }

                            } else {                               
                                $documento = SysDocuments::findFirst(intval($request['formato_requisito_id']));
                                $documento->setTransaction($tx);
                                if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                    $documento->doc_type = 'pdf';
                                } else {
                                    $documento->doc_type = $file->getExtension();
                                }
                                $documento->name=$img;
                                if($documento->update()) {
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/solicitudes/comprobantes/'.$documento->id;
                                    unlink($nombre_fichero);
                                    $file->moveTo($upload_dir . $documento->id);
                                    if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                        $path=$upload_dir . $documento->id;
                                        $this->orientation($path);
                                    }
                                    $content['result'] = 'success';
                                    $content['message'] = ['title' => '¡Exito!', 'content' => 'Se actualizó el archivo.'];
                                    $tx->commit();
                                } else {
                                    $content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el archivo.'];
                                    $tx->rollback();
                                }
                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
                        }
                    }
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
            }
        } catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
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