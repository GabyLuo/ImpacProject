<?php

use Phalcon\Mvc\Controller;

class RequerimientosController extends Controller
{
	public $content = ['result' => 'error', 'message' => ''];
	
	public function getBasesByLicitacion ($licitacion_id)
    {
        $sql = "SELECT lic_requerimientos.id,  lic_requerimientos.licitacion_id,lic_requerimientos.documento_id, lic_requerimientos.descripcion, lic_requerimientos.nombre_referencia as nombre_documento, (select sys_documents.name from sys_documents where sys_documents.id = lic_requerimientos.documento_id) as name, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requerimientos.documento_id) as doc_type
		from lic_requerimientos
		where licitacion_id = $licitacion_id order by lic_requerimientos.id";

        $this->content['bases'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAll ()
    {
        $sql = "SELECT * from lic_requerimientos";

        $this->content['bases'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function deleteFormato() {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $requerimientos = Requerimientos::findFirst($request['id']);
                    $requerimientos->setTransaction($tx);

                    $id = $requerimientos->documento_id;
                    if ($requerimientos->delete()) {

                        if($id !== null) {
                            $documentos = SysDocuments::findFirst($request['documento_id']);
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                //$nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/'.$documentos->name;
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/bases/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }

                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el formato.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el formato.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($requerimientos);
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

    public function guardarArchivo () {

        $request = $this->request->getPost();
        $tx = $this->transactions->get();

        $requerimientos = new Requerimientos();
        $requerimientos->setTransaction($tx);
        $requerimientos->licitacion_id = intval($request['licitacion_id']);
        if($request['observaciones_archivo'] === ''){
            $requerimientos->descripcion = null;
        } else {
            $requerimientos->descripcion = $request['observaciones_archivo'];
        }

        if($request['nombre_documento'] === ''){
            $requerimientos->nombre_referencia = null;
        } else {
            $requerimientos->nombre_referencia = $request['nombre_documento'];
        }
        
        if(intval($request['documento_id2'] === 0) || $request['documento_id2'] === '0'){
            $requerimientos->documento_id = null;
        } else {
            $requerimientos->documento_id = intval($request['documento_id2']);
        }

        if ($requerimientos->create()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el formato.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($requerimientos);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el formato al registro de la licitacion.'];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update () {

        $request = $this->request->getPut();
        $tx = $this->transactions->get();

        $requerimiento = Requerimientos::findFirst(intval($request['id']));
        $requerimiento->setTransaction($tx);
        if($request['observaciones_archivo'] === ''){
            $requerimiento->descripcion = null;
        } else {
            $requerimiento->descripcion = $request['observaciones_archivo'];
        }

        if($request['nombre_documento'] === ''){
            $requerimiento->nombre_referencia = null;
        } else {
            $requerimiento->nombre_referencia = $request['nombre_documento'];
        }

        if ($requerimiento->update()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha actualizado el formato.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($requerimiento);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el formato.'];
            $tx->rollback();
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
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/bases/';
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

                                    $requerimiento = Requerimientos::findFirst(intval($request['id']));
                                    //if($requerimientos){
                                        $requerimiento->setTransaction($tx);
                                        $requerimiento->documento_id = $id;
                                        if ($requerimiento->update()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($requerimiento);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                        $tx->rollback();
                                        }
                                    /*} else {
                                        $this->content['error'] = Helpers::getErrors($requerimientos);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la solicitud.'];
                                        $tx->rollback();
                                    }*/
                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }

                            } else {
                                // foreach (glob($upload_dir.$request['nombre_antiguo'].'.*') as $nombre_fichero) {
                                   // unlink($nombre_fichero);
                                // }
                                $documento = SysDocuments::findFirst(intval($request['formato_requisito_id']));
                                $documento->setTransaction($tx);
                                $tipo = $documento->doc_type;
                                $documento->doc_type=$file->getExtension();
                                $documento->name=$img;                    
            
                                if($documento->update()){
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/bases/'.$documento->id.'.'.$tipo;
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
}