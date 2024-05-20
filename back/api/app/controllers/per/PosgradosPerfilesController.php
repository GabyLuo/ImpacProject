<?php

use Phalcon\Mvc\Controller;

class PosgradosPerfilesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_posgrados
            ORDER BY nombre";
        $this->content['posgrados'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT * 
            FROM per_posgrados
            where perfil_id = $perfil_id
            ORDER BY nombre";
        $posgrados = $this->db->query($sql)->fetchAll();
            // name
            // doc_type
        $nuevo = [];
        foreach ($posgrados as $elemento){
            $posgrado=(object)array();
            $posgrado->id = $elemento['id'];
            $id = $elemento['id'];
            $posgrado->perfil_id = $elemento['perfil_id'];
            $posgrado->nombre = $elemento['nombre'];
            $posgrado->universidad = $elemento['universidad'];
            $posgrado->fecha_egreso = $elemento['fecha_egreso'];
            $posgrado->titulo = $elemento['titulo'];
            $posgrado->extension = $elemento['extension'];
            $sql_documentos = "SELECT * from per_posgrados_documentos where posgrado_id = $id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $nuevo_documentos = [];
            foreach ($documentos as $documento) {
                $doc=(object)array();
                $doc->id = $documento['id'];
                $doc->posgrado_id = $documento['posgrado_id'];
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
                array_push($nuevo_documentos, $doc);
            }
            $posgrado->documentos = $nuevo_documentos;
            array_push($nuevo,$posgrado);
        }
        $this->content['posgrados'] = $nuevo;
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

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $posgrado = PosgradosPerfiles::findFirst(
                [
                    'nombre = :nombre: AND (perfil_id = :perfil_id:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'perfil_id' => intval($request['perfil_id'])
                    ]
                ]
            );
            if ($posgrado) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un posgrado con este nombre para este perfil'];
            } else {
                $posgrado = new PosgradosPerfiles();
                $posgrado->setTransaction($tx);
                $posgrado->perfil_id = intval($request['perfil_id']);
                $posgrado->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                $posgrado->universidad = trim(strtoupper($request['universidad']));
                $posgrado->nombre = trim(strtoupper($request['nombre']));
                if ($posgrado->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el posgrado.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($posgrado);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el posgrado.'];
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

    public function uploadDocumento () {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/posgrados_perfiles/';
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
                                $posgrado = new PosgradosDocumentos();
                                $posgrado->setTransaction($tx);
                                $posgrado->posgrado_id = intval($request['id']);
                                $posgrado->archivo = $request['nombre'];
                                $posgrado->extension = $file->getExtension();
                                if ($posgrado->create()) {
                                    $nombre_con_id = $posgrado->id .'.'. $file->getExtension();
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
                                    $this->content['error'] = Helpers::getErrors($posgrado);
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
        $documento = PosgradosDocumentos::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/posgrados_perfiles/' .$documento->id.'.'.$documento->extension;
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
            $documento = PosgradosDocumentos::findFirst($request['id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/posgrados_perfiles/'.$documento->id.'.'.$documento->extension;
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
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $posgrado = PosgradosPerfiles::findFirst(
                [
                    'id != :id: AND ((nombre = :nombre:) AND (perfil_id = :perfil_id:))',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'perfil_id' => intval($request['perfil_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($posgrado) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un posgrado con este nombre para este perfil'];
            } else {
                $posgrado = PosgradosPerfiles::findFirst($request['id']);
                if ($posgrado) {
                    $posgrado->setTransaction($tx);
                    $posgrado->perfil_id = intval($request['perfil_id']);
                    $posgrado->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                    $posgrado->universidad = trim(strtoupper($request['universidad']));
                    $posgrado->nombre = trim(strtoupper($request['nombre']));

                    if ($posgrado->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el posgrado.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($posgrado);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el posgrado'];
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
                    $perfiles = PosgradosPerfiles::findFirst($request['id']);
                    $perfiles->setTransaction($tx);

                    if ($perfiles->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el posgrado.'];
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