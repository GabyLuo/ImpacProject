<?php

use Phalcon\Mvc\Controller;

class EventosController extends Controller
{
	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT lic_eventos.id, lic_eventos.licitacion_id,lic_eventos.documento_id, lic_eventos.evento, lic_eventos.fecha_evento, (select sys_documents.name from sys_documents where sys_documents.id = lic_eventos.documento_id) as name, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_eventos.documento_id) as doc_type from lic_eventos order by lic_eventos.id";

        $this->content['eventos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
	
	public function getEventosByLicitacion ($licitacion_id)
    {
        $sql = "SELECT lic_eventos.id, lic_eventos.licitacion_id,lic_eventos.documento_id, lic_eventos.evento, lic_eventos.fecha_evento, (select sys_documents.name from sys_documents where sys_documents.id = lic_eventos.documento_id) as name, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_eventos.documento_id) as doc_type
		from lic_eventos
		where licitacion_id = $licitacion_id order by lic_eventos.id";

        $eventos = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($eventos as $elemento){
            $l=(object)array();
            $l->id = $elemento['id'];
            $id = $elemento['id'];
            $l->licitacion_id = $elemento['licitacion_id'];
            $l->documento_id = $elemento['documento_id'];
            $l->evento = $elemento['evento'];
            $l->fecha_evento = date("Y/m/d H:i", strtotime($elemento['fecha_evento']));
            $l->name = $elemento['name'];
            $l->doc_type = $elemento['doc_type'];
            $sql_documentos = "SELECT * from lic_documentos_eventos where evento_id = $id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $nuevo_documentos = [];
            foreach ($documentos as $documento) {
                $doc=(object)array();
                $doc->id = $documento['id'];
                $doc->evento_id = $documento['evento_id'];
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
            $l->documentos = $nuevo_documentos;
            array_push($nuevo,$l);
        }

        $this->content['eventos'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete() {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $eventos = Eventos::findFirst($request['id']);
                    $eventos->setTransaction($tx);

                    $id = $eventos->documento_id;
                    if ($eventos->delete()) {

                        if($id !== null) {
                            $documentos = SysDocuments::findFirst(intval($id));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/eventos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }

                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el evento.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el evento.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($eventos);
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

    public function save () {

        $request = $this->request->getPost();
        $tx = $this->transactions->get();
        date_default_timezone_set("America/Mexico_City");
        $eventos = new Eventos();
        $eventos->setTransaction($tx);
        $eventos->licitacion_id = intval($request['licitacion_id']);
        if(trim($request['evento'] === '')){
            $eventos->evento = null;
        } else {
            $eventos->evento = trim($request['evento']);
        }

        if($request['fecha_evento'] === ''){
            $eventos->fecha_evento = null;
        } else {
            $eventos->fecha_evento = date("Y/m/d H:i:s", strtotime($request['fecha_evento']));
        }

        if ($eventos->create()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha creado el evento.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($eventos);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el evento al registro de la licitacion.'];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update () {
        $request = $this->request->getPut();
        $tx = $this->transactions->get();
        date_default_timezone_set("America/Mexico_City");
        $eventos = Eventos::findFirst(intval($request['id']));
        $eventos->setTransaction($tx);
        if(trim($request['evento'] === '')){
            $eventos->evento = null;
        } else {
            $eventos->evento = trim($request['evento']);
        }

        if($request['fecha_evento'] === ''){
            $eventos->fecha_evento = null;
        } else {
            $eventos->fecha_evento = date("Y/m/d H:i:s", strtotime($request['fecha_evento']));
        }

        if ($eventos->update()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha actualizado el evento.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($eventos);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el evento al registro de la licitacion.'];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/eventos/' .$documento->id.'.'.$documento->doc_type;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, $documento->name);
        $response->send();
        return $response;
    }

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($this->request->hasFiles()) {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/eventos/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx'){
                            
                            $img = $request['nombre'];
                            $nombre = $request['nombre'];

                            if(intval($request['formato_requisito_id']) === 0){

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                $documento->doc_type = $file->getExtension();
                                $documento->name=$img;

                                if ($documento->create()) {
                                    $id = $documento->id;
                                    $nombre_con_id = $id .'.'. $file->getExtension();

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
                        
                                    $eventos = Eventos::findFirst(intval($request['id']));
                                    $eventos->setTransaction($tx);
                                    $eventos->documento_id = $id;
                                    $eventos->tipo = $file->getExtension();
                                    if ($eventos->update()) {
                                        $content['result'] = 'success';
                                        $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                        $tx->commit();
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($eventos);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la licitación.'];
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
                                $tipo = $documento->doc_type;
                                $documento->doc_type=$file->getExtension();
                                $documento->name=$img;
                                if($documento->update()){
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/eventos/'.$documento->id.'.'.$tipo;
                                    unlink($nombre_fichero);
                                    $file->moveTo($upload_dir . $documento->id . '.' . $documento->doc_type);
                                    if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                        $path=$upload_dir . $documento->id . '.' . $documento->doc_type;
                                        $this->orientation($path);
                                    }
                                    $content['result'] = 'success';
                                    $content['message'] = ['title' => '¡Exito!', 'content' => 'Se actualizó el archivo.'];
                                    $tx->commit();
                                }else{
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

    public function uploadDocumento () {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/documentos_eventos/';
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
                                $documentos = new DocumentosEventos();
                                $documentos->setTransaction($tx);
                                $documentos->evento_id = intval($request['id']);
                                $documentos->archivo = $request['nombre'];
                                $documentos->extension = $file->getExtension();
                                if ($documentos->create()) {
                                    $nombre_con_id = $documentos->id .'.'. $file->getExtension();
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
                                    $this->content['error'] = Helpers::getErrors($documentos);
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

    public function getFileDocumentos($id, $ext)
    {    
        $documento = DocumentosEventos::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/documentos_eventos/' .$documento->id.'.'.$documento->extension;
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
            $documento = DocumentosEventos::findFirst($request['id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/documentos_eventos/'.$documento->id.'.'.$documento->extension;
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