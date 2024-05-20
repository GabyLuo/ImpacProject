<?php

use Phalcon\Mvc\Controller;

class ProveedorController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_proveedor.id, vnt_proveedor.empresa_id, vnt_proveedor.folio, vnt_proveedor.fecha_registro, vnt_proveedor.fecha_renovacion, vnt_proveedor.estado_id, vnt_proveedor.municipio_id, vnt_proveedor.status, vnt_proveedor.institucion, vnt_proveedor.observaciones_proceso, vnt_proveedor.observaciones_negada, vnt_proveedor.observaciones_concluida, vnt_empresa.razon_social as empresa, vnt_proveedor.formato_requisito_id, sys_documents.name as nombre_documento, vnt_estado.nombre as estado, case when vnt_proveedor.municipio_id = 0 then 'ESTATAL' else (select vnt_municipio.nombre from vnt_municipio where vnt_municipio.id = vnt_proveedor.municipio_id) end as municipio
                from vnt_proveedor
                join vnt_empresa on vnt_proveedor.empresa_id = vnt_empresa.id
                join vnt_estado on vnt_proveedor.estado_id = vnt_estado.id
                left join sys_documents on sys_documents.id = vnt_proveedor.formato_requisito_id
                order by vnt_proveedor.id";
        
        $this->content['proveedores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFormato ($proveedor_id)
    {
        $sql = "SELECT vnt_proveedor.formato_requisito_id,sys_documents.name as nombre_documento
        from vnt_proveedor
        left join sys_documents on sys_documents.id = vnt_proveedor.formato_requisito_id
        where vnt_proveedor.id=$proveedor_id";
        
        $this->content['formato'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByEmpresa ($empresa_id)
    {
        $sql = "SELECT vnt_proveedor.cliente_id, vnt_clientes.razon_social as cliente,
                vnt_proveedor.folio,vnt_estado.nombre as estado, vnt_municipio.nombre as municipio
                from vnt_proveedor join vnt_clientes on vnt_proveedor.cliente_id = vnt_clientes.id
                left join vnt_estado on vnt_estado.id = vnt_clientes.estado_id
                left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id
                where vnt_proveedor.empresa_id = $empresa_id
                order by vnt_clientes.razon_social";
        
        $clientes = $this->db->query($sql)->fetchAll();

        $contenido = new stdClass();
        $contenido->label ='Clientes';
        $contenido->avatar = 'statics/cliente_avatar.png';

        $children = array();

        foreach ($clientes as $cliente){
            $client=(object)array('label'=>$cliente['cliente']);
                $children_children = array();
                $descripcion = (object)array('label' => 'Folio del proveedor: '.$cliente['folio']);
                array_push($children_children,$descripcion);
                $descripcion = (object)array('label' => 'Estado: '.$cliente['estado']);
                array_push($children_children,$descripcion);
            $client->children = $children_children;
            array_push($children,$client);
        }

        $contenido->children = $children;

        $resultado = array();
        array_push($resultado,$contenido);

        $this->content['resultado'] = $resultado;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByCliente ($cliente_id)
    {
        $sql = "SELECT vnt_proveedor.folio,vnt_proveedor.empresa_id,vnt_empresa.razon_social as empresa,vnt_empresa.rfc,vnt_empresa.rep_legal
		from vnt_proveedor join vnt_empresa on vnt_proveedor.empresa_id = vnt_empresa.id
		where vnt_proveedor.cliente_id = $cliente_id
		order by vnt_empresa.razon_social";
        
        $empresas = $this->db->query($sql)->fetchAll();

        $contenido = new stdClass();
        $contenido->label ='Empresas';
        $contenido->avatar = 'statics/empresa_avatar.png';

        $children = array();

        foreach ($empresas as $empresa){
            $emp=(object)array('label'=>$empresa['empresa']);
                $children_children = array();
                $descripcion = (object)array('label' => 'Folio del proveedor: '.$empresa['folio']);
                array_push($children_children,$descripcion);
                if($empresa['rfc']!=="") {
                    $descripcion = (object)array('label' => 'RFC: '.$empresa['rfc']);
                    array_push($children_children,$descripcion);
                }
                if($empresa['rep_legal']!=='') {
                    $descripcion = (object)array('label' => 'Representante legal: '.$empresa['rep_legal']);
                    array_push($children_children,$descripcion);
                }
            $emp->children = $children_children;
            array_push($children,$emp);
        }

        $contenido->children = $children;

        $resultado = array();
        array_push($resultado,$contenido);

        $this->content['resultado'] = $resultado;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $proveedores = Proveedor::findFirst(
                [
                    'folio = :folio: AND empresa_id = :empresa_id: AND estado_id = :estado_id: AND municipio_id = :municipio_id: AND fecha_registro = :fecha_registro:',
                    'bind' => [
                        'folio' => trim($request['folio']),
                        'empresa_id' => intval($request['empresa_id']),
                        'estado_id' => intval($request['estado_id']),
                        'municipio_id' => intval($request['empresa_id']),
                        'fecha_registro' => date("Y/m/d", strtotime($request['fecha_registro']))
                    ]
                ]
            );
            if ($proveedores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este proveedor con estos datos'];
            } else {
                $proveedores = new Proveedor();
                $proveedores->setTransaction($tx);
                $proveedores->empresa_id = intval($request['empresa_id']);
                $proveedores->folio = strtoupper($request['folio']);
                $proveedores->fecha_registro = date("Y/m/d", strtotime($request['fecha_registro']));
                if($request['fecha_renovacion'] === ''){
                    $proveedores->fecha_renovacion = null;
                } else {
                    $proveedores->fecha_renovacion = date("Y/m/d", strtotime($request['fecha_renovacion']));
                }
                $proveedores->estado_id = intval($request['estado_id']);
                $proveedores->municipio_id = intval($request['municipio_id']);
                $proveedores->status = $request['status'];
                $proveedores->institucion = trim($request['institucion']);
                $proveedores->observaciones_proceso = trim($request['observaciones_proceso']);
                $proveedores->observaciones_negada = trim($request['observaciones_negada']);
                $proveedores->observaciones_concluida = trim($request['observaciones_concluida']);

                if ($proveedores->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el proveedor.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proveedores);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el proveedor.'];
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

            $proveedores = Proveedor::findFirst(
                [
                    'id != :id: AND folio = :folio: AND empresa_id = :empresa_id: AND estado_id = :estado_id: AND municipio_id = :municipio_id: AND fecha_registro = :fecha_registro:',
                    'bind' => [
                        'folio' => trim($request['folio']),
                        'empresa_id' => intval($request['empresa_id']),
                        'id' => intval($request['id']),
                        'estado_id' => intval($request['estado_id']),
                        'municipio_id' => intval($request['empresa_id']),
                        'fecha_registro' => date("Y/m/d", strtotime($request['fecha_registro']))
                    ]
                ]
            );
            if ($proveedores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este proveedor con estos datos'];
            } else {
                $proveedores = Proveedor::findFirst($request['id']);
                if ($proveedores) {
                    $proveedores->setTransaction($tx);
                    $proveedores->empresa_id = intval($request['empresa_id']);
                    $proveedores->folio = strtoupper($request['folio']);
                    $proveedores->fecha_registro = date("Y/m/d", strtotime($request['fecha_registro']));
                    if($request['fecha_renovacion'] === ""){
                        $proveedores->fecha_renovacion = null;
                    } else {
                        $proveedores->fecha_renovacion = date("Y/m/d", strtotime($request['fecha_renovacion']));
                    }
                    $proveedores->estado_id = intval($request['estado_id']);
                    $proveedores->municipio_id = intval($request['municipio_id']);
                    $proveedores->status = $request['status'];
                    $proveedores->institucion = trim($request['institucion']);
                    $proveedores->observaciones_proceso = trim($request['observaciones_proceso']);
                    $proveedores->observaciones_negada = trim($request['observaciones_negada']);
                    $proveedores->observaciones_concluida = trim($request['observaciones_concluida']);
                    if ($proveedores->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el proveedor.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($proveedores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el proveedor.'];
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

    public function deleteFormato() {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $proveedores = Proveedor::findFirst($request['id']);
                    $proveedores->setTransaction($tx);

                    $proveedores->formato_requisito_id = null;

                    if ($proveedores->update()) {
                        $documentos = SysDocuments::findFirst($request['formato_requisito_id']);
                        $documentos->setTransaction($tx);

                        if ($documentos->delete()) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la constancia del proveedor.'];
                            $tx->commit();
                        } else {
                            $this->content['error'] = Helpers::getErrors($documentos);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }

                    } else {
                        $this->content['error'] = Helpers::getErrors($proveedores);
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

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $proveedores = Proveedor::findFirst($request['id']);
                    $proveedores->setTransaction($tx);

                    if ($proveedores->delete()) {
                        if(intval($proveedores->formato_requisito_id) > 0){
                            $documento = SysDocuments::findFirst($proveedores->formato_requisito_id);
                            $documento->setTransaction($tx);
                            if ($documento->delete()) {
                                $this->content['result'] = 'success';
                            }
                        } else {
                            $this->content['result'] = 'success';
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($proveedores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el proveedor.'];
                    $tx->commit();
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
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }


                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf'|| strtolower($file->getExtension())==='docx'){
                            
                            $img = 'constancia'.'_'.$request['proveedor_id']. '.' . strtolower($file->getExtension());
                            $nombre = 'constancia'.'_'.$request['proveedor_id'];

                            if(intval($request['formato_requisito_id']) === 0){
                                foreach (glob($upload_dir.$nombre.'.*') as $nombre_fichero) {
                                    unlink($nombre_fichero);
                                }
                                $file->moveTo($upload_dir . $img);
                                if (file_exists($upload_dir . $img)) {
                                    chmod($upload_dir . $img, 0777);
                                }
                                if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                    $path=$upload_dir . $img;
                                    $this->orientation($path);
                                }

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                $documento->doc_type = $file->getExtension();
                                $documento->name=$img;

                                if ($documento->create()) {
                                    $proveedor = Proveedor::findFirst($request['proveedor_id']);
                                    $proveedor->setTransaction($tx);
                                    $proveedor->formato_requisito_id=$documento->id;

                                    if ($proveedor->update()) {
                                        $content['result'] = 'success';
                                        $this->content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                        $tx->commit();
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($proveedor);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro del proveedor.'];
                                        $tx->rollback();
                                    }

                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }

                            } else {
                                foreach (glob($upload_dir.$nombre.'.*') as $nombre_fichero) {
                                    unlink($nombre_fichero);
                                }
                                $file->moveTo($upload_dir . $img);
                                if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                    $path=$upload_dir . $img;
                                    $this->orientation($path);
                                }

                                
                                $documento = SysDocuments::findFirst($request['formato_requisito_id']);
                                $documento->setTransaction($tx);
                                $documento->doc_type=$file->getExtension();
                                $documento->name=$img;
                                if($documento->update()){
                                    $content['result'] = 'success';
                                    if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                        $path=$upload_dir . $img;
                                        $this->orientation($path);
                                    }
                                    $tx->commit();
                                }else{
                                    $content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el archivo.'];
                                    $tx->rollback();
                                }
                                

                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png y .pdf'];
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

    public function save_requisito () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $lista = Requisitos::findFirst(
                [
                    'nombre = :nombre: AND padron_id = :padron_id:',
                    'bind' => [
                        'nombre' => trim($request['nombre']),
                        'padron_id' => intval($request['padron_id'])
                    ]
                ]
            );
            if ($lista) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un requisito con ese nombre para este proveedor'];
            } else {
                $lista = new Requisitos();
                $lista->setTransaction($tx);
                $lista->padron_id = intval($request['padron_id']);
                $lista->nombre = strtoupper($request['nombre']);
                $lista->tramite = 'PADRÓN DE PROVEEDORES';
                $lista->tipo = 'ALL';
                if ($lista->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el requisito.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($lista);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el requisito.'];
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

    public function update_requisito () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $lista = Requisitos::findFirst(
                [
                    'id != :id: AND nombre = :nombre: AND padron_id = :padron_id:',
                    'bind' => [
                        'id' => intval($request['id']),
                        'nombre' => trim($request['nombre']),
                        'padron_id' => intval($request['padron_id'])
                    ]
                ]
            );
            if ($lista) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un requisito con ese nombre para este proveedor'];
            } else {
                $lista = Requisitos::findFirst($request['id']);
                if ($lista) {
                    $lista->setTransaction($tx);
                    // $lista->padron_id = intval($request['padron_id']);
                    $lista->nombre = strtoupper($request['nombre']);
                    $lista->tramite = 'PADRÓN DE PROVEEDORES';
                    $lista->tipo = 'ALL';
                    if ($lista->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el requisito.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($lista);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el requisito.'];
                        $tx->rollback();
                    }
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete_requisito ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $lista = Requisitos::findFirst($request['id']);
                    $lista->setTransaction($tx);

                    if ($lista->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($lista);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el proveedor.'];
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