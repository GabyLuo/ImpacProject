<?php

use Phalcon\Mvc\Controller;

class RequisitosLController extends Controller
{
	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT lic_requisitos.id, lic_requisitos.licitacion_id, lic_requisitos.requisito, lic_requisitos.descripcion, lic_requisitos.fecha, lic_requisitos.ganadora, lic_requisitos.documento, lic_requisitos.responsable, lic_requisitos.respaldo1, lic_requisitos.documento1, lic_requisitos.responsable1, lic_requisitos.respaldo2, lic_requisitos.documento2, lic_requisitos.responsable2, lic_requisitos.respaldo3, lic_requisitos.documento3, lic_requisitos.responsable3, lic_requisitos.respaldo4, lic_requisitos.documento4, lic_requisitos.responsable4, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento) as name0, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento1) as name1, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento2) as name2, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento3) as name3, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento4) as name4, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable) as responsable_name, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable1) as responsable_name1, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable2) as responsable_name2, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable3) as responsable_name3, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable4) as responsable_name4      from lic_requisitos";

        $this->content['requisitos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
	
	public function getRequisitosByLicitacion ($licitacion_id)
    {
        $sql = "SELECT lic_requisitos.id, lic_requisitos.licitacion_id, lic_requisitos.requisito, lic_requisitos.descripcion, lic_requisitos.fecha, lic_requisitos.ganadora, lic_requisitos.documento, lic_requisitos.responsable, lic_requisitos.respaldo1, lic_requisitos.documento1, lic_requisitos.responsable1, lic_requisitos.respaldo2, lic_requisitos.documento2, lic_requisitos.responsable2, lic_requisitos.respaldo3, lic_requisitos.documento3, lic_requisitos.responsable3, lic_requisitos.respaldo4, lic_requisitos.documento4, lic_requisitos.responsable4, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento) as name0, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requisitos.documento) as doc_type0, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento1) as name1, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requisitos.documento1) as doc_type1, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento2) as name2, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requisitos.documento2) as doc_type2, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento3) as name3, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requisitos.documento3) as doc_type3, (select sys_documents.name from sys_documents where sys_documents.id = lic_requisitos.documento4) as name4, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requisitos.documento4) as doc_type4, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable) as responsable_name, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable1) as responsable_name1, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable2) as responsable_name2, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable3) as responsable_name3, (select sys_users.nickname from sys_users where sys_users.id = lic_requisitos.responsable4) as responsable_name4		from lic_requisitos
		    where licitacion_id = $licitacion_id order by lic_requisitos.id";

        $this->content['requisitos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/' .$documento->id.'.'.$documento->doc_type;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        // $response->setFileToSend($path, $documento->name);
        $response->setFileToSend($path, "\"".$documento->name."\"");
        $response->send();
        return $response;
    }

    public function delete() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $requisitos = RequisitosL::findFirst($request['id']);
                    $requisitos->setTransaction($tx);

                    $documento1 = $requisitos->documento1;
                    $documento2 = $requisitos->documento2;
                    $documento3 = $requisitos->documento3;
                    $documento4 = $requisitos->documento4;
                    $documento = $requisitos->documento;
                    if ($requisitos->delete()) {

                        if($documento1 !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento1));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if($documento2 !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento2));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if($documento3 !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento3));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if($documento4 !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento4));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if($documento !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documentos->id.'.'.$documentos->doc_type;
                                unlink($nombre_fichero);
                            }
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                        if ($this->content['result'] === 'success') {
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el requisito.'];
                            $tx->commit();
                        } else {}
                    } else {
                        $this->content['error'] = Helpers::getErrors($requisitos);
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

        $requisitos = new RequisitosL();
        $requisitos->setTransaction($tx);
        $requisitos->licitacion_id = intval($request['licitacion_id']);
        if(trim($request['requisito'] === '')){
            $requisitos->requisito = null;
        } else {
            $requisitos->requisito = trim($request['requisito']);
        }
        if(trim($request['descripcion'] === '')){
            $requisitos->descripcion = null;
        } else {
            $requisitos->descripcion = trim($request['descripcion']);
        }

        if($request['fecha'] === ''){
            $requisitos->fecha = null;
        } else {
            $requisitos->fecha = date("Y/m/d", strtotime($request['fecha']));
        }

        if(intval($request['ganadora']) === 0){
            $requisitos->ganadora = null;
        } else {
            $requisitos->ganadora = intval($request['ganadora']);
        }

        if(intval($request['respaldo1']) === 0){
            $requisitos->respaldo1 = null;
        } else {
            $requisitos->respaldo1 = intval($request['respaldo1']);
        }

        if(intval($request['respaldo2']) === 0){
            $requisitos->respaldo2 = null;
        } else {
            $requisitos->respaldo2 = intval($request['respaldo2']);
        }

        if(intval($request['respaldo3']) === 0){
            $requisitos->respaldo3 = null;
        } else {
            $requisitos->respaldo3 = intval($request['respaldo3']);
        }

        if(intval($request['respaldo4']) === 0){
            $requisitos->respaldo4 = null;
        } else {
            $requisitos->respaldo4 = intval($request['respaldo4']);
        }

        if ($requisitos->create()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha creado el requisito.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($requisitos);
            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update () {
        $request = $this->request->getPut();
        $tx = $this->transactions->get();

        $requisitos = RequisitosL::findFirst(intval($request['id']));
        $requisitos->setTransaction($tx);
        if(trim($request['requisito'] === '')){
            $requisitos->requisito = null;
        } else {
            $requisitos->requisito = trim($request['requisito']);
        }
        if(trim($request['descripcion'] === '')){
            $requisitos->descripcion = null;
        } else {
            $requisitos->descripcion = trim($request['descripcion']);
        }

        if($request['fecha'] === ''){
            $requisitos->fecha = null;
        } else {
            $requisitos->fecha = date("Y/m/d", strtotime($request['fecha']));
        }

        if($request['responsable'] === null || intval($request['responsable']) === 0){
            $requisitos->responsable = null;
        } else {
            $requisitos->responsable = intval($request['responsable']);
        }
        if($request['responsable1'] === null || intval($request['responsable1']) === 0){
            $requisitos->responsable1 = null;
        } else {
            $requisitos->responsable1 = intval($request['responsable1']);
        }
        if($request['responsable2'] === null || intval($request['responsable2']) === 0){
            $requisitos->responsable2 = null;
        } else {
            $requisitos->responsable2 = intval($request['responsable2']);
        }
        if($request['responsable3'] === null || intval($request['responsable3']) === 0){
            $requisitos->responsable3 = null;
        } else {
            $requisitos->responsable3 = intval($request['responsable3']);
        }
        if($request['responsable4'] === null || intval($request['responsable4']) === 0){
            $requisitos->responsable4 = null;
        } else {
            $requisitos->responsable4 = intval($request['responsable4']);
        }

        if ($requisitos->update()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha actualizado el requisito.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($requisitos);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el requisito.'];
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
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>500000000||$sizeA===0){
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

                                    $requisitos = RequisitosL::findFirst(intval($request['id']));
                                        $requisitos->setTransaction($tx);
                                        if($request['bandera_ganadora'] === 'true'){
                                            $requisitos->documento = $id;
                                        }
                                        if($request['bandera_documento1'] === 'true'){
                                            $requisitos->documento1 = $id;
                                        }
                                        if($request['bandera_documento2'] === 'true'){
                                            $requisitos->documento2 = $id;
                                        }
                                        if($request['bandera_documento3'] === 'true'){
                                            $requisitos->documento3 = $id;
                                        }
                                        if($request['bandera_documento4'] === 'true'){
                                            $requisitos->documento4 = $id;
                                        }
                                        if ($requisitos->update()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($requisitos);
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
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/requisitos/'.$documento->id.'.'.$tipo;
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