<?php

use Phalcon\Mvc\Controller;

class ArchivosTareasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

	public function getAll () {
		$sql="SELECT * from crm_tareas";
		$this->content['archivos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
	}

	public function getByTarea ($id) {
		$sql="SELECT * from crm_archivos where tarea_id = $id";
        $documentos = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($documentos as $elemento){
            $documento=(object)array();
            $documento->id = $elemento['id'];
            $documento->tarea_id = $elemento['tarea_id'];
            $documento->extension = $elemento['extension'];
            if ($elemento['extension'] === 'docx') {
                $documento->color = 'blue-9';
                $documento->icon = 'fas fa-file-word';
            } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                $documento->color = 'red-10';
                $documento->icon = 'fas fa-file-pdf';
            } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                $documento->color = 'light-green';
                $documento->icon = 'fas fa-file-code';
            } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                $documento->color = 'amber';
                $documento->icon = 'fas fa-file-image';
            }
            array_push($nuevo,$documento);
        }
		$this->content['archivos'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
	}

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = ArchivosTareas::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/archivos_tareas/' .$documento->id.'.'.$documento->extension;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, "\"".$documento->nombre."\"");
        $response->send();
        return $response;
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

	public function uploadDocumento()
    {
        try {
            $content = $this->content;

            $tx = $this->transactions->get();

            $request = $this->request->getPost();
            if ($this->request->hasFiles()) {
                // $upload_dir = __DIR__ . '../../../public/assets/logos/';
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/archivos_tareas/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                //var_dump($imagen_id);
                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    //var_dump($file->getType());
                    if($sizeA>100||$sizeA===0) {
                        $content['err'] = 'Archivo demasiado grande';
                    } else {
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx') {
                            //var_dump('renombre');
                            $archivo = new ArchivosTareas();
                            $archivo->setTransaction($tx);
                            $archivo->extension = $file->getExtension();
                            $archivo->tarea_id = $request['tarea_id'];
                            $archivo->nombre = $request['nombre'];
                            if ($archivo->create()) {
                                $nombre_con_id = $archivo->id .'.'. $file->getExtension();
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
                                $content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el archivo.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($archivo);
                                $content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                $tx->rollback();
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function deleteFormato () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $documento = ArchivosTareas::findFirst($request['id']);
                $documento->setTransaction($tx);

                if ($documento->delete()) {
                    $this->content['result'] = 'success';
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/archivos_tareas/'.$documento->id.'.'.$documento->extension;
                    if (file_exists($nombre_fichero)) {
                        unlink($nombre_fichero);
                    }
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el archivo.'];
                    $this->content['result'] = 'success';
                    $tx->commit();
                } else {
                    $this->content['result'] = 'error';
                    $this->content['error'] = Helpers::getErrors($documento);
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'No se ha podido eliminar el archivo.'];
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