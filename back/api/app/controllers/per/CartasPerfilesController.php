<?php

use Phalcon\Mvc\Controller;

class CartasPerfilesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_cartas
            ORDER BY id";
        $this->content['cartas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $data = [];
        $sql_perfil = "SELECT * FROM per_perfiles_expertos where id = $perfil_id";
        $perfil = $this->db->query($sql_perfil)->fetchAll();
        for ($i=0; $i<2; $i++) {
            $cartas_perfil=(object)array();
            $cartas_perfil->id = $perfil[0]['id'];
            if ($i == 0) {
                $cartas_perfil->tipo = 'Laborales';
                $sql = "SELECT * 
                FROM per_cartas
                where perfil_id = $perfil_id and tipo = 'Laborales'
                ORDER BY id";
            } else {
                $cartas_perfil->tipo = 'Personales';
                $sql = "SELECT * 
                FROM per_cartas
                where perfil_id = $perfil_id and tipo = 'Personales'
                ORDER BY id";
            }
            $cartas = $this->db->query($sql)->fetchAll();
                // name
                // doc_type
            $nuevo = [];
            foreach ($cartas as $elemento){
                $carta=(object)array();
                $carta->id = $elemento['id'];
                $carta->perfil_id = $elemento['perfil_id'];
                $carta->archivo = $elemento['archivo'];
                $carta->tipo = $elemento['tipo'];
                $carta->extension = $elemento['extension'];
                if ($elemento['extension'] === 'docx') {
                    $carta->color = 'blue-9';
                    $carta->icon = 'fas fa-file-word';
                } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                    $carta->color = 'red-10';
                    $carta->icon = 'fas fa-file-pdf';
                } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                    $carta->color = 'light-green';
                    $carta->icon = 'fas fa-file-code';
                } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                    $carta->color = 'amber';
                    $carta->icon = 'fas fa-file-image';
                }
                array_push($nuevo,$carta);
            }
            $cartas_perfil->cartas = $nuevo;
            array_push($data, $cartas_perfil);
        }
        
        $this->content['cartas'] = $data;
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

    public function uploadDocumento () {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cartas_perfiles/';
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
                                    $carta = new CartasPerfiles();
                                    $carta->setTransaction($tx);
                                    $carta->perfil_id = intval($request['perfil_id']);
                                    $carta->archivo = $request['nombre'];
                                    $carta->tipo = 'Laborales';
                                    $carta->empleo_id = $request['empleo_id'];
                                    $carta->extension = $file->getExtension();

                                    if ($carta->create()) {
                                        $nombre_con_id = $carta->id .'.'. $file->getExtension();
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
                                        $this->content['error'] = Helpers::getErrors($carta);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro del posgrado.'];
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
        $documento = CartasPerfiles::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cartas_perfiles/' .$documento->id.'.'.$documento->extension;
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
            $documento = CartasPerfiles::findFirst($request['id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cartas_perfiles/'.$documento->id.'.'.$documento->extension;
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