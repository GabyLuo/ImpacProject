<?php

use Phalcon\Mvc\Controller;

class EmpresaController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $sql = "SELECT * 
            FROM vnt_empresa";
                    
        if(strtoupper($role)===strtoupper('finanzas') || strtoupper($role)===strtoupper('finanzas/comisiones')) {
            $sql = $sql . " where padre_id is null ORDER BY razon_social";
        } else {
            $sql = $sql . " where permisos = 'todos' and padre_id is null ORDER BY razon_social";
        }
        // print_r($sql);

        $this->content['empresas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPadre ($padre)
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $sql = "SELECT * 
            FROM vnt_empresa where padre_id is not NULL";

        $this->content['subempresas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $empresas = Empresa::findFirst(
                [
                    'razon_social = :razon_social: OR rfc = :rfc:',
                    'bind' => [
                        'razon_social' => trim(strtoupper($request['razon_social'])),
                        'rfc' => trim(strtoupper($request['rfc']))
                    ]
                ]
            );

            $empresas_hijo = false;

            if (intval($request['padre_id']) !== 0) {
                $empresas_hijo = Empresa::findFirst(
                    [
                        '(razon_social = :razon_social: OR rfc = :rfc: OR curp_representante = :curp_representante: OR nombre_corto = :nombre_corto: OR rfc_representante = :rfc_representante:) AND padre_id = :padre_id: ',
                        'bind' => [
                            'razon_social' => trim(strtoupper($request['razon_social'])),
                            'rfc' => trim(strtoupper($request['rfc'])),
                            'curp_representante' => trim(strtoupper($request['curp_representante'])),
                            'nombre_corto' => trim(strtoupper($request['nombre_corto'])),
                            'rfc_representante' => trim(strtoupper($request['rfc_representante'])),
                            'padre_id' => intval($request['padre_id'])
                        ]
                    ]
                );
            }
            
            
            if ( ($empresas && intval($request['padre_id'])===0) || ($empresas_hijo && intval($request['padre_id'])!==0)) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una empresa con estos datos.'];
            } else {
                $empresas = new Empresa();
                $empresas->setTransaction($tx);
                $empresas->razon_social = trim(strtoupper($request['razon_social']));
                $empresas->nombre_corto = trim(strtoupper($request['nombre_corto']));
                $empresas->rfc = trim(strtoupper($request['rfc']));
                $empresas->rep_legal = trim(strtoupper($request['rep_legal']));
                $empresas->regimen = trim(strtoupper($request['regimen']));
                if(trim(strtoupper($request['curp_representante']))===''){
                    $empresas->curp_representante = null;
                } else {
                    $empresas->curp_representante = trim(strtoupper($request['curp_representante']));
                }
                if(trim(strtoupper($request['rfc_representante']))===''){
                    $empresas->rfc_representante = null;
                } else {
                    $empresas->rfc_representante = trim(strtoupper($request['rfc_representante']));
                }
                if(intval($request['subempresa_id'])===0) {
                    $empresas->subempresa_id = null;
                } else {
                    $empresas->subempresa_id = intval($request['subempresa_id']);
                }
                if(intval($request['padre_id']) === 0) {
                    $empresas->padre_id = null;
                } else {
                    $empresas->padre_id = intval($request['padre_id']);
                }
                $validUser = Auth::getUserData($this->config);
                $id = $validUser->user_id;
                $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
                    
                if($role === 'Finanzas' || $role === 'Finanzas/Comisiones') {
                    $empresas->permisos = 'gastos';
                } else {
                    $empresas->permisos = 'todos';
                }

                if(trim(strtoupper($request['telefono']))===''){
                    $empresas->telefono = null;
                } else {
                    $empresas->telefono = trim(strtoupper($request['telefono']));
                }
                if(trim(strtoupper($request['correo']))===''){
                    $empresas->correo = null;
                } else {
                    $empresas->correo = trim($request['correo']);
                }
                
                if ($empresas->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la empresa.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($empresas);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la empresa.'];
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

            $empresas = Empresa::findFirst(
                [
                    'id != :id: AND (razon_social = :razon_social: OR rfc = :rfc:)',
                    'bind' => [
                        'razon_social' => trim(strtoupper($request['razon_social'])),
                        'rfc' => trim(strtoupper($request['rfc'])),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($empresas) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe una empresa con estos datos.'];
            } else {
                $empresas = Empresa::findFirst($request['id']);
                if ($empresas) {
                    $cadena_log = '';
                    $empresas->setTransaction($tx);
                    if ($empresas->razon_social !== trim(strtoupper($request['razon_social']))) {
                        $cadena_log = $cadena_log . 'razon social, ';
                    }
                    if ($empresas->nombre_corto !== trim(strtoupper($request['nombre_corto']))) {
                        $cadena_log = $cadena_log . 'nombre corto de la empresa, ';
                    }
                    if ($empresas->rfc !== trim(strtoupper($request['rfc']))) {
                        $cadena_log = $cadena_log . 'rfc ';
                    }
                    if ($empresas->rep_legal !== trim(strtoupper($request['rep_legal']))) {
                        $cadena_log = $cadena_log . 'representante legal, ';
                    }
                    if ($empresas->curp_representante !== trim(strtoupper($request['curp_representante']))) {
                        $cadena_log = $cadena_log . 'curp del representante, ';
                    }
                    if ($empresas->rfc_representante !== trim(strtoupper($request['rfc_representante']))) {
                        $cadena_log = $cadena_log . 'rfc del representante.';
                    }
                    $empresas->razon_social = trim(strtoupper($request['razon_social']));
                    $empresas->nombre_corto = trim(strtoupper($request['nombre_corto']));
                    $empresas->rfc = trim(strtoupper($request['rfc']));
                    $empresas->rep_legal = trim(strtoupper($request['rep_legal']));
                    $empresas->regimen = trim(strtoupper($request['regimen']));
                    if(trim(strtoupper($request['curp_representante']))===''){
                        $empresas->curp_representante = null;
                    } else {
                        $empresas->curp_representante = trim(strtoupper($request['curp_representante']));
                    }
                    if(trim(strtoupper($request['rfc_representante']))===''){
                        $empresas->rfc_representante = null;
                    } else {
                        $empresas->rfc_representante = trim(strtoupper($request['rfc_representante']));
                    }
                    if(trim(strtoupper($request['telefono']))===''){
                        $empresas->telefono = null;
                    } else {
                        $empresas->telefono = trim(strtoupper($request['telefono']));
                    }
                    if(trim(strtoupper($request['correo']))===''){
                        $empresas->correo = null;
                    } else {
                        $empresas->correo = trim($request['correo']);
                    }
                    if(intval($request['subempresa_id'])===0){
                        $empresas->subempresa_id = null;
                    } else {
                        $empresas->subempresa_id = intval($request['subempresa_id']);
                    }
                    if(intval($request['padre_id']) === 0) {
                        $empresas->padre_id = null;
                    } else {
                        $empresas->padre_id = intval($request['padre_id']);
                    }
                    if ($empresas->update()) {
                        if ($cadena_log != '') {
                            $logs = new Logs();
                            $logs->setTransaction($tx);
                            $validUser = Auth::getUserData($this->config);
                            $logs->account_id = $validUser->user_id;
                            $logs->level_id = 10;
                            $logs->log = 'MODIFICÓ LA EMPRESA CON NÚMERO ' . $empresas->id . ', LOS CAMBIOS REALIZADOS MODIFICAN ESTOS CAMPOS: ' . strtoupper($cadena_log);
                            $logs->fecha = date("Y-m-d H:i:s");
                            if($logs->create()){
                              $this->content['result'] = 'success';
                              $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la empresa.'];
                              $tx->commit();
                            } else {
                              $this->content['result'] = 'success';
                              $this->content['message'] = ['title' => '¡Exito!', 'content' => 'No se pudo actualizar la empresa.'];
                              $tx->rollback();
                            }
                        } else {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la empresa'];
                        $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($empresas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la empresa'];
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
                    $empresas = Empresa::findFirst($request['id']);
                    $empresas->setTransaction($tx);

                    if ($empresas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($empresas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la empresa.'];
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