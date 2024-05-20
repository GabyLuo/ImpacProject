<?php

use Phalcon\Mvc\Controller;

class FianzasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT lic_fianzas.id, lic_fianzas.fianza, to_char(lic_fianzas.fecha_inicio,'DD/MM/YYYY') as fecha_inicio, to_char(lic_fianzas.fecha_fin,'DD/MM/YYYY') as fecha_fin, lic_fianzas.contrato, lic_fianzas.empresa, (select vnt_empresa.razon_social from vnt_empresa where vnt_empresa.id = lic_fianzas.empresa) as nombre_empresa, lic_fianzas.fecha_cancelacion,lic_fianzas.monto,lic_fianzas.documento_final, lic_fianzas.extension
            FROM lic_fianzas order by lic_fianzas.id";
        $fianzas = $this->db->query($sql)->fetchAll();
            // name
            // doc_type
        $nuevo = [];
        foreach ($fianzas as $elemento){
            $fianza=(object)array();
            $fianza->id = $elemento['id'];
            $id = $elemento['id'];
            $fianza->fianza = $elemento['fianza'];
            $fianza->fecha_inicio = $elemento['fecha_inicio'];
            $fianza->fecha_fin = $elemento['fecha_fin'];
            $fianza->contrato = $elemento['contrato'];
            $fianza->empresa = $elemento['empresa'];
            $fianza->nombre_empresa = $elemento['nombre_empresa'];
            $fianza->fecha_cancelacion = $elemento['fecha_cancelacion'];
            $fianza->monto = $elemento['monto'];
            $fianza->documento_final = $elemento['documento_final'];
            $fianza->extension = $elemento['extension'];
            if ($elemento['extension'] === 'docx') {
                $fianza->color = 'blue-9';
                $fianza->icon = 'fas fa-file-word';
            } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                $fianza->color = 'red-10';
                $fianza->icon = 'fas fa-file-pdf';
            } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                $fianza->color = 'light-green';
                $fianza->icon = 'fas fa-file-code';
            } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                $fianza->color = 'amber';
                $fianza->icon = 'fas fa-file-image';
            }
            $sql_documentos = "SELECT sys_documents.id as documento_id, sys_documents.name, sys_documents.doc_type, lic_documentos_fianzas.id as id
                            FROM sys_documents
                            inner join lic_documentos_fianzas
                            on sys_documents.id = lic_documentos_fianzas.documento_id 
                            and lic_documentos_fianzas.fianza_id= $id";
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $array_documentos = [];
            foreach ($documentos as $doc_elemento){
            $doc=(object)array();
            $doc->documento_id = $doc_elemento['documento_id'];
            $doc->name = $doc_elemento['name'];
            $doc->doc_type = $doc_elemento['doc_type'];
            $doc->id = $doc_elemento['id'];
            if ($doc_elemento['doc_type'] === 'docx') {
                  $doc->color = 'blue-9';
                  $doc->icon = 'fas fa-file-word';
                } else if ($doc_elemento['doc_type'] === 'pdf' || $doc_elemento['doc_type'] === 'PDF') {
                  $doc->color = 'red-10';
                  $doc->icon = 'fas fa-file-pdf';
                } else if ($doc_elemento['doc_type'] === 'xml' || $doc_elemento['doc_type'] === 'XML') {
                  $doc->color = 'light-green';
                  $doc->icon = 'fas fa-file-code';
                } else if ($doc_elemento['doc_type'] === 'jpg' || $doc_elemento['doc_type'] === 'jpeg' || $doc_elemento['doc_type'] === 'png' || $doc_elemento['doc_type'] === 'JPG' || $doc_elemento['doc_type'] === 'JPEG' || $doc_elemento['doc_type'] === 'PNG') {
                  $doc->color = 'amber';
                  $doc->icon = 'fas fa-file-image';
                }
                array_push($array_documentos,$doc);
            }
            $fianza->documentos = $array_documentos;
            array_push($nuevo,$fianza);
        }
        $this->content['fianzas'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $fianzas = new Fianzas();
            $fianzas->setTransaction($tx);
            $fianzas->fianza = trim(strtoupper($request['fianza']));
            if($request['fecha_inicio'] === ''){
                $fianzas->fecha_inicio = null;
            } else {
                $fianzas->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
            }
            if($request['fecha_fin'] === ''){
                $fianzas->fecha_fin = null;
            } else {
                $fianzas->fecha_fin = date("Y/m/d", strtotime($request['fecha_fin']));
            }
            $fianzas->monto = floatval($request['monto']);
            $fianzas->contrato = strtoupper($request['contrato']);
            if(intval($request['empresa'] === 0)){
                $fianzas->empresa = null;
            } else {
                $fianzas->empresa = intval($request['empresa']);
            }
            
                if ($fianzas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la fianza.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($fianzas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la fianza.'];
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

            $fianzas = Fianzas::findFirst(intval($request['id']));
                if ($fianzas) {
                    $fianzas->setTransaction($tx);
                    $fianzas->fianza = trim(strtoupper($request['fianza']));
                    if($request['fecha_inicio'] === ''){
                        $fianzas->fecha_inicio = null;
                    } else {
                        $fianzas->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
                    }
                    if($request['fecha_fin'] === ''){
                        $fianzas->fecha_fin = null;
                    } else {
                        $fianzas->fecha_fin = date("Y/m/d", strtotime($request['fecha_fin']));
                    }
                    if($request['fecha_cancelacion'] === ''){
                        $fianzas->fecha_cancelacion = null;
                    } else {
                        $fianzas->fecha_cancelacion = date("Y-m-d H:i:s", strtotime($request['fecha_cancelacion']));
                    }
                    $fianzas->monto = floatval($request['monto']);
                    $fianzas->contrato = strtoupper($request['contrato']);
                    if(intval($request['empresa'] === 0)){
                        $fianzas->empresa = null;
                    } else {
                        $fianzas->empresa = intval($request['empresa']);
                    }

                    if ($fianzas->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la fianza'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($fianzas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la fianza'];
                        $tx->rollback();
                    }
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function deleteFormato() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $documento = SysDocuments::findFirst($request['documento_id']);
                if($documento){
                    $documento->setTransaction($tx);
                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/fianzas/'.$documento->id.'.'.$documento->doc_type;
                    unlink($nombre_fichero);
                    if ($documento->delete()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el documento de cancelación.'];
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

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                //$upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/';
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/fianzas/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx' || strtolower($file->getExtension())==='txt'){
                            
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

                                    $fianzas = new DocumentosFianzas();
                                    $fianzas->setTransaction($tx);
                                    $fianzas->documento_id = $documento->id;
                                    $fianzas->fianza_id = $request['fianza_id'];
                                        if ($fianzas->create()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($fianzas);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la fianza.'];
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
                                if ($documento) {
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/fianzas/'.$documento->id.'.'.$documento->doc_type;
                                    unlink($nombre_fichero);
                                    $documento->doc_type=$file->getExtension();
                                    $documento->name=$img;
                                    if($documento->update()) {
                                        $id = $documento->id;
                                        $nombre_con_id = $id .'.'. $file->getExtension();
                                        $file->moveTo($upload_dir . $nombre_con_id);
                                        if (file_exists($upload_dir . $nombre_con_id)) {
                                            chmod($upload_dir . $nombre_con_id, 0777);
                                        }
                                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                            $path=$upload_dir . $nombre_con_id;
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
                                } else {

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

    public function getFile($id, $ext)
    {    
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/fianzas/' .$documento->id.'.'.$ext;
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

    public function getFileFinal($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = Fianzas::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/fianzas_documentos_finales/' .$documento->id.'.'.$ext;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, "\"".$documento->documento_final."\"");
        $response->send();
        return $response;
    }

    public function deleteFileFinal()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $contratos = Fianzas::findFirst(intval($request['id']));

            if ($contratos) {
                $contratos->setTransaction($tx);
                $contratos->documento_final = null;
                if ($contratos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la fianza.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($contratos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la fianza.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function uploadArchivoFinal()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                //$upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/';
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/fianzas_documentos_finales/';
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
                            
                                $nombre = $request['nombre'];

                                    $contrato = Fianzas::findFirst(intval($request['contrato_id']));
                                    $contrato->setTransaction($tx);
                                    $contrato->documento_final = $nombre;
                                    $contrato->extension = $file->getExtension();

                                    if ($contrato->update()) {
                                        $id = $contrato->id;
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
                                        $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                        $tx->commit();
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($contrato);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
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
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $fianzas = Fianzas::findFirst($request['id']);
                    $fianzas->setTransaction($tx);

                    if ($fianzas->delete()) {
                        $this->content['result'] = 'success';

                        if($fianzas->documento_id !== null) {
                            $id = $fianzas->documento_id;
                            $documentos = SysDocuments::findFirst(intval($id));
                            $documentos->setTransaction($tx);
                            $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/fianzas/'.$documentos->id.'.'.$documentos->doc_type;
                            unlink($nombre_fichero);
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la fianza.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la fianza.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($fianzas);
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