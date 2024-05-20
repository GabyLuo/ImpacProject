<?php

use Phalcon\Mvc\Controller;

class FoldersController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $validUser = Auth::getUserData($this->config);
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $where = "";
        if(strtoupper($role)!==strtoupper('admin') && strtoupper($role)!==strtoupper('finanzas') && strtoupper($role)!==strtoupper('finanzas/comisiones')) {
            $where = "where privado = false";
        } else {
            // $where = "where (privado = true or nivel = 1)";
        }
        $sql = "SELECT id, nivel, nombre, padre, ruta, privado
            FROM sys_folders {$where} order by id";
        $sql_archivos = "SELECT sys_files.id, sys_files.folder_id, sys_files.documento_id, sys_files.ruta, sys_documents.doc_type, sys_documents.name, sys_documents.name as label
            from sys_files 
            inner join sys_documents
            on sys_files.documento_id = sys_documents.id
            order by sys_files.id";

        $items = $this->db->query($sql)->fetchAll();
        $files = $this->db->query($sql_archivos)->fetchAll();
        $array_f = [];
        foreach ($files as $f) {
            $array_files['id'] = $f['id'].'file';
            $array_files['id_file'] = $f['id'];
            $array_files['folder_id'] = $f['folder_id'];
            $array_files['documento_id'] = $f['documento_id'];
            $array_files['ruta'] = $f['ruta'];
            $array_files['doc_type'] = $f['doc_type'];
            $array_files['name'] = $f['name'];
            $array_files['label'] = $f['label'];
            $array_files['tipo'] = 'file';
            if($f['doc_type'] === 'pdf') {
                $array_files['icon'] = 'fas fa-file-pdf';
                $array_files['color'] = 'red-14';
            }
            if($f['doc_type'] === 'docx') {
                $array_files['icon'] = 'fas fa-file-word';
                $array_files['color'] = 'blue-9';
            }
            if($f['doc_type'] === 'xlsx') {
                $array_files['icon'] = 'fas fa-file-excel';
                $array_files['color'] = 'green';
            }
            if($f['doc_type'] === 'jpg' || $f['doc_type'] === 'jpeg' || $f['doc_type'] === 'png') {
                $array_files['icon'] = 'fas fa-file-image';
                $array_files['color'] = 'amber';
            }
            $array_files['header'] = 'generic';
            array_push($array_f,$array_files);
        }
        $files = $array_f;

        $childs = array();
        foreach($items as &$item) $childs[$item['padre']][] = &$item;
        unset($item);

        foreach ($files as &$file) $childs[$file['folder_id']][] = &$file;
        unset($file);

        foreach($items as &$item) {
            $item['label'] = $item['nombre'];
            $item['icon'] = 'folder';
            $item['header'] = 'generic'; 
            $item['color'] = 'blue-8';
            $item['tipo'] = 'folder';
            if (isset($childs[$item['id']])) {
                $item['children'] = $childs[$item['id']];
                $item['has_children'] = 'true';
            } else {
                $item['has_children'] = 'false';
            }
        }
        unset($item);
        $tree = $childs[null];
        $tree[0]['header'] = 'root';

        $this->content['folders'] = $tree;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPadre ($padre)
    {
        $sql = "SELECT id, nivel, nombre, padre
            FROM sys_folders where padre = $padre";
        $this->content['folders'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $folder = Folders::findFirst(
                [
                    'nombre = :nombre: AND nivel = :nivel: AND padre = :padre:',
                    'bind' => [
                        'nombre' => trim($request['nombre']),
                        'nivel' => intval($request['nivel'])+1,
                        'padre' => intval($request['id'])
                    ]
                ]
            );
            if ($folder) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esta carpeta en este nivel'];
            } else {
                if ($request['ruta']==='/'){
                    $ruta_directorio = $request['ruta'].$request['nombre'];
                } else {
                    $ruta_directorio = $request['ruta'].'/'.$request['nombre'];
                }
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$ruta_directorio.'/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                $folder = new Folders();
                $folder->setTransaction($tx);
                $folder->nombre = trim($request['nombre']);
                $folder->nivel = intval($request['nivel']) + 1;
                $folder->padre = intval($request['id']);
                $folder->ruta = $ruta_directorio;
                $folder->privado = $request['privado'];
                if ($folder->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la carpeta.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($folder);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la carpeta.'];
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
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $folder = Folders::findFirst(
                [
                    'nombre = :nombre: AND nivel = :nivel: AND padre = :padre:',
                    'bind' => [
                        'nombre' => trim($request['nombre']),
                        'nivel' => intval($request['nivel']),
                        'padre' => intval($request['padre'])
                    ]
                ]
            );
            if ($folder) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una carpeta con ese nombre en este nivel'];
            } else {
                $folder = Folders::findFirst($request['id']);
                if ($folder) {
                    $folder->setTransaction($tx);
                    $folder->nombre = trim($request['nombre']);

                    if ($folder->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el nombre de la carpeta'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($folder);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el nombre de la carpeta'];
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

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                //$upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/';
                $upload_dir = '';
                $ruta = '';
                if($request['ruta']==='/'){
                    $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio/';
                    $ruta = '/';
                } else {
                    $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$request['ruta'].'/';
                    $ruta = $request['ruta'].'/';
                }
                
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx' || strtolower($file->getExtension())==='xlsx'){
                            
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

                                    $files = new Files();
                                    $files->setTransaction($tx);
                                    $files->documento_id = $documento->id;
                                    $files->folder_id = intval($request['folder_id']);
                                    $files->ruta = $ruta;
                                        if ($files->create()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($files);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo a la carpeta.'];
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
                                    $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio/'.$documento->id.'.'.$documento->doc_type;
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
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xlsx .xml y .pdf'];
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

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $id = $request['id'];
                $this->content['result'] = 'success';
                $folder_principal = Folders::findFirst($id);
                $r = $folder_principal->ruta;

                $sql_archivos = "SELECT sys_files.id, sys_files.documento_id from sys_files where ruta like '$r%' order by id desc";
                $archivos = $this->db->query($sql_archivos)->fetchAll();
                if(sizeof($archivos)>0){
                    foreach ($archivos as $archivo) {
                        $file = Files::findFirst($archivo['id']);
                        if($file){
                            $file->setTransaction($tx);
                            $ruta_file = $file->ruta;
                            if ($file->delete()) {
                                $documento = SysDocuments::findFirst($archivo['documento_id']);
                                if($documento) {
                                    $documento->setTransaction($tx);
                                    $nombre_documento = $documento->id.'.'.$documento->doc_type;
                                    if($documento->delete()) {
                                        $nombre_file = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$ruta_file.$nombre_documento;
                                        unlink($nombre_file);
                                        $this->content['result'] = 'success';
                                        //$tx->commit();
                                    } else {
                                        $this->content['result'] = 'error';
                                    }
                                } else {
                                    $this->content['result'] = 'error';
                                }
                            } else {
                                $this->content['result'] = 'error';
                            }
                        } else {
                            $this->content['result'] = 'error';
                        }
                    }
                }

                $sql_folders = "SELECT id FROM sys_folders where ruta like '$r%' order by id desc";
                $carpetas = $this->db->query($sql_folders)->fetchAll();
                //$tx = $this->transactions->get();
                if(sizeof($carpetas)>0){
                    foreach ($carpetas as $carpeta) {
                        //print_r($carpeta['id']);
                        $folder = Folders::findFirst($carpeta['id']);
                        if($folder) {
                            $folder->setTransaction($tx);
                            $ruta_folder = $folder->ruta;
                            if ($folder->delete()) {
                                $nombre_carpeta = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$ruta_folder;
                                rmdir($nombre_carpeta);
                                $this->content['result'] = 'success';
                                //$tx->commit();
                            } else {
                                $this->content['result'] = 'error';
                            }
                        } else {
                            $this->content['result'] = 'error';
                        }
                    }
                }
                //$tx = $this->transactions->get();
                if($this->content['result'] === 'success'){
                    //$folder_principal = Folders::findFirst($id);
                    //if($folder_principal){
                        //$folder_principal->setTransaction($tx);
                        //$ruta = $folder_principal->ruta;
                        //if ($folder_principal->delete()) {
                          //  $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$ruta;
                          //  rmdir ($nombre_fichero);
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la carpeta.'];
                            $tx->commit();
                        //} else {
                          //  $this->content['result'] = 'error';
                           // $this->content['message'] = ['title' => '¡Error!', 'content' => 'La carpeta no puede eliminarse'];
                            //$tx->rollback();
                        //}
                    /*} else {
                        $this->content['result'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'La carpeta no puede eliminarse o no está disponible en este momentooooooooooo'];
                        $tx->rollback();
                    }*/
                } else {
                    $this->content['result'] = 'error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'La carpeta no puede eliminarse o no está disponible'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function deleteFile ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $file = Files::findFirst($request['id']);
                    $file->setTransaction($tx);

                    if ($file->delete()) {
                        $documento_id = $file->documento_id;
                        $ruta = $file->ruta;
                        $documento = SysDocuments::findFirst($documento_id);
                        $documento->setTransaction($tx);
                        $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$ruta.$documento->id.'.'.$documento->doc_type;
                        unlink($nombre_fichero);
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
                        $this->content['error'] = Helpers::getErrors($file);
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

    public function getFile($id_file, $id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $file = Files::findFirst($id_file);
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/repositorio'.$file->ruta.$documento->id.'.'.$ext;
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
}