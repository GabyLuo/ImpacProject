<?php

use Phalcon\Mvc\Controller;

class CedulasPerfilesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_cedulas
            ORDER BY archivo";
        $this->content['cedulas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT * 
            FROM per_cedulas
            where perfil_id = $perfil_id
            ORDER BY archivo";
        $documentos = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($documentos as $documento) {
            $doc=(object)array();
            $doc->id = $documento['id'];
            $doc->perfil_id = $documento['perfil_id'];
            $doc->archivo = $documento['archivo'];
            $doc->extension = $documento['extension'];
            if ($documento['extension'] === 'docx') {
                $doc->color = 'blue-9';
                $doc->icon = 'fas fa-file-word';
            } else if ($documento['extension'] === 'pdf' || $documento['extension'] === 'PDF') {
                $doc->color = 'red-10';
                $doc->icon = 'fas fa-file-pdf';
            } else if ($documento['extension'] === 'xml' || $documento['extension'] === 'XML') {
                $doc->color = 'light-green';
                $doc->icon = 'fas fa-file-code';
            } else if ($documento['extension'] === 'jpg' || $documento['extension'] === 'jpeg' || $documento['extension'] === 'png' || $documento['extension'] === 'JPG' || $documento['extension'] === 'JPEG' || $documento['extension'] === 'PNG') {
                $doc->color = 'amber';
                $doc->icon = 'fas fa-file-image';
            }
            array_push($nuevo, $doc);
        }
        $this->content['cedulas'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
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

    public function uploadCedula () {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($this->request->hasFiles()) {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cedulas_perfiles/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx') {
                                $cedulas = new Cedulas();
                                $cedulas->setTransaction($tx);
                                $cedulas->perfil_id = intval($request['id']);
                                $cedulas->archivo = $request['nombre'];
                                $cedulas->extension = $file->getExtension();
                                if ($cedulas->create()) {
                                    $nombre_con_id = $cedulas->id .'.'. $file->getExtension();
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
                                    $content['message'] = ['title' => 'Éxito', 'content' => 'Se ha guardado el archivo.'];
                                    $tx->commit();
                                } else {
                                    $this->content['error'] = Helpers::getErrors($cedulas);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro del perfil.'];
                                    $tx->rollback();
                                }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
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

    public function getFile($id, $ext)
    {    
        $documento = Cedulas::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cedulas_perfiles/' .$documento->id.'.'.$documento->extension;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, "\"".$documento->archivo."\"");
        $response->send();
        return $response;
    }

    public function deleteFormato() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $documento = Cedulas::findFirst($request['id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cedulas_perfiles/'.$documento->id.'.'.$documento->extension;
                    if (file_exists($nombre_fichero)) {
                        unlink($nombre_fichero);
                    }
                    if ($documento->delete()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el archivo.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($documento);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                } else {
                    $this->content['result'] = 'error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró el documento.'];
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}