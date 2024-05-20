<?php

use Phalcon\Mvc\Controller;

class ProveedoresController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, nombre_comercial, razon_social, COALESCE (rfc,'(SIN RFC)') as rfc, curp, created, created_by, modified, modified_by, direccion, banco, clabe, banco2, clabe2, telefono, contacto, toka, email, banco3, clabe3, banco4, clabe4, tarjeta1, tarjeta2, tarjeta3, tarjeta4, tipo
            FROM pmo_proveedores
                order by nombre_comercial";

        $proveedores = $this->content['proveedor'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getById ($id)
    {
        $sql = "SELECT *
                FROM pmo_proveedores 
                where id = $id";

        $this->content['proveedorid'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }


    public function getOptions () {
        $sql = "SELECT id as value, razon_social as label FROM pmo_proveedores";

        $this->content['proveedores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getCuentas ($id) {
        $sql = "SELECT clabe, clabe2, clabe3, clabe4, toka, tarjeta1, tarjeta2, tarjeta3, tarjeta4, banco, banco2, banco3, banco4
            from pmo_proveedores
            where id = $id";
        $data = $this->db->query($sql)->fetchAll();
        $cuentas = [];
        if (count($data) > 0) {
            if ($data[0]['clabe'] !== null) {
                $array['label'] = 'CLABE ' . $data[0]['banco'] . ' - ' . $data[0]['clabe'];
                $array['value'] = 'banco1';
                array_push($cuentas, $array);
            }
            if ($data[0]['clabe2'] !== null) {
                $array['label'] = 'CLABE ' . $data[0]['banco2'] . ' - ' . $data[0]['clabe2'];
                $array['value'] = 'banco2';
                array_push($cuentas, $array);
            }
            if ($data[0]['clabe3'] !== null) {
                $array['label'] = 'CLABE ' . $data[0]['banco3'] . ' - ' . $data[0]['clabe3'];
                $array['value'] = 'banco3';
                array_push($cuentas, $array);
            }
            if ($data[0]['clabe4'] !== null) {
                $array['label'] = 'CLABE ' . $data[0]['banco4'] . ' - ' . $data[0]['clabe4'];
                $array['value'] = 'banco4';
                array_push($cuentas, $array);
            }
            if ($data[0]['toka'] !== null) {
                $array['label'] = 'TOKA ' . $data[0]['toka'];
                $array['value'] = 'toka';
                array_push($cuentas, $array);
            }
            if ($data[0]['tarjeta1'] !== null) {
                $array['label'] = 'TARJETA ' . $data[0]['banco'] . ' - ' . $data[0]['tarjeta1'];
                $array['value'] = 'tarjeta1';
                array_push($cuentas, $array);
            }
            if ($data[0]['tarjeta2'] !== null) {
                $array['label'] = 'TARJETA ' . $data[0]['banco2'] . ' - ' . $data[0]['tarjeta2'];
                $array['value'] = 'tarjeta2';
                array_push($cuentas, $array);
            }
            if ($data[0]['tarjeta3'] !== null) {
                $array['label'] = 'TARJETA ' . $data[0]['banco3'] . ' - ' . $data[0]['tarjeta3'];
                $array['value'] = 'tarjeta3';
                array_push($cuentas, $array);
            }
            if ($data[0]['tarjeta4'] !== null) {
                $array['label'] = 'TARJETA ' . $data[0]['banco4'] . ' - ' . $data[0]['tarjeta4'];
                $array['value'] = 'tarjeta4';
                array_push($cuentas, $array);
            }
        }
        $this->content['cuentas'] = $cuentas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportCSV () {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Nombre comercial', 'Razón social', 'RFC', 'CURP', 'Dirección', 'Teléfono', 'Contacto', 'Correo electrónico', 'Banco', 'Clabe', 'Tarjeta', 'Banco 2', 'Clabe 2', 'Tarjeta2', 'Toka', 'Banco 3', 'Clabe 3', 'Tarjeta3', 'Banco 4', 'Clabe 4', 'Tarjeta4', 'Tipo'), ',');

        $sql = "SELECT id, nombre_comercial, razon_social, COALESCE (rfc,'(SIN RFC)') as rfc, curp, created, created_by, modified, modified_by, direccion, banco, clabe, banco2, clabe2, telefono, contacto, toka, email, banco3, clabe3, banco4, clabe4, tarjeta1, tarjeta2, tarjeta3, tarjeta4, tipo
            FROM pmo_proveedores
                order by nombre_comercial";

        $arreglo = $this->db->query($sql)->fetchAll();
        foreach($arreglo as $elemento){
            fputcsv($fp, [
                $elemento['nombre_comercial'],
                $elemento['razon_social'],
                $elemento['rfc'],
                $elemento['curp'],
                $elemento['direccion'],
                $elemento['telefono'],
                $elemento['contacto'],
                $elemento['email'],
                $elemento['banco'],
                'CLABE '.$elemento['clabe'],
                'TARJETA '.$elemento['tarjeta1'],
                $elemento['banco2'],
                'CLABE '.$elemento['clabe2'],
                'TARJETA '.$elemento['tarjeta2'],
                $elemento['toka'],
                $elemento['banco3'],
                'CLABE '.$elemento['clabe3'],
                'TARJETA '.$elemento['tarjeta3'],
                $elemento['banco4'],
                'CLABE '.$elemento['clabe4'],
                'TARJETA '.$elemento['tarjeta4'],
                $elemento['tipo']
                ], ',');
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Proveedores'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $proveedores = Proveedores::findFirst(
                [
                    'rfc = :rfc: OR curp = :curp: OR clabe = :clabe: OR nombre_comercial = :nombre_comercial:',
                    'bind' => [
                        'nombre_comercial' => trim(strtoupper($request['nombre_comercial'])),
                        'rfc' => trim(strtoupper($request['rfc'])),
                        'curp' => trim(strtoupper($request['curp'])),
                        'clabe' => trim(strtoupper($request['clabe']))
                    ]
                ]
            );
            if ($proveedores) {
                $rfc = trim(strtoupper($request['rfc']));
                $curp = trim(strtoupper($request['curp']));
                $clabe = trim(strtoupper($request['clabe']));
                $nombre_comercial = trim(strtoupper($request['nombre_comercial']));
                if (trim(strtoupper($request['rfc'])) != '') {
                    $sql_rfc = "SELECT * from pmo_proveedores where rfc = '$rfc'";
                    $proveedor_rfc = $this->db->query($sql_rfc)->fetchAll();
                    if (sizeof($proveedor_rfc) > 0) {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con ese rfc.'];
                    }
                }
                if (trim(strtoupper($request['curp'])) != '') {
                    $sql_curp = "SELECT * from pmo_proveedores where curp = '$curp'";
                    $proveedor_curp = $this->db->query($sql_curp)->fetchAll();
                    if (sizeof($proveedor_curp) > 0) {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con ese curp.'];
                    }
                }
                $sql_clabe = "SELECT * from pmo_proveedores where clabe = '$clabe'";
                $proveedor_clabe = $this->db->query($sql_clabe)->fetchAll();
                if (sizeof($proveedor_clabe) > 0) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con esa clabe.'];
                }
                $sql_nombre_comercial = "SELECT * from pmo_proveedores where nombre_comercial = '$nombre_comercial'";
                $proveedor_nombre_comercial = $this->db->query($sql_nombre_comercial)->fetchAll();
                if (sizeof($proveedor_nombre_comercial) > 0) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con ese nombre comercial.'];
                }
            } else {
                $proveedores = new Proveedores();
                $proveedores->setTransaction($tx);
                $proveedores->nombre_comercial = trim(strtoupper($request['nombre_comercial']));
              
                if(trim(strtoupper($request['razon_social'])) == ''){
                    $proveedores->razon_social = null;
                } else {
                    $proveedores->razon_social = trim(strtoupper($request['razon_social']));
                }
                if(trim(strtoupper($request['rfc'])) == ''){
                    $proveedores->rfc = null;
                } else {
                    $proveedores->rfc = trim(strtoupper($request['rfc']));
                }

                if(trim(strtoupper($request['curp'])) == ''){
                    $proveedores->curp = null;
                } else {
                    $proveedores->curp = trim(strtoupper($request['curp']));
                }
                
                if(trim(strtoupper($request['direccion'])) == ''){
                    $proveedores->direccion = null;
                } else {
                    $proveedores->direccion = strtoupper($request['direccion']);
                }

                if(trim(strtoupper($request['banco'])) == ''){
                    $proveedores->banco = null;
                } else {
                    $proveedores->banco = strtoupper($request['banco']);
                }

                if(trim(strtoupper($request['clabe'])) == ''){
                    $proveedores->clabe = null;
                } else {
                    $proveedores->clabe = trim(strtoupper($request['clabe']));
                }

                if(trim(strtoupper($request['banco2'])) == ''){
                    $proveedores->banco2 = null;
                } else {
                    $proveedores->banco2 = strtoupper($request['banco2']);
                }

                if(trim(strtoupper($request['clabe2'])) == ''){
                    $proveedores->clabe2 = null;
                } else {
                    $proveedores->clabe2 = trim(strtoupper($request['clabe2']));
                }

                if(trim(strtoupper($request['telefono'])) == ''){
                    $proveedores->telefono = null;
                } else {
                    $proveedores->telefono = trim(strtoupper($request['telefono']));
                }

                if(trim(strtoupper($request['contacto'])) == ''){
                    $proveedores->contacto = null;
                } else {
                    $proveedores->contacto = strtoupper($request['contacto']);
                }
                
                if(trim(strtoupper($request['toka'])) == ''){
                    $proveedores->toka = null;
                } else {
                    $proveedores->toka = strtoupper($request['toka']);
                }

                if(trim($request['email']) == ''){
                    $proveedores->email = null;
                } else {
                    $proveedores->email = trim($request['email']);
                }

                if(trim(strtoupper($request['banco3'])) == ''){
                    $proveedores->banco3 = null;
                } else {
                    $proveedores->banco3 = strtoupper($request['banco3']);
                }

                if(trim(strtoupper($request['clabe3'])) == ''){
                    $proveedores->clabe3 = null;
                } else {
                    $proveedores->clabe3 = trim(strtoupper($request['clabe3']));
                }

                if(trim(strtoupper($request['banco4'])) == ''){
                    $proveedores->banco4 = null;
                } else {
                    $proveedores->banco4 = strtoupper($request['banco4']);
                }

                if(trim(strtoupper($request['clabe4'])) == ''){
                    $proveedores->clabe4 = null;
                } else {
                    $proveedores->clabe4 = trim(strtoupper($request['clabe4']));
                }

                $proveedores->tarjeta1 = trim($request['tarjeta1']) ?? null;
                $proveedores->tarjeta2 = trim($request['tarjeta2']) ?? null;
                $proveedores->tarjeta3 = trim($request['tarjeta3']) ?? null;
                $proveedores->tarjeta4 = trim($request['tarjeta4']) ?? null;
                $proveedores->tipo = trim($request['tipo']) ?? null;

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

            $proveedores = Proveedores::findFirst(
                [
                    'id != :id: AND (nombre_comercial = :nombre_comercial: OR rfc = :rfc: OR curp = :curp:)',
                    'bind' => [
                        'nombre_comercial' => trim(strtoupper($request['nombre_comercial'])),
                        'rfc' => trim(strtoupper($request['rfc'])),
                        'curp' => trim(strtoupper($request['curp'])),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            
            if ($proveedores) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un proveedor con esos datos.'];
            } else {
                $proveedores = Proveedores::findFirst($request['id']);
                if ($proveedores) {
                    $proveedores->setTransaction($tx);
                    $proveedores->nombre_comercial = trim(strtoupper($request['nombre_comercial']));
                    if(trim(strtoupper($request['razon_social'])) == '') {
                        $proveedores->razon_social = null;
                    } else {
                        $proveedores->razon_social = trim(strtoupper($request['razon_social']));
                    }
                    if(trim(strtoupper($request['rfc'])) == ''){
                        $proveedores->rfc = null;
                    } else {
                        $proveedores->rfc = trim(strtoupper($request['rfc']));
                    }
                    if(trim(strtoupper($request['curp'])) == ''){
                        $proveedores->curp = null;
                    } else {
                        $proveedores->curp = trim(strtoupper($request['curp']));
                    }
                                    
                if(trim(strtoupper($request['direccion'])) == ''){
                    $proveedores->direccion = null;
                } else {
                    $proveedores->direccion = strtoupper($request['direccion']);
                }

                if(trim(strtoupper($request['banco'])) == ''){
                    $proveedores->banco = null;
                } else {
                    $proveedores->banco = strtoupper($request['banco']);
                }

                if(trim(strtoupper($request['clabe'])) == ''){
                    $proveedores->clabe = null;
                } else {
                    $proveedores->clabe = trim(strtoupper($request['clabe']));
                }

                if(trim(strtoupper($request['banco2'])) == ''){
                    $proveedores->banco2 = null;
                } else {
                    $proveedores->banco2 = strtoupper($request['banco2']);
                }

                if(trim(strtoupper($request['clabe2'])) == ''){
                    $proveedores->clabe2 = null;
                } else {
                    $proveedores->clabe2 = trim(strtoupper($request['clabe2']));
                }

                if(trim(strtoupper($request['telefono'])) == ''){
                    $proveedores->telefono = null;
                } else {
                    $proveedores->telefono = trim(strtoupper($request['telefono']));
                }

                if(trim(strtoupper($request['contacto'])) == ''){
                    $proveedores->contacto = null;
                } else {
                    $proveedores->contacto = strtoupper($request['contacto']);
                }

                if(trim(strtoupper($request['toka'])) == ''){
                    $proveedores->toka = null;
                } else {
                    $proveedores->toka = strtoupper($request['toka']);
                }

                if(trim($request['email']) == ''){
                    $proveedores->email = null;
                } else {
                    $proveedores->email = trim($request['email']);
                }

                if(trim(strtoupper($request['banco3'])) == ''){
                    $proveedores->banco3 = null;
                } else {
                    $proveedores->banco3 = strtoupper($request['banco3']);
                }

                if(trim(strtoupper($request['clabe3'])) == ''){
                    $proveedores->clabe3 = null;
                } else {
                    $proveedores->clabe3 = trim(strtoupper($request['clabe3']));
                }

                if(trim(strtoupper($request['banco4'])) == ''){
                    $proveedores->banco4 = null;
                } else {
                    $proveedores->banco4 = strtoupper($request['banco4']);
                }

                if(trim(strtoupper($request['clabe4'])) == ''){
                    $proveedores->clabe4 = null;
                } else {
                    $proveedores->clabe4 = trim(strtoupper($request['clabe4']));
                }

                $proveedores->tarjeta1 = trim($request['tarjeta1']) ?? null;
                $proveedores->tarjeta2 = trim($request['tarjeta2']) ?? null;
                $proveedores->tarjeta3 = trim($request['tarjeta3']) ?? null;
                $proveedores->tarjeta4 = trim($request['tarjeta4']) ?? null;
                $proveedores->tipo = trim($request['tipo']) ?? null;

                    if ($proveedores->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el proveedor'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($proveedores);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el proveedor'];
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
                    $proveedores = Proveedores::findFirst($request['id']);
                    $proveedores->setTransaction($tx);

                    if ($proveedores->delete()) {
                        $this->content['result'] = 'success';
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
        $content = $this->content;
        $request = $this->request->getPost();
        
        $doc = trim(strtoupper($request['nombre'])).'_'.$request['curp'];

       
        if ($this->request->hasFiles())  {
           $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/';
            if (!is_dir($upload_dir))  {
                mkdir($upload_dir, 0777);
            }
            $fullPath = '';
            foreach ($this->request->getUploadedFiles() as $file) {
                $fullPath = $upload_dir . $doc.'.'. $file->getExtension();
               
                if  (file_exists($upload_dir . $doc.'.pdf'))  {
                    @unlink($upload_dir . $doc.'.pdf');
                }
                if  (file_exists($upload_dir . $doc.'.docx'))  {
                    @unlink($upload_dir . $doc.'.docx');
                }

                $file->moveTo($fullPath);
            }
       }
       $this->content['result'] = 'success';
       $this->content['message'] = ['title' => '¡Exito!', 'content' =>  'El Archivo se ha subido con exito'];
       $this->response->setJsonContent($this->content);
       $this->response->send();
    }
    public function getFile($nombre)
    {      
        $archivo=false;

            $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/' .$nombre.'.pdf';
            if(file_exists($path)){
                $archivo=true;
            }else{
                $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/' .$nombre.'.docx';
                if(file_exists($path)){
                    $archivo=true;
                }
            }

            if($archivo){
                $filetype = mime_content_type($path);   
                $filesize = filesize($path);       
                $response = new \Phalcon\Http\Response();
                $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
                $response->setHeader("Content-Length", $filesize);
                $response->setContentType($filetype);
                $response->setFileToSend($path, null, false);
                $response->send();
                return $response;
            } else{
                echo "Sin expediente archivado";
            } 
    }
  }
