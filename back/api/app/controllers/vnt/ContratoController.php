<?php

use Phalcon\Mvc\Controller;

class ContratoController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getByID($id_proyecto)
    {
        $where=($id_proyecto!=null?'where vnt_contrato.recurso_id='.$id_proyecto:'');
        

        $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,vnt_contrato.nombre,
                vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,to_char(vnt_contrato.fecha_inicio,'DD-MM-YYY') as f_inicio,to_char(vnt_contrato.fecha_fin,'DD-MM-YYY') as f_fin,
                vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
                vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion, vnt_contrato.organismo_id, vnt_contrato.porcentaje * 100 as porcentaje, vnt_contrato.porcentaje as p,
                vnt_clientes.razon_social as cliente, vnt_contrato.documento_final, vnt_contrato.extension, vnt_contrato.observaciones
                from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
                join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id
                left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id
                ".$where."
                order by vnt_contrato.id";
        $contratos = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        $monto_contratos = 0;
        foreach ($contratos as $elemento){
            $contrato=(object)array();
            $contrato->id = $elemento['id'];
            $contrato->num_contrato = $elemento['num_contrato'];
            $contrato->recurso_id = $elemento['recurso_id'];
            $contrato->recurso = $elemento['recurso'];
            $contrato->nombre = $elemento['nombre'];
            $contrato->empresa_id = $elemento['empresa_id'];
            $contrato->razon_social = $elemento['razon_social'];
            $contrato->fecha_inicio = $elemento['fecha_inicio'];
            $contrato->fecha_fin = $elemento['fecha_fin'];
            $contrato->f_inicio = $elemento['f_inicio'];
            $contrato->f_fin = $elemento['f_fin'];
            $contrato->monto_total = $elemento['monto_total'];
            $monto_contratos = $monto_contratos + $elemento['monto_total'];
            $contrato->licitacion_id = $elemento['licitacion_id'];
            $contrato->porcentaje = $elemento['porcentaje'];
            $contrato->p = $elemento['p'];
            $contrato->cliente = $elemento['cliente'];
            $contrato->documento_final = $elemento['documento_final'];
            $contrato->extension = $elemento['extension'];
            $contrato->rep_legal_contrato = $elemento['rep_legal_contrato'];
            $contrato->organismo_id = $elemento['organismo_id'];
            if ($elemento['extension'] === 'docx') {
                $contrato->color = 'blue-9';
                $contrato->icon = 'fas fa-file-word';
            } else if ($elemento['extension'] === 'pdf' || $elemento['extension'] === 'PDF') {
                $contrato->color = 'red-10';
                $contrato->icon = 'fas fa-file-pdf';
            } else if ($elemento['extension'] === 'xml' || $elemento['extension'] === 'XML') {
                $contrato->color = 'light-green';
                $contrato->icon = 'fas fa-file-code';
            } else if ($elemento['extension'] === 'jpg' || $elemento['extension'] === 'jpeg' || $elemento['extension'] === 'png' || $elemento['extension'] === 'JPG' || $elemento['extension'] === 'JPEG' || $elemento['extension'] === 'PNG') {
                $contrato->color = 'amber';
                $contrato->icon = 'fas fa-file-image';
            }
            
            $contrato->documento_pdf = 0;
            $contrato->doc_type_pdf = 0;
            $contrato->nombre_pdf = 0;
            $contrato->observaciones = $elemento['observaciones'];
            if ($elemento['licitacion_id'] !== null) {
                $contrato_pdf = $this->getFileContrato($elemento['licitacion_id']);
                if (count($contrato_pdf) > 0) {
                    if ($contrato_pdf[0]['documento_id'] !== null) {
                        $contrato->documento_pdf = $contrato_pdf[0]['documento_id'];
                        $contrato->doc_type_pdf = $contrato_pdf[0]['doc_type'];
                        $contrato->nombre_pdf = $contrato_pdf[0]['name'];
                    }
                }
            }
            array_push($nuevo,$contrato);
        }
        $this->content['contratos'] = $nuevo;
        $this->content['monto_contratos'] = number_format(floatval($monto_contratos),2,'.',',');
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getFileContrato ($licitacion_id) {
        $sql = "SELECT lic_requerimientos.licitacion_id,lic_requerimientos.documento_id, lic_requerimientos.nombre_referencia as nombre_documento, (select sys_documents.name from sys_documents where sys_documents.id = lic_requerimientos.documento_id) as name, (select sys_documents.doc_type from sys_documents where sys_documents.id = lic_requerimientos.documento_id) as doc_type
            from lic_requerimientos
            where licitacion_id = $licitacion_id and lic_requerimientos.nombre_referencia like '%CONTRATO%' order by lic_requerimientos.id";
        return $this->db->query($sql)->fetchAll();
    }
    public function getAll ()
    {  $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,vnt_contrato.nombre,
                vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,
                vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
                vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion, vnt_contrato.organismo_id, vnt_contrato.porcentaje * 100 as porcentaje, vnt_contrato.porcentaje as p,
                vnt_clientes.razon_social as cliente, vnt_contrato.documento_final, vnt_contrato.extension
                from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
                join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id
                left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id
                order by vnt_contrato.id";
        $this->content['contratos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getOptions ($id) {
        
        $sql = "SELECT vnt_contrato.id, vnt_contrato.num_contrato from vnt_contrato where vnt_contrato.recurso_id = $id";
        $contratos = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($contratos as $contrato) {
            $array['label'] = '' . $contrato['num_contrato'];
            $array['value'] = $contrato['id'];
            array_push($niveles, $array);
        }
        $this->content['contratosOptions'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            if(trim($request['num_contrato'])!== null){

                $contratos = Contrato::findFirst(
                    [
                        'num_contrato = :num_contrato:',
                        'bind' => [
                            'num_contrato' => trim($request['num_contrato'])
                        ]
                    ]
                );

                if ($contratos) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este número de contrato.'];
                } else {
                    $recurso = Recurso::findFirst($request['recurso_id']);
                    $cliente_id = $recurso->cliente_id;
                    $subprograma_id = $recurso->subprograma_id;

                    $cliente = VntClientes::findFirst($cliente_id);
                    $subprograma = Subprograma::findFirst($subprograma_id);

                    $array_codigo = explode("-",$recurso->codigo);

                    if(count($array_codigo) === 4) {

                        if($cliente->codigo !== "" && $subprograma->codigo !=="" && $array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo) {
                            $contratos = new Contrato();
                            $contratos->setTransaction($tx);
                            
                            $contratos->num_contrato = trim($request['num_contrato']);
                            $contratos->recurso_id = intval($request['recurso_id']);
                            $contratos->empresa_id = intval($request['empresa_id']);
                            $contratos->year = $request['year'];
                            if(intval($request['licitacion_id'])===0){
                                $contratos->licitacion_id = null;
                            } else {
                                $contratos->licitacion_id = intval($request['licitacion_id']);
                            }
                            if($request['fecha_inicio'] === ""){
                                $contratos->fecha_inicio = null;
                            } else {
                                $contratos->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
                            }
                            if($request['fecha_fin'] === ""){
                                $contratos->fecha_fin = null;
                            } else {
                                $contratos->fecha_fin = date("Y/m/d", strtotime($request['fecha_fin']));
                            }
                            $contratos->monto_total = floatval($request['monto_total']);
                            $contratos->rep_legal_contrato = $request['rep_legal_contrato'];
                            if(trim($request['nombre']) === "") {
                                $contratos->nombre = null;
                            } else {
                                $contratos->nombre = trim($request['nombre']);
                            }
                            if(intval($request['organismo_id']) === 0){
                                $contratos->organismo_id = null;
                            } else {
                                $contratos->organismo_id = intval($request['organismo_id']);
                            }
                            // $contratos->porcentaje = intval($request['porcentaje']);
                            if (intval($request['porcentaje']) > 0) {
                                $contratos->porcentaje = intval($request['porcentaje'])/100;
                            } else {
                                $contratos->porcentaje = 0;
                            }
                            if(trim($request['observaciones']) === "") {
                                $contratos->observaciones = null;
                            } else {
                                $contratos->observaciones = trim($request['observaciones']);
                            }
                            if ($contratos->create()) {
                                
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el contrato.', 'id' => $contratos->id ];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($contratos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el contrato.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                    }
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El número de contrato no puede ser 0'];
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

            // var_dump($request);
            if(trim($request['num_contrato'])!== null){

                $contratos = Contrato::findFirst(
                    [
                        'id != :id: AND num_contrato = :num_contrato:',
                        'bind' => [
                            'num_contrato' => trim($request['num_contrato']),
                            'id' => intval($request['id'])
                        ]
                    ]
                );

                if ($contratos) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este número de contrato.'];
                } else {

                    $recurso = Recurso::findFirst($request['recurso_id']);
                    $cliente_id = $recurso->cliente_id;
                    $subprograma_id = $recurso->subprograma_id;

                    $cliente = VntClientes::findFirst($cliente_id);
                    $subprograma = Subprograma::findFirst($subprograma_id);

                    $array_codigo = explode("-",$recurso->codigo);

                    if(count($array_codigo) === 4) {

                        if($cliente->codigo !== "" && $subprograma->codigo !=="" && $array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo) {


                            $contratos = Contrato::findFirst($request['id']);
                            if ($contratos) {
                                $contratos->setTransaction($tx);
                                $contratos->year = $recurso->year;
                                $contratos->num_contrato = trim($request['num_contrato']);
                                $contratos->recurso_id = intval($request['recurso_id']);
                                $contratos->empresa_id = intval($request['empresa_id']);
                                if(intval($request['licitacion_id'])===0){
                                    $contratos->licitacion_id = null;
                                } else {
                                    $contratos->licitacion_id = intval($request['licitacion_id']);
                                }
                                if($request['fecha_inicio'] === ""){
                                    $contratos->fecha_inicio = null;
                                } else {
                                    $contratos->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
                                }
                                if($request['fecha_fin'] === ""){
                                    $contratos->fecha_fin = null;
                                } else {
                                    $contratos->fecha_fin = date("Y/m/d", strtotime($request['fecha_fin']));
                                }
                                $contratos->monto_total = floatval($request['monto_total']);
                                $contratos->rep_legal_contrato = $request['rep_legal_contrato'];
                                if (trim($request['nombre']) === "") {
                                    $contratos->nombre = null;
                                } else {
                                    $contratos->nombre = trim($request['nombre']);
                                }
                                if(intval($request['organismo_id']) === 0){
                                    $contratos->organismo_id = null;
                                } else {
                                    $contratos->organismo_id = intval($request['organismo_id']);
                                }
                                if (intval($request['porcentaje']) > 0) {
                                    $contratos->porcentaje = intval($request['porcentaje'])/100;
                                } else {
                                    $contratos->porcentaje = 0;
                                }
                                if(trim($request['observaciones']) === "") {
                                    $contratos->observaciones = null;
                                } else {
                                    $contratos->observaciones = trim($request['observaciones']);
                                }
                                if ($contratos->update()) {
                                    $this->content['result'] = 'success';
                                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el contrato.'];
                                    $tx->commit();
                                } else {
                                    $this->content['error'] = Helpers::getErrors($contratos);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el contrato.'];
                                    $tx->rollback();
                                }
                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                    }
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El número de contrato no puede ser 0'];
            }

        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = Contrato::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/contratos_documentos_finales/' .$documento->id.'.'.$ext;
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

    public function deleteFile()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $contratos = Contrato::findFirst(intval($request['id']));

            if ($contratos) {
                $contratos->setTransaction($tx);
                $contratos->documento_final = null;
                if ($contratos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el contrato.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($contratos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el contrato.'];
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
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/contratos_documentos_finales/';
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

                                    $contrato = Contrato::findFirst(intval($request['contrato_id']));
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
                    $contratos = Contrato::findFirst($request['id']);
                    $contratos->setTransaction($tx);

                    if ($contratos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($contratos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el contrato.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

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
}