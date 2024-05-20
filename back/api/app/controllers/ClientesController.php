<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 17/09/17
 * Time: 11:42 AM
 */

use Phalcon\Mvc\Controller;

class ClientesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;

        if (strtoupper($role) === 'ROOT') {
            $condicion = "";
        } else {
            $condicion = " and vnt_clientes.tipo = 'GDP'";
        }
        if (strtoupper($role) === 'INVENTARIOS' || strtoupper($role) === 'ADMINISTRACIÓN MAQUILADORA') {
            $condicion = " and vnt_clientes.tipo = 'RETAIL'";
        }

        $sql = "SELECT vnt_clientes.*,vnt_municipio.nombre as municipio_nombre,
		vnt_estado.nombre as estado_nombre
        FROM vnt_clientes left join vnt_estado on vnt_estado.id = vnt_clientes.estado_id
		left join vnt_municipio on vnt_municipio.id = vnt_clientes.municipio_id
        JOIN sys_accounts on sys_accounts.id=vnt_clientes.account_id".$condicion."
        ORDER BY vnt_clientes.razon_social";
        $this->content['clientes'] = $this->db->query($sql)->fetchAll();

        // $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/logos/';
        // chdir($upload_dir);
        foreach ($this->content['clientes'] as $key => $cliente) {
            $cliente_id = $cliente['id'];
            $totalRecursos = "SELECT count(vnt_recurso.id) as total_recursos
                              from vnt_recurso
                              where cliente_id = $cliente_id";
            $this->content['clientes'][$key]['total_recursos'] = $this->db->query($totalRecursos)->fetchAll()[0]['total_recursos'];
            
            /* $this->content['clientes'][$key]['logo'] = '';
            foreach (glob('logo_'.$cliente['id'].'.*') as $nombre_fichero) {
                $this->content['clientes'][$key]['logo'] = $nombre_fichero;
            } */
        }
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAll_byName ()
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;

        if (strtoupper($role) === 'ROOT') {
            $condicion = "";
        } else {
            $condicion = " and vnt_clientes.tipo = 'GDP'";
        }
        if (strtoupper($role) === 'INVENTARIOS' || strtoupper($role) === 'ADMINISTRACIÓN MAQUILADORA') {
            $condicion = " and vnt_clientes.tipo = 'RETAIL'";
        }
        $sql = "SELECT vnt_clientes.*
        FROM vnt_clientes
        JOIN sys_accounts on sys_accounts.id=vnt_clientes.account_id".$condicion."
        ORDER BY vnt_clientes.razon_social";
        $this->content['clientes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            if(trim($request['rfc'])===''){
                $cliente = VntClientes::findFirst(
                    [
                        'razon_social = :razon_social: AND codigo = :codigo: AND nombre_corto = :nombre_corto:',
                        'bind' => [
                            'razon_social' => trim(strtoupper($request['razon_social'])),
                            'codigo' => trim(strtoupper($request['codigo'])),
                            'nombre_corto' => trim(strtoupper($request['nombre_corto']))
                        ]
                    ]
                );
            } else {
                $cliente = VntClientes::findFirst(
                    [
                        'razon_social = :razon_social: AND codigo = :codigo: AND rfc = :rfc: AND nombre_corto = :nombre_corto:',
                        'bind' => [
                            'razon_social' => trim(strtoupper($request['razon_social'])),
                            'codigo' => trim(strtoupper($request['codigo'])),
                            'rfc' => trim(strtoupper($request['rfc'])),
                            'nombre_corto' => trim(strtoupper($request['nombre_corto']))
                        ]
                    ]
                );
            }
            if ($cliente) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un cliente con estos datos.'];
            } else {
                $cliente = new VntClientes();
                $cliente->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $cliente->account_id = $validUser->account_id;

                //
                $id = $validUser->user_id;
                $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;

                if (strtoupper($role) === 'INVENTARIOS' || strtoupper($role) === 'ADMINISTRACIÓN MAQUILADORA') {
                    $tipo = 'RETAIL';
                } else {
                    $tipo = 'GDP';
                }
                //
                $cliente->tipo = $tipo;
                $cliente->codigo = trim(strtoupper($request['codigo']));
                $cliente->razon_social = trim(strtoupper($request['razon_social']));
                $cliente->nombre_corto = trim(strtoupper($request['nombre_corto']));
                $cliente->estado_id = intval($request['estado_id']);
                if(intval($request['municipio_id'])===0){
                    $cliente->municipio_id = null;
                } else {
                    $cliente->municipio_id = intval($request['municipio_id']);
                }
                $cliente->status = trim(strtoupper($request['status']));
                if(trim($request['rfc']) === ''){
                    $cliente->rfc = null;
                } else {
                    $cliente->rfc = trim(strtoupper($request['rfc']));
                }
                $cliente->precio_lista = $request['precio'];
                if ($cliente->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el cliente.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cliente);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el cliente.'];
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

            if(trim($request['rfc'])===''){
                $cliente = VntClientes::findFirst(
                    [
                        'id != :id: AND (razon_social = :razon_social: AND codigo = :codigo: AND nombre_corto=:nombre_corto:)',
                        'bind' => [
                            'razon_social' => strtoupper($request['razon_social']),
                            'id' => intval($request['id']),
                            'codigo' => strtoupper($request['codigo']),
                            'nombre_corto' => trim(strtoupper($request['nombre_corto']))
                        ]
                    ]
                );
            } else {
                $cliente = VntClientes::findFirst(
                    [
                        'id != :id: AND (razon_social = :razon_social: AND codigo = :codigo: AND rfc = :rfc: AND nombre_corto = :nombre_corto:)',
                        'bind' => [
                            'razon_social' => trim(strtoupper($request['razon_social'])),
                            'id' => intval($request['id']),
                            'codigo' => trim(strtoupper($request['codigo'])),
                            'rfc' => trim(strtoupper($request['rfc'])),
                            'nombre_corto' => trim(strtoupper($request['nombre_corto']))
                        ]
                    ]
                );
            }
            if ($cliente) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un cliente con estos datos'];
            } else {
                $cliente = VntClientes::findFirst($request['id']);
                if ($cliente) {
                    $cliente->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $cliente->account_id = $validUser->account_id;
                    $cliente->codigo = trim(strtoupper($request['codigo']));
                    $cliente->razon_social = trim(strtoupper($request['razon_social']));
                    $cliente->nombre_corto = trim(strtoupper($request['nombre_corto']));
                    $cliente->estado_id = intval($request['estado_id']);
                    if(intval($request['municipio_id'])===0){
                        $cliente->municipio_id = null;
                    } else {
                        $cliente->municipio_id = intval($request['municipio_id']);
                    }
                    $cliente->status = trim(strtoupper($request['status']));
                    if(trim($request['rfc']) === ''){
                        $cliente->rfc = null;
                    } else {
                        $cliente->rfc = trim(strtoupper($request['rfc']));
                    }
                    $cliente->precio_lista = $request['precio'];
                    if ($cliente->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el cliente.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($cliente);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el cliente.'];
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
                $user = VntClientes::findFirst($request['id']);
                $user->setTransaction($tx);

                if ($user->delete()) {
                    $this->content['result'] = 'success';
                } else {
                    $this->content['error'] = Helpers::getErrors($user);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }
                

                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha borrado el cliente.'];
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function uploadLogo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();

            if ($this->request->hasFiles()) {
                
                // $upload_dir = __DIR__ . '../../../public/assets/logos/';
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/logos/';


                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if($file->getExtension()==='jpg'||$file->getExtension()==='jpeg'||$file->getExtension()==='png'){
                            $img = 'logo_' . $request['id'] . '.' . $file->getExtension();
                            $nombre = 'logo_' . $request['id'];
                            $update = glob($upload_dir.$nombre.'.*');
                            if(!empty($update)){
                                foreach (glob($upload_dir.$nombre.'.*') as $nombre_fichero) {
                                    unlink($nombre_fichero);
                                }
                                $file->moveTo($upload_dir . $img);
                                if($file->getExtension()==='jpg'||$file->getExtension()==='jpeg'){
                                    $path=$upload_dir . $img;
                                    $this->orientation($path);
                                }
                                $content['result'] = 'success';
                            }else{
                                $file->moveTo($upload_dir . $img);
                                if (file_exists($upload_dir . $img)) {
                                    chmod($upload_dir . $img, 0777);
                                    if($file->getExtension()==='jpg'||$file->getExtension()==='jpeg'){
                                        $path=$upload_dir . $img;
                                        $this->orientation($path);
                                    }
                                    $content['result'] = 'success';
                                    // $content['existe'] = TRUE;
                                }
                            }
                        } else {
                            $this->content['err'] = 'Sólo archivos con extensión .jpg, .jpeg y .png';
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg y .png'];
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