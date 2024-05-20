<?php

use Phalcon\Mvc\Controller;

class PerfilesExpertosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT per_perfiles_expertos.id, per_perfiles_expertos.apellido_paterno, per_perfiles_expertos.apellido_materno, 
        per_perfiles_expertos.nombre, per_perfiles_expertos.telefono, per_perfiles_expertos.rfc, per_perfiles_expertos.curp, 
        per_perfiles_expertos.calle, per_perfiles_expertos.numero_exterior, per_perfiles_expertos.numero_interior, 
        per_perfiles_expertos.colonia, per_perfiles_expertos.codigo_postal, per_perfiles_expertos.estado_id, 
        per_perfiles_expertos.municipio_id, per_perfiles_expertos.email, per_perfiles_expertos.foto, 
        per_perfiles_expertos.licenciatura, per_perfiles_expertos.fecha_egreso, per_perfiles_expertos.universidad, 
        per_perfiles_expertos.cedula, per_perfiles_expertos.titulo_certificado, per_perfiles_expertos.extension, 
        per_perfiles_expertos.cedula_archivo, per_perfiles_expertos.extension_cedula, per_perfiles_expertos.area_id,
        (select per_areas.nombre as area from per_areas where per_areas.id = per_perfiles_expertos.area_id)
            FROM per_perfiles_expertos
            ORDER BY nombre";
        $this->content['perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function filtrar ($estado, $municipio, $area, $aptitudes, $curso)
    {
        // var_dump($aptitudes);
        $sql = "SELECT distinct per_perfiles_expertos.id, per_perfiles_expertos.apellido_paterno, per_perfiles_expertos.apellido_materno, 
        per_perfiles_expertos.nombre, per_perfiles_expertos.telefono, per_perfiles_expertos.rfc, per_perfiles_expertos.curp, 
        per_perfiles_expertos.calle, per_perfiles_expertos.numero_exterior, per_perfiles_expertos.numero_interior, 
        per_perfiles_expertos.colonia, per_perfiles_expertos.codigo_postal, per_perfiles_expertos.estado_id, 
        per_perfiles_expertos.municipio_id, per_perfiles_expertos.email, per_perfiles_expertos.foto, 
        per_perfiles_expertos.licenciatura, per_perfiles_expertos.fecha_egreso, per_perfiles_expertos.universidad, 
        per_perfiles_expertos.cedula, per_perfiles_expertos.titulo_certificado, per_perfiles_expertos.extension, 
        per_perfiles_expertos.cedula_archivo, per_perfiles_expertos.extension_cedula, per_perfiles_expertos.area_id,
        (select per_areas.nombre as area from per_areas where per_areas.id = per_perfiles_expertos.area_id)
            FROM per_perfiles_expertos";
        if ($aptitudes === 'all') {
            $sql = $sql . " where per_perfiles_expertos.id > 0";
        } else {
            $sql = $sql . " inner join per_aptitudes_perfiles on per_aptitudes_perfiles.perfil_id = per_perfiles_expertos.id and";
            $array_aptitud = explode(",", $aptitudes);
            $numero_elementos = count($array_aptitud);
            if ($numero_elementos > 1) {
                $sql = $sql . " (per_aptitudes_perfiles.aptitud = '" . $array_aptitud[0] . "'";
                for ($i=1; $i<$numero_elementos-1; $i++) {
                    $sql = $sql . " or per_aptitudes_perfiles.aptitud = '" . $array_aptitud[$i] . "'";
                }
                $sql = $sql . " or per_aptitudes_perfiles.aptitud = '" . $array_aptitud[$numero_elementos-1] . "')";
            } else {
                $sql = $sql . " per_aptitudes_perfiles.aptitud = '" . $array_aptitud[0] . "'";
            }
        }
        if (trim($curso) !== 'all') {
            $sql = $sql . " and (EXISTS (SELECT per_cursos.nombre FROM per_cursos WHERE per_cursos.perfil_id = per_perfiles_expertos.id AND per_cursos.nombre like '%$curso%')) OR (EXISTS (SELECT per_posgrados.nombre FROM per_posgrados WHERE per_posgrados.perfil_id = per_perfiles_expertos.id AND per_posgrados.nombre like '%$curso%'))";
        }
        if (intval($estado) > 0) {
            $sql = $sql . " and per_perfiles_expertos.estado_id = $estado";
        }
        if (intval($municipio) > 0) {
            $sql = $sql . " and per_perfiles_expertos.municipio_id = $municipio";
        }
        if (intval($area) > 0) {
            $sql = $sql . " and per_perfiles_expertos.area_id = $area";
        }
        $sql = $sql . " order by per_perfiles_expertos.id";
        // print_r($sql);

        $perfiles = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($perfiles as $elemento){
            $perfil=(object)array();
            $perfil->id = $elemento['id'];
            $id = $elemento['id'];
            $perfil->nombre_completo = $elemento['nombre'] . ' ' . $elemento['apellido_paterno'] . ' ' . $elemento['apellido_materno'];
            $perfil->nombre = $elemento['nombre'];
            $perfil->apellido_paterno = $elemento['apellido_paterno'];
            $perfil->apellido_materno = $elemento['apellido_materno'];
            $perfil->telefono = $elemento['telefono'];
            $perfil->rfc = $elemento['rfc'];
            $perfil->curp = $elemento['curp'];
            $perfil->calle = $elemento['calle'];
            $perfil->numero_exterior = $elemento['numero_exterior'];
            $perfil->numero_interior = $elemento['numero_interior'];
            $perfil->colonia = $elemento['colonia'];
            $perfil->codigo_postal = $elemento['codigo_postal'];
            $perfil->estado_id = $elemento['estado_id'];
            $perfil->municipio_id = $elemento['municipio_id'];
            $perfil->email = $elemento['email'];
            $perfil->foto = $elemento['foto'];
            $perfil->licenciatura = $elemento['licenciatura'];
            $perfil->fecha_egreso = $elemento['fecha_egreso'];
            $perfil->universidad = $elemento['universidad'];
            $perfil->cedula = $elemento['cedula'];
            $perfil->titulo_certificado = $elemento['titulo_certificado'];
            $perfil->extension = $elemento['extension'];
            $perfil->cedula_archivo = $elemento['cedula_archivo'];
            $perfil->extension_cedula = $elemento['extension_cedula'];
            $perfil->area_id = $elemento['area_id'];
            $perfil->area = $elemento['area'];
            $sql_aptitudes = "SELECT * from per_aptitudes_perfiles where perfil_id = $id";
            $aptitudes = $this->db->query($sql_aptitudes)->fetchAll();
            $nuevo_aptitudes = [];
            foreach ($aptitudes as $aptitud) {
                $apt=(object)array();
                $apt->id = $aptitud['id'];
                $apt->perfil_id = $aptitud['perfil_id'];
                $apt->aptitud = $aptitud['aptitud'];
                $apt->color = 'pink';
                $apt->icon = 'fas fa-file-word';
                array_push($nuevo_aptitudes, $apt);
            }
            $perfil->aptitudes = $nuevo_aptitudes;
            array_push($nuevo,$perfil);
        }

        $this->content['perfiles'] = $nuevo;
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
            /* $perfiles = PerfilesExpertos::findFirst(
                [
                    'curp = :curp:',
                    'bind' => [
                        'curp' => strtoupper($request['curp'])
                    ]
                ]
            );
            if ($perfiles) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un perfil con este curp'];
            } else { */
                $perfiles = new PerfilesExpertos();
                $perfiles->setTransaction($tx);
                $perfiles->apellido_paterno = trim(strtoupper($request['apellido_paterno']));
                $perfiles->apellido_materno = trim(strtoupper($request['apellido_materno']));
                $perfiles->nombre = trim(strtoupper($request['nombre']));
                $perfiles->telefono = trim($request['telefono']);
                $perfiles->rfc = trim(strtoupper($request['rfc']));
                if (trim($request['curp']) === '') {
                    $perfiles->curp = null;
                } else {
                    $perfiles->curp = trim(strtoupper($request['curp']));
                }
                $perfiles->calle = trim(strtoupper($request['calle']));
                $perfiles->numero_exterior = trim(strtoupper($request['numero_exterior']));
                $perfiles->numero_interior = trim(strtoupper($request['numero_interior']));
                $perfiles->colonia = trim(strtoupper($request['colonia']));
                $perfiles->codigo_postal = trim(strtoupper($request['codigo_postal']));
                $perfiles->estado_id = intval($request['estado_id']);
                $perfiles->municipio_id = intval($request['municipio_id']);
                $perfiles->email = trim($request['email']);
                
                if ($perfiles->create()) {
                    $this->content['result'] = 'success';
                    //
                    $nombre_con_id = null;
                    $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/fotos_perfiles/';
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
                                
                                $nombre_con_id = $perfiles->id .'.'. $file->getExtension();
                                $perfiles->foto = $perfiles->id .'.'. $file->getExtension();
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
                                if ($perfiles->update()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($perfiles);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el perfil.'];
                                    $tx->rollback();
                                }
                            } else {
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
                            }
                        }
                    }

                    if ($this->content['result'] === 'success') {
                        $this->content['id'] = $perfiles->id;
                        $this->content['foto'] = $nombre_con_id;
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el perfil.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el perfil.'];
                        $tx->rollback();
                    }
                    //  
                } else {
                    $this->content['error'] = Helpers::getErrors($perfiles);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el perfil.'];
                    $tx->rollback();
                }
            // }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function uploadFoto () {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/fotos_perfiles/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                $nombre_con_id = null;

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png') {

                                    $nombre_con_id = $request['id'] .'.'. $file->getExtension();
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

                                    $perfiles = PerfilesExpertos::findFirst($request['id']);
                                    $perfiles->setTransaction($tx);
                                    $perfiles->foto = $nombre_con_id;
                                    if ($perfiles->update()) {
                                        $content['result'] = 'success';
                                        $content['foto'] = $nombre_con_id;
                                        $content['message'] = ['title' => 'Éxito', 'content' => 'Se ha guardado el archivo.'];
                                        $tx->commit();
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($perfiles);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la fianza.'];
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
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            /* $perfiles = PerfilesExpertos::findFirst(
                [
                    'id != :id:',
                    'bind' => [
                        'curp' => strtoupper($request['curp']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($perfiles) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un perfil con este curp'];
            } else { */
                $perfiles = PerfilesExpertos::findFirst($request['id']);
                if ($perfiles) {
                    $perfiles->setTransaction($tx);
                    $perfiles->apellido_paterno = trim(strtoupper($request['apellido_paterno']));
                    $perfiles->apellido_materno = trim(strtoupper($request['apellido_materno']));
                    $perfiles->nombre = trim(strtoupper($request['nombre']));
                    $perfiles->telefono = trim($request['telefono']);
                    $perfiles->rfc = trim(strtoupper($request['rfc']));
                    $perfiles->curp = trim(strtoupper($request['curp']));
                    $perfiles->calle = trim(strtoupper($request['calle']));
                    $perfiles->numero_exterior = trim(strtoupper($request['numero_exterior']));
                    $perfiles->numero_interior = trim(strtoupper($request['numero_interior']));
                    $perfiles->colonia = trim(strtoupper($request['colonia']));
                    $perfiles->codigo_postal = trim(strtoupper($request['codigo_postal']));
                    $perfiles->estado_id = intval($request['estado_id']);
                    $perfiles->municipio_id = intval($request['municipio_id']);
                    $perfiles->email = trim($request['email']);
                    $perfiles->area_id = intval($request['area_id']);
                    $perfiles->licenciatura = trim($request['licenciatura']);
                    $perfiles->cedula = trim($request['cedula']);
                    $perfiles->universidad = trim($request['universidad']);
                    if ($request['fecha_egreso'] !== '') {
                        $perfiles->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                    } else {
                        $perfiles->fecha_egreso = null;
                    }

                    if ($perfiles->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el perfil.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el perfil'];
                        $tx->rollback();
                    }
                }
            // }
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
                    $perfiles = PerfilesExpertos::findFirst($request['id']);
                    $perfiles->setTransaction($tx);

                    if ($perfiles->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el perfil.'];
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