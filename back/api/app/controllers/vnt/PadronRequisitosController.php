<?php

use Phalcon\Mvc\Controller;

class PadronRequisitosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getByProveedor ($proveedor_id,$cliente_id)
    {
        
        $sql = "SELECT id, nombre, padron_id from exp_requisitos where padron_id = $proveedor_id
        union 
        SELECT id, nombre, padron_id from exp_requisitos where padron_id is null";

        $data = $this->db->query($sql)->fetchAll();

        $registros = [];
        foreach ($data as $registro) {
            $r = (object)array();
            $r->id = $registro['id'];
            $id = $registro['id'];
            $sql_documentos = "SELECT id, id_lista, doc_type, name from vnt_documentos_padron where id_lista = $id and padron_id = $proveedor_id";
            $r->documentos = $this->db->query($sql_documentos)->fetchAll();
            $r->nombre = $registro['nombre'];
            $r->padron_id = $registro['padron_id'];
            array_push($registros, $r);
        }
        $this->content['padron_requisitos'] = $registros;
        $this->content['result'] = 'success';
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
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/documentos_padron/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }


                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf'||strtolower($file->getExtension())==='docx'){

                            $nombre = $request['nombre'];
                            
                                $documento = new DocumentosPadron();
                                $documento->setTransaction($tx);
                                $documento->id_lista = $request['id'];
                                $documento->doc_type = $file->getExtension();
                                $documento->name=$nombre;
                                $documento->padron_id = $request['proveedor_id'];

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
                                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                            $path=$upload_dir . $nombre_con_id;
                                            $this->orientation($path);
                                        }
                                    $content['result'] = 'success';
                                    $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo'];
                                    $tx->commit();

                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png y .pdf'];
                        }
                    }
                }

            } else {
                $content['message'] = 'No hay archivos.';
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

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = DocumentosPadron::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/documentos_padron/' .$documento->id.'.'.$ext;
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

    public function deleteFile() {    
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $documentos = DocumentosPadron::findFirst($request['id']);
                $documentos->setTransaction($tx);
                if ($documentos->delete()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el documento.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($documentos);
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
}