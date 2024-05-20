<?php

use Phalcon\Mvc\Controller;

class CursosPerfilesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, perfil_id, nombre, universidad, to_char(fecha,'DD/MM/YYYY') as fecha, to_char(fecha_egreso,'DD/MM/YYYY') as fecha_egreso, titulo, extension, tipo, horas
            FROM per_cursos
            ORDER BY nombre";
        $this->content['cursos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id, $tipo)
    {
        $sql = "SELECT id, perfil_id, nombre, universidad, to_char(fecha,'DD/MM/YYYY') as fecha, to_char(fecha_egreso,'DD/MM/YYYY') as fecha_egreso, titulo, extension, tipo, horas 
            FROM per_cursos
            where perfil_id = $perfil_id and tipo = '$tipo'
            ORDER BY nombre";
        $cursos = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($cursos as $elemento){
            $curso=(object)array();
            $curso->id = $elemento['id'];
            $id = $elemento['id'];
            $curso->perfil_id = $elemento['perfil_id'];
            $curso->nombre = $elemento['nombre'];
            $curso->universidad = $elemento['universidad'];
            $curso->fecha = $elemento['fecha'];
            $curso->fecha_egreso = $elemento['fecha_egreso'];
            $curso->titulo = $elemento['titulo'];
            $curso->extension = $elemento['extension'];
            $curso->tipo = $elemento['tipo'];
            $curso->horas = $elemento['horas'];
            $sql_documentos = "SELECT * from per_cursos_documentos where curso_id = $id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $nuevo_documentos = [];
            foreach ($documentos as $documento) {
                $doc=(object)array();
                $doc->id = $documento['id'];
                $doc->curso_id = $documento['curso_id'];
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
            $curso->documentos = $nuevo_documentos;
            array_push($nuevo,$curso);
        }
        $this->content['cursos'] = $nuevo;
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
            $cursos = CursosPerfiles::findFirst(
                [
                    'nombre = :nombre: AND (perfil_id = :perfil_id:) AND (tipo = :tipo:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'perfil_id' => intval($request['perfil_id']),
                        'tipo' => $request['tipo']
                    ]
                ]
            );
            if ($cursos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un curso con este nombre para este perfil'];
            } else {
                $cursos = new CursosPerfiles();
                $cursos->setTransaction($tx);
                $cursos->perfil_id = intval($request['perfil_id']);
                $cursos->fecha = date("Y/m/d", strtotime($request['fecha']));
                $cursos->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                $cursos->universidad = trim(strtoupper($request['universidad']));
                $cursos->nombre = trim(strtoupper($request['nombre']));
                $cursos->tipo = $request['tipo'];
                $cursos->horas = intval($request['horas']);

                if ($cursos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el curso.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cursos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el curso.'];
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
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cursos_perfiles/';
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
                                $cursos = new CursosDocumentos();
                                $cursos->setTransaction($tx);
                                $cursos->curso_id = intval($request['id']);
                                $cursos->archivo = $request['nombre'];
                                $cursos->extension = $file->getExtension();
                                if ($cursos->create()) {
                                    $nombre_con_id = $cursos->id .'.'. $file->getExtension();
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
                                    $this->content['error'] = Helpers::getErrors($cursos);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro del curso.'];
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
        $documento = CursosDocumentos::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cursos_perfiles/' .$documento->id.'.'.$documento->extension;
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
            $documento = CursosDocumentos::findFirst($request['id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/cursos_perfiles/'.$documento->id.'.'.$documento->extension;
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
            $cursos = CursosPerfiles::findFirst(
                [
                    'id != :id: AND (nombre = :nombre:) AND (perfil_id = :perfil_id:) AND (tipo = :tipo:)',
                    'bind' => [
                        'nombre' => strtoupper($request['nombre']),
                        'perfil_id' => intval($request['perfil_id']),
                        'tipo' => $request['tipo'],
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($cursos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un curso con este nombre para este perfil'];
            } else {
                $cursos = CursosPerfiles::findFirst($request['id']);
                if ($cursos) {
                    $cursos->setTransaction($tx);
                    $cursos->perfil_id = intval($request['perfil_id']);
                    $cursos->fecha = date("Y/m/d", strtotime($request['fecha']));
                    $cursos->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                    $cursos->universidad = trim(strtoupper($request['universidad']));
                    $cursos->nombre = trim(strtoupper($request['nombre']));
                    $cursos->tipo = $request['tipo'];
                    $cursos->horas = intval($request['horas']);

                    if ($cursos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el curso.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($cursos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el curso'];
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
                    $cursos = CursosPerfiles::findFirst($request['id']);
                    $cursos->setTransaction($tx);

                    if ($cursos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($cursos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el curso.'];
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