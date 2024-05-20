<?php

use Phalcon\Mvc\Controller;

class PropuestasController extends Controller
{
	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $sql = "SELECT lic_propuestas.id, lic_propuestas.licitacion_id, lic_propuestas.propuesta, lic_propuestas.descripcion, lic_propuestas.fecha, lic_propuestas.ganadora, lic_propuestas.documento, lic_propuestas.responsable, lic_propuestas.respaldo1, lic_propuestas.documento1, lic_propuestas.responsable1, lic_propuestas.respaldo2, lic_propuestas.documento2, lic_propuestas.responsable2, lic_propuestas.respaldo3, lic_propuestas.documento3, lic_propuestas.responsable3, lic_propuestas.respaldo4, lic_propuestas.documento4, lic_propuestas.responsable4, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento) as name0, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento1) as name1, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento2) as name2, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento3) as name3, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento4) as name4, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable) as responsable_name, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable1) as responsable_name1, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable2) as responsable_name2, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable3) as responsable_name3, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable4) as responsable_name4      from lic_propuestas";

        $this->content['propuestas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
	
	public function getPropuestasByLicitacion ($licitacion_id)
    {
        $sql = "SELECT lic_propuestas.id, lic_propuestas.licitacion_id, lic_propuestas.propuesta, lic_propuestas.descripcion, lic_propuestas.fecha, lic_propuestas.ganadora, lic_propuestas.documento, lic_propuestas.responsable, lic_propuestas.respaldo1, lic_propuestas.documento1, lic_propuestas.responsable1, lic_propuestas.respaldo2, lic_propuestas.documento2, lic_propuestas.responsable2, lic_propuestas.respaldo3, lic_propuestas.documento3, lic_propuestas.responsable3, lic_propuestas.respaldo4, lic_propuestas.documento4, lic_propuestas.responsable4, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento) as name0, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_propuestas.documento) as doc_type0, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento1) as name1, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_propuestas.documento1) as doc_type1, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento2) as name2, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_propuestas.documento2) as doc_type2, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento3) as name3, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_propuestas.documento3) as doc_type3, (select sys_documents.name from sys_documents where sys_documents.id = lic_propuestas.documento4) as name4, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_propuestas.documento4) as doc_type4, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable) as responsable_name, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable1) as responsable_name1, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable2) as responsable_name2, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable3) as responsable_name3, (select sys_users.nickname from sys_users where sys_users.id = lic_propuestas.responsable4) as responsable_name4		from lic_propuestas
		    where licitacion_id = $licitacion_id order by lic_propuestas.id";

        $this->content['propuestas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $propuestas = Propuestas::findFirst($request['id']);
                    $propuestas->setTransaction($tx);

                    $documento1 = $propuestas->documento1;
                    $documento2 = $propuestas->documento2;
                    $documento3 = $propuestas->documento3;
                    $documento4 = $propuestas->documento4;
                    $documento = $propuestas->documento;
                    if ($propuestas->delete()) {

                        if($documento1 !== null) {
                            $documentos = SysDocuments::findFirst(intval($documento1));
                            $documentos->setTransaction($tx);
                            if($documentos->name !== null){
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documentos->id.'.'.$documentos->doc_type;
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
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documentos->id.'.'.$documentos->doc_type;
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
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documentos->id.'.'.$documentos->doc_type;
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
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documentos->id.'.'.$documentos->doc_type;
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
                                $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documentos->id.'.'.$documentos->doc_type;
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
                        $this->content['error'] = Helpers::getErrors($propuestas);
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

        $propuestas = new Propuestas();
        $propuestas->setTransaction($tx);
        $propuestas->licitacion_id = intval($request['licitacion_id']);
        if(trim($request['propuesta'] === '')){
            $propuestas->propuesta = null;
        } else {
            $propuestas->propuesta = trim($request['propuesta']);
        }
        if(trim($request['descripcion'] === '')){
            $propuestas->descripcion = null;
        } else {
            $propuestas->descripcion = trim($request['descripcion']);
        }

        if($request['fecha'] === ''){
            $propuestas->fecha = null;
        } else {
            $propuestas->fecha = date("Y/m/d", strtotime($request['fecha']));
        }

        if(intval($request['ganadora']) === 0){
            $propuestas->ganadora = null;
        } else {
            $propuestas->ganadora = intval($request['ganadora']);
        }

        if(intval($request['respaldo1']) === 0){
            $propuestas->respaldo1 = null;
        } else {
            $propuestas->respaldo1 = intval($request['respaldo1']);
        }

        if(intval($request['respaldo2']) === 0){
            $propuestas->respaldo2 = null;
        } else {
            $propuestas->respaldo2 = intval($request['respaldo2']);
        }

        if(intval($request['respaldo3']) === 0){
            $propuestas->respaldo3 = null;
        } else {
            $propuestas->respaldo3 = intval($request['respaldo3']);
        }

        if(intval($request['respaldo4']) === 0){
            $propuestas->respaldo4 = null;
        } else {
            $propuestas->respaldo4 = intval($request['respaldo4']);
        }

        if ($propuestas->create()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha creado la propuesta.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($propuestas);
            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update () {
        $request = $this->request->getPut();
        $tx = $this->transactions->get();

        $propuestas = Propuestas::findFirst(intval($request['id']));
        $propuestas->setTransaction($tx);
        if(trim($request['propuesta'] === '')){
            $propuestas->propuesta = null;
        } else {
            $propuestas->propuesta = trim($request['propuesta']);
        }
        if(trim($request['descripcion'] === '')){
            $propuestas->descripcion = null;
        } else {
            $propuestas->descripcion = trim($request['descripcion']);
        }

        if($request['fecha'] === ''){
            $propuestas->fecha = null;
        } else {
            $propuestas->fecha = date("Y/m/d", strtotime($request['fecha']));
        }

        if($request['responsable'] === null || intval($request['responsable']) === 0){
            $propuestas->responsable = null;
        } else {
            $propuestas->responsable = intval($request['responsable']);
        }
        if($request['responsable1'] === null || intval($request['responsable1']) === 0){
            $propuestas->responsable1 = null;
        } else {
            $propuestas->responsable1 = intval($request['responsable1']);
        }
        if($request['responsable2'] === null || intval($request['responsable2']) === 0){
            $propuestas->responsable2 = null;
        } else {
            $propuestas->responsable2 = intval($request['responsable2']);
        }
        if($request['responsable3'] === null || intval($request['responsable3']) === 0){
            $propuestas->responsable3 = null;
        } else {
            $propuestas->responsable3 = intval($request['responsable3']);
        }
        if($request['responsable4'] === null || intval($request['responsable4']) === 0){
            $propuestas->responsable4 = null;
        } else {
            $propuestas->responsable4 = intval($request['responsable4']);
        }

        if ($propuestas->update()) {
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha actualizado el requisito.'];
            $tx->commit();
        } else {
            $this->content['error'] = Helpers::getErrors($propuestas);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el requisito.'];
            $tx->rollback();
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/' .$documento->id.'.'.$documento->doc_type;
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

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($this->request->hasFiles()) {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/';
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

                                    $propuestas = Propuestas::findFirst(intval($request['id']));
                                        $propuestas->setTransaction($tx);
                                        if($request['bandera_ganadora'] === 'true'){
                                            $propuestas->documento = $id;
                                        }
                                        if($request['bandera_documento1'] === 'true'){
                                            $propuestas->documento1 = $id;
                                        }
                                        if($request['bandera_documento2'] === 'true'){
                                            $propuestas->documento2 = $id;
                                        }
                                        if($request['bandera_documento3'] === 'true'){
                                            $propuestas->documento3 = $id;
                                        }
                                        if($request['bandera_documento4'] === 'true'){
                                            $propuestas->documento4 = $id;
                                        }
                                        if ($propuestas->update()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($propuestas);
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
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/propuestas/'.$documento->id.'.'.$tipo;
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