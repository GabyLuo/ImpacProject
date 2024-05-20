<?php

use Phalcon\Mvc\Controller;

class ProyectoController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getByID($id_proyecto, $year)
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $anio_recurso = Recurso::findFirst($id_proyecto)->year;
        $anio_actual = $year;
        $condicion_anio = "";
        if ($anio_recurso != $anio_actual) {
            // $condicion_anio = " and presupuestos.finalizado = false";
        }
        if (strtoupper($role) === strtoupper('Lider')) {
            $and = " and presupuestos.lider_proyecto = $id";
        } else {
            $and = "";
        }
        $where=($id_proyecto!=null?'where recurso_id='.$id_proyecto:'');

        $sql = "SELECT presupuestos.*,u.nickname from (SELECT pmo_proyecto.id,pmo_proyecto.nombre as nombre_proyecto,recurso_id,vnt_recurso.codigo as recurso,to_char(inicio,'DD-MM-YYY') as f_inicio,inicio,to_char(fin,'DD-MM-YYY') as f_fin,fin,dias,presupuesto,presupuesto_actual,pmo_proyecto.lider_proyecto,pmo_proyecto.licitacion_id,pmo_proyecto.empresa_id, vnt_clientes.razon_social, pmo_proyecto.status, pmo_proyecto.monto_bolsa, pmo_proyecto.monto_bolsa_iva, pmo_proyecto.finalizado, vnt_recurso.sucursal_id, pmo_proyecto.nombre_corto
        from pmo_proyecto join vnt_recurso on vnt_recurso.id=pmo_proyecto.recurso_id join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id order by pmo_proyecto.id DESC) as presupuestos join sys_users u
        on u.id = presupuestos.lider_proyecto ".$where.$and.$condicion_anio;

        $proyectos = $this->content['proyectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy($id)
    {
        $sql = "SELECT * from pmo_proyecto where id = $id";
        $proyectos = $this->content['proyectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy2($id_proyecto)
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        if (strtoupper($role) === strtoupper('Lider')) {
            $and = " and presupuestos.lider_proyecto = $id";
        } else {
            $and = "";
        }
        $where=($id_proyecto!=null?'where presupuestos.id='.$id_proyecto:'');

        $sql = "SELECT presupuestos.*,u.nickname from (SELECT pmo_proyecto.id,pmo_proyecto.nombre as nombre_proyecto,recurso_id,codigo as recurso,inicio,fin,dias,presupuesto,presupuesto_actual,pmo_proyecto.lider_proyecto,pmo_proyecto.licitacion_id,pmo_proyecto.empresa_id,pmo_proyecto.monto_bolsa, pmo_proyecto.monto_bolsa_iva, vnt_recurso.sucursal_id, pmo_proyecto.nombre_corto
        from pmo_proyecto join vnt_recurso on vnt_recurso.id=pmo_proyecto.recurso_id
        order by pmo_proyecto.id DESC) as presupuestos join sys_users u
        on u.id = presupuestos.lider_proyecto ".$where.$and;

        $proyectos = $this->content['proyectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAll ()
    {
        $sql = "SELECT presupuestos.*,u.nickname 
                from (SELECT pmo_proyecto.id,pmo_proyecto.nombre as nombre_proyecto,recurso_id,vnt_recurso.codigo as recurso,inicio,fin,dias,presupuesto,presupuesto_actual,pmo_proyecto.lider_proyecto,pmo_proyecto.licitacion_id,pmo_proyecto.empresa_id,vnt_clientes.razon_social, pmo_proyecto.monto_bolsa, pmo_proyecto.monto_bolsa_iva, vnt_recurso.sucursal_id, pmo_proyecto.nombre_corto
                    from pmo_proyecto 
                    join vnt_recurso on vnt_recurso.id=pmo_proyecto.recurso_id 
                    join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id order by pmo_proyecto.id DESC) as presupuestos 
                join sys_users u on u.id = presupuestos.lider_proyecto";
        $this->content['proyectos'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByRecurso ($id)
    {
        $and = "";
        if (intval($id) > 0) {
            $and = " and vnt_recurso.id = $id";
        }
        $sql = "SELECT pmo_proyecto.id as value,pmo_proyecto.nombre as label
                    from pmo_proyecto 
                    join vnt_recurso on vnt_recurso.id=pmo_proyecto.recurso_id $and";
        $proyectos = $this->content['proyectos'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getProyectosPerfiles ($year) {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $cuenta = $validUser->account_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $year_anterior = intval($year) - 1;
        
        if(strtoupper($role)===strtoupper('root') || strtoupper($role)===strtoupper('admin') || strtoupper($role) === strtoupper('finanzas') || strtoupper($role) === strtoupper('compras') || strtoupper($role) === strtoupper('finanzas/comisiones')) {
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto  inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and (vnt_recurso.year = '$year' or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))  or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
                ) order by nombre";
        } else {
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id
            and usuario_id = $id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and (vnt_recurso.year = '$year' or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year')))

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_solicitantes on pmo_proyecto.id = pmo_solicitantes.proyecto_id
            and usuario_id = $id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and (vnt_recurso.year = '$year' or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year')))

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id
            and usuario_id = $id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and (vnt_recurso.year = '$year' or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year')))

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_responsable_pagos on pmo_proyecto.id = pmo_responsable_pagos.proyecto_id
            and usuario_id = $id
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and (vnt_recurso.year = '$year' or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (EXISTS (SELECT * FROM fin_solicitudes WHERE fin_solicitudes.proyecto_id = pmo_proyecto.id and pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year')))
            order by nombre";
        }

        $proyectos = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($proyectos as $proyecto) {
            $array['label'] = '' . $proyecto['nombre'];
            $array['value'] = $proyecto['id'];
            array_push($niveles, $array);
        }
        $this->content['proyectos'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getProyectosPerfilesByRecurso ($recurso) {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $cuenta = $validUser->account_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $and = "";
        if (intval($recurso) > 0) {
            $and = " and vnt_recurso.id = $recurso";
        }

        if(strtoupper($role)===strtoupper('admin') || strtoupper($role)===strtoupper('root') || strtoupper($role)===strtoupper('gerente')){
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id
            {$and}";
        } else {
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id
            inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id
            and pmo_colaboradores.usuario_id = $id {$and}

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_solicitantes on pmo_proyecto.id = pmo_solicitantes.proyecto_id
            inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id
            and usuario_id = $id {$and}

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id
            inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id
            and usuario_id = $id {$and}

            union

            select pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_responsable_pagos on pmo_proyecto.id = pmo_responsable_pagos.proyecto_id
            inner join vnt_recurso on pmo_proyecto.recurso_id = vnt_recurso.id
            and usuario_id = $id {$and}";
        }

        $proyectos = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($proyectos as $proyecto) {
            $array['label'] = '' . $proyecto['nombre'];
            $array['value'] = $proyecto['id'];
            array_push($niveles, $array);
        }
        $this->content['proyectos'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getProyectosColaborador ($year) {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $year_anterior = intval($year) - 1;
        $role = SysGrants::findFirst("user_id=$id")->SysRoles->name;

        if ($role == 'Admin' || $role == 'Compras') {
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.status = 'ACTIVO' and ((vnt_recurso.year = '$year' and pmo_proyecto.finalizado = false) or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (vnt_recurso.year = '2021' and pmo_proyecto.finalizado = false)) order by nombre";
        } else {
            $sql = "SELECT pmo_proyecto.id, pmo_proyecto.nombre from pmo_proyecto 
            inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id
            and usuario_id = $id and pmo_proyecto.status = 'ACTIVO'
            inner join vnt_recurso on vnt_recurso.id = pmo_proyecto.recurso_id and ((vnt_recurso.year = '$year' and pmo_proyecto.finalizado = false) or (vnt_recurso.year = '$year_anterior' and pmo_proyecto.finalizado = false) or (vnt_recurso.year = '2021' and pmo_proyecto.finalizado = false)) order by nombre";
        }

        $proyectos = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($proyectos as $proyecto) {
            $array['label'] = '' . $proyecto['nombre'];
            $array['value'] = $proyecto['id'];
            array_push($niveles, $array);
        }
        $this->content['proyectos'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getUsuariosByPresupuesto ($proyecto_id) {
        $sql = "SELECT sys_users.id, sys_users.nickname
                from sys_users
                inner join pmo_colaboradores on pmo_colaboradores.usuario_id = sys_users.id
                and pmo_colaboradores.proyecto_id = $proyecto_id
                union
                select sys_users.id, sys_users.nickname
                from sys_users
                inner join pmo_solicitantes on pmo_solicitantes.usuario_id = sys_users.id
                and pmo_solicitantes.proyecto_id = $proyecto_id
                union
                select sys_users.id, sys_users.nickname
                from sys_users
                inner join pmo_autorizadores on pmo_autorizadores.usuario_id = sys_users.id
                and pmo_autorizadores.proyecto_id = $proyecto_id
                union
                select sys_users.id, sys_users.nickname
                from sys_users
                inner join pmo_responsable_pagos on pmo_responsable_pagos.usuario_id = sys_users.id
                and pmo_responsable_pagos.proyecto_id = $proyecto_id";
        $users = $this->db->query($sql)->fetchAll();
        $arr = [];
        foreach ($users as $user) {
            $array['label'] = '' . $user['nickname'];
            $array['value'] = $user['id'];
            array_push($arr, $array);
        }
        $this->content['users'] = $arr;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $recurso = Recurso::findFirst($request['recurso_id']);
            $cliente_id = $recurso->cliente_id;
            $subprograma_id = $recurso->subprograma_id;

            $cliente = VntClientes::findFirst($cliente_id);
            $subprograma = Subprograma::findFirst($subprograma_id);

            $array_codigo = explode("-",$recurso->codigo);

            if(count($array_codigo) === 4) {

                if($cliente->codigo !== "" && $subprograma->codigo !=="" && $array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo) {
                    if(trim($request['nombre_proyecto'])!=='') {
                        $proyectos = new Proyecto();
                        $proyectos->setTransaction($tx);
                        $proyectos->year = $request['year'];
                        $proyectos->nombre = trim(strtoupper($request['nombre_proyecto']));
                        $proyectos->nombre_corto = trim(strtoupper($request['nombre_corto']));
                        $proyectos->recurso_id = intval($request['recurso_id']);
                        if($request['inicio'] === ""){
                            $proyectos->inicio = null;
                        } else {
                            $proyectos->inicio = date("Y/m/d", strtotime($request['inicio']));
                        }
                        if($request['fin'] === ""){
                            $proyectos->fin= null;
                        } else {
                            $proyectos->fin= date("Y/m/d", strtotime($request['fin']));
                        }
                        $proyectos->dias = intval($request['dias']);
                        $proyectos->presupuesto = floatval($request['presupuesto']);
                        $proyectos->presupuesto_actual = floatval($request['presupuesto']);
                        $proyectos->monto_bolsa = floatval($request['monto_bolsa_validado']);
                        $proyectos->lider_proyecto = intval($request['lider_proyecto']);
                        if (intval($request['licitacion_id']) > 0) {
                            $proyectos->licitacion_id = intval($request['licitacion_id']);
                        } else {
                            $proyectos->licitacion_id = null;
                        }
                        if (intval($request['empresa_id']) > 0) {
                            $proyectos->empresa_id = intval($request['empresa_id']);
                        } else {
                            $proyectos->empresa_id = null;
                        }

                        if ($proyectos->create()) {
                            $logs = new Logs();
                            $logs->setTransaction($tx);
                            $validUser = Auth::getUserData($this->config);
                            $logs->account_id = $validUser->user_id;
                            $logs->level_id = 8;
                            $logs->log = 'CREÓ EL PROJECT: ' . trim(strtoupper($request['nombre_proyecto']));
                            $logs->fecha = date("Y-m-d H:i:s");
                            if($logs->create()){
                              $this->content['result']='success';
                              $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha creado el project.'];
                              $tx->commit();
                            } else {
                              $this->content['result']='success';
                              $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud pero sin guardar el registro en logs.']; 
                              $tx->commit();
                            }
                            // $this->content['result'] = 'success';
                            // $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el project.'];
                            // $tx->commit();
                        } else {
                            $this->content['error'] = Helpers::getErrors($proyectos);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el project.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El campo del nombre del project esta vacío'];
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
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

            $recurso = Recurso::findFirst($request['recurso_id']);
            $cliente_id = $recurso->cliente_id;
            $subprograma_id = $recurso->subprograma_id;

            $cliente = VntClientes::findFirst($cliente_id);
            $subprograma = Subprograma::findFirst($subprograma_id);

            $array_codigo = explode("-",$recurso->codigo);

            if(count($array_codigo) === 4) {

                if($cliente->codigo !== "" && $subprograma->codigo !=="" && $array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo) {
                    if(trim($request['nombre_proyecto'])!=='') {
                        $proyectos = Proyecto::findFirst($request['id']);
                        if ($proyectos) {
                            $proyectos->setTransaction($tx);

                            $proyectos->nombre = trim(strtoupper($request['nombre_proyecto']));
                            $proyectos->nombre_corto = trim(strtoupper($request['nombre_corto']));
                            $recurso_anterior = $proyectos->recurso_id;
                            $recurso_actual = intval($request['recurso_id']);
                            $codigo_recurso_actual = Recurso::findFirst($request['recurso_id']);
                            $proyectos->recurso_id = intval($request['recurso_id']);
                            if (floatval($proyectos->presupuesto) !== floatval($request['presupuesto'])) {
                                // var_dump(floatval($proyectos->presupuesto));
                                // var_dump($request['presupuesto']);
                                $proyectos->presupuesto = floatval($request['presupuesto']);
                                // $proyectos->presupuesto_actual = floatval($request['presupuesto']);
                            }
                            if($request['inicio'] === ""){
                                $proyectos->inicio = null;
                            } else {
                                $proyectos->inicio = date("Y/m/d", strtotime($request['inicio']));
                            }
                            if($request['fin'] === ""){
                                $proyectos->fin= null;
                            } else {
                                $proyectos->fin= date("Y/m/d", strtotime($request['fin']));
                            }

                            $proyectos->dias = intval($request['dias']);
                            // $proyectos->presupuesto = floatval($request['presupuesto']);
                            $proyectos->lider_proyecto= intval($request['lider_proyecto']);
                            $proyectos->monto_bolsa = floatval($request['monto_bolsa_validado']);
                            $proyectos->monto_bolsa_iva = floatval($request['monto_bolsa_validado_iva']);
                            $validUser = Auth::getUserData($this->config);
                            $user = SysUsers::findFirst($validUser->user_id)->toArray();
                            $userid= $validUser->account_id;
                            if (intval($request['licitacion_id']) > 0) {
                            $proyectos->licitacion_id = intval($request['licitacion_id']);
                            } else {
                                $proyectos->licitacion_id = null;
                            }
                            if (intval($request['empresa_id']) > 0) {
                                $proyectos->empresa_id = intval($request['empresa_id']);
                            } else {
                                $proyectos->empresa_id = null;
                            }
                            // if(intval($user['nickname']=='sa') || $userid==intval($request['lider_proyecto'])){
                                if ($proyectos->update()) {
                                    if ($recurso_anterior != $recurso_actual) {
                                        $logs = new Logs();
                                        $logs->setTransaction($tx);
                                        $validUser = Auth::getUserData($this->config);
                                        $logs->account_id = $validUser->user_id;
                                        $logs->level_id = 11;
                                        $logs->log = "CAMBIÓ EL PROJECT '" . $proyectos->nombre ."' DEL PROYECTO '" . $recurso->codigo . "' AL '" . $codigo_recurso_actual->codigo . "'";
                                        $logs->fecha = date("Y-m-d H:i:s");
                                        if($logs->create()){
                                          $this->content['result'] = 'success';
                                          $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el project.'];
                                          $tx->commit();
                                        } else {
                                          $this->content['result'] = 'success';
                                          $this->content['message'] = ['title' => '¡Exito!', 'content' => 'No se pudo actualizar el project.'];
                                          $tx->commit();
                                        }

                                    } else {
                                        $this->content['result'] = 'success';
                                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el project.'];
                                        $tx->commit();
                                    }
                                } else {
                                    $this->content['error'] = Helpers::getErrors($proyectos);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el project'];
                                    $tx->rollback();
                                }
                            /* } else {
                                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Solo el lider del project puede modificar los valores'];
                            } */                
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El campo del nombre del project esta vacío'];
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateMontos () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $projects = Proyecto::find();
            if ($projects) {
                foreach ($projects as $project) {
                    $id = $project->id;
                    $sql = "(SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = $id)";
                    $data = $this->db->query($sql)->fetchAll();

                    $proyectos = Proyecto::findFirst($id);
                    $proyectos->setTransaction($tx);
                    $proyectos->presupuesto_actual = $data[0]['presupuesto_actividad_principal'];
                    if ($proyectos->update()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($proyectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el monto del project'];
                        $tx->rollback();
                    }
                }
                
                if ($this->content['result'] === 'success') {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han actualizado los projects.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proyectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status del project'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateStatus () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $proyectos = Proyecto::findFirst($request['id']);
            if ($proyectos) {
                $proyectos->setTransaction($tx);
                $proyectos->status = $request['status'];
                if ($proyectos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el status del project.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proyectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el status del project'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateFinalizado () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $proyectos = Proyecto::findFirst($request['id']);
            if ($proyectos) {
                $proyectos->setTransaction($tx);
                $finalizado = $proyectos->finalizado;
                if ($finalizado == true) {
                    $proyectos->finalizado = false;
                } else {
                    $proyectos->finalizado = true;
                }
                if ($proyectos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el project.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proyectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el project.'];
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
                    $proyectos = Proyecto::findFirst($request['id']);
                    $proyectos->setTransaction($tx);

                    if ($proyectos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($proyectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el project.'];
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