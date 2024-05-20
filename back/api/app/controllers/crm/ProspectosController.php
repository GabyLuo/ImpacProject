<?php

use Phalcon\Mvc\Controller;

class ProspectosController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll () {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $sql = "SELECT id, nombre_compania, nombre_comercial, giro_comercial, producto_id, rfc, razon_social, dias_credito, abreviatura, nombre_contacto, email, telefono, calle, colonia, poblacion, numero_exterior, numero_interior, codigo_postal, estado_id, municipio_id, acta_representante, acta_estado, acta_notaria, acta_notario, acta_fecha, acta_libro, acta_rpp, acta_giro, ejecutivo_id, tipo, created, created_by, modified, modified_by, (select vnt_estado.nombre from vnt_estado where id = estado_id) as estado, (select vnt_municipio.nombre from vnt_municipio where id = municipio_id) as municipio FROM crm_prospectos order by id";
        $this->content['prospectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getById ($id) {
        $sql = "SELECT * 
            FROM crm_prospectos where id = $id";
        $this->content['prospectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy ($id) {
        $sql = "SELECT * 
            FROM crm_prospectos where id = $id";
        $this->content['prospectos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $prospectos = Prospectos::findFirst(
                [
                    'nombre_comercial = :nombre_comercial:',
                    'bind' => [
                        'nombre_comercial' => trim($request['nombre_comercial'])
                    ]
                ]
            );
            if ($prospectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe este prospecto.'];
            } else {
                $validUser = Auth::getUserData($this->config);
                $id = $validUser->user_id;

                $nombre_compania = trim($request['nombre_compania']);
                $giro_comercial = intval($request['giro_comercial']);
                $producto_id = intval($request['producto_id']);
                $rfc = trim($request['rfc']);
                $razon_social = trim($request['razon_social']);
                $dias_credito = intval($request['dias_credito']);
                $abreviatura = trim($request['abreviatura']);
                $nombre_contacto = trim($request['nombre_contacto']);
                $email = trim($request['email']);
                $telefono = trim($request['telefono']);
                $calle = trim($request['calle']);
                $colonia = trim($request['colonia']);
                $poblacion = trim($request['poblacion']);
                $numero_exterior = trim($request['numero_exterior']);
                $numero_interior = trim($request['numero_interior']);
                $codigo_postal = trim($request['codigo_postal']);
                $estado_id = intval($request['estado_id']);
                $municipio_id = intval($request['municipio_id']);
                $acta_representante = trim($request['acta_representante']);
                $acta_estado = intval($request['acta_estado']);
                $acta_notaria = intval($request['acta_notaria']);
                $acta_notario = trim($request['acta_notario']);
                $acta_fecha = $request['acta_fecha'];
                $acta_libro = intval($request['acta_libro']);
                $acta_rpp = trim($request['acta_rpp']);
                $acta_giro = trim($request['acta_giro']);

                $prospectos = new Prospectos();
                $prospectos->setTransaction($tx);
                $prospectos->nombre_compania = ($nombre_compania == '') ? null : $nombre_compania;
                $prospectos->nombre_comercial = trim($request['nombre_comercial']);
                $prospectos->giro_comercial = ($giro_comercial > 0) ? $giro_comercial : null;
                $prospectos->producto_id = ($producto_id > 0) ? $producto_id : null;
                $prospectos->rfc = ($rfc == '') ? null : $rfc;
                $prospectos->razon_social = ($razon_social == '') ? null : $razon_social;
                $prospectos->dias_credito = ($dias_credito > 0) ? $dias_credito : null;
                $prospectos->abreviatura = ($abreviatura == '') ? null : $abreviatura;
                $prospectos->nombre_contacto = ($nombre_contacto == '') ? null : $nombre_contacto;
                $prospectos->email = ($email == '') ? null : $email;
                $prospectos->telefono = ($telefono == '') ? null : $telefono;
                $prospectos->calle = ($calle == '') ? null : $calle;
                $prospectos->colonia = ($colonia == '') ? null : $colonia;
                $prospectos->poblacion = ($poblacion == '') ? null : $poblacion;
                $prospectos->numero_exterior = ($numero_exterior == '') ? null : $numero_exterior;
                $prospectos->numero_interior = ($numero_interior == '') ? null : $numero_interior;
                $prospectos->codigo_postal = ($codigo_postal == '') ? null : $codigo_postal;
                $prospectos->estado_id = ($estado_id > 0) ? $estado_id : null;
                $prospectos->municipio_id = ($municipio_id > 0) ? $municipio_id : null;
                $prospectos->acta_representante = ($acta_representante == '') ? null : $acta_representante;
                $prospectos->acta_estado = ($acta_estado > 0) ? $acta_estado : null;
                $prospectos->acta_notaria = ($acta_notaria > 0) ? $acta_notaria : null;
                $prospectos->acta_notario = ($acta_notario == '') ? null : $acta_notario;
                $prospectos->acta_fecha = ($acta_fecha == '') ? null : $acta_fecha;
                $prospectos->acta_libro = ($acta_libro > 0) ? $acta_libro : null;
                $prospectos->acta_rpp = ($acta_rpp == '') ? null : $acta_rpp;
                $prospectos->acta_giro = ($acta_giro == '') ? null : $acta_giro;
                $prospectos->ejecutivo_id = $id;

                if ($prospectos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el prospecto.'];
                    $this->content['prospecto_id'] = $prospectos->id;
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($prospectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el prospecto.'];
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
            $prospectos = Prospectos::findFirst(
                [
                    'id != :id: AND nombre_comercial = :nombre_comercial:',
                    'bind' => [
                        'nombre_comercial' => trim($request['nombre_comercial']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($prospectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un prospecto con este nombre'];
            } else {
                $validUser = Auth::getUserData($this->config);
                $id = $validUser->user_id;

                $nombre_compania = trim($request['nombre_compania']);
                $giro_comercial = intval($request['giro_comercial']);
                $producto_id = intval($request['producto_id']);
                $rfc = trim($request['rfc']);
                $razon_social = trim($request['razon_social']);
                $dias_credito = intval($request['dias_credito']);
                $abreviatura = trim($request['abreviatura']);
                $nombre_contacto = trim($request['nombre_contacto']);
                $email = trim($request['email']);
                $telefono = trim($request['telefono']);
                $calle = trim($request['calle']);
                $colonia = trim($request['colonia']);
                $poblacion = trim($request['poblacion']);
                $numero_exterior = trim($request['numero_exterior']);
                $numero_interior = trim($request['numero_interior']);
                $codigo_postal = trim($request['codigo_postal']);
                $estado_id = intval($request['estado_id']);
                $municipio_id = intval($request['municipio_id']);
                $acta_representante = trim($request['acta_representante']);
                $acta_estado = intval($request['acta_estado']);
                $acta_notaria = intval($request['acta_notaria']);
                $acta_notario = trim($request['acta_notario']);
                $acta_fecha = $request['acta_fecha'];
                $acta_libro = intval($request['acta_libro']);
                $acta_rpp = trim($request['acta_rpp']);
                $acta_giro = trim($request['acta_giro']);

                $prospectos = Prospectos::findFirst($request['id']);
                if ($prospectos) {
                    $prospectos->setTransaction($tx);
                    $prospectos->nombre_compania = ($nombre_compania == '') ? null : $nombre_compania;;
                    $prospectos->nombre_comercial = trim($request['nombre_comercial']);
                    $prospectos->giro_comercial = ($giro_comercial > 0) ? $giro_comercial : null;
                    $prospectos->producto_id = ($producto_id > 0) ? $producto_id : null;
                    $prospectos->rfc = ($rfc == '') ? null : $rfc;
                    $prospectos->razon_social = ($razon_social == '') ? null : $razon_social;
                    $prospectos->dias_credito = ($dias_credito > 0) ? $dias_credito : null;
                    $prospectos->abreviatura = ($abreviatura == '') ? null : $abreviatura;
                    $prospectos->nombre_contacto = ($nombre_contacto == '') ? null : $nombre_contacto;
                    $prospectos->email = ($email == '') ? null : $email;
                    $prospectos->telefono = ($telefono == '') ? null : $telefono;
                    $prospectos->calle = ($calle == '') ? null : $calle;
                    $prospectos->colonia = ($colonia == '') ? null : $colonia;
                    $prospectos->poblacion = ($poblacion == '') ? null : $poblacion;
                    $prospectos->numero_exterior = ($numero_exterior == '') ? null : $numero_exterior;
                    $prospectos->numero_interior = ($numero_interior == '') ? null : $numero_interior;
                    $prospectos->codigo_postal = ($codigo_postal == '') ? null : $codigo_postal;
                    $prospectos->estado_id = ($estado_id > 0) ? $estado_id : null;
                    $prospectos->municipio_id = ($municipio_id > 0) ? $municipio_id : null;
                    $prospectos->acta_representante = ($acta_representante == '') ? null : $acta_representante;
                    $prospectos->acta_estado = ($acta_estado > 0) ? $acta_estado : null;
                    $prospectos->acta_notaria = ($acta_notaria > 0) ? $acta_notaria : null;
                    $prospectos->acta_notario = ($acta_notario == '') ? null : $acta_notario;
                    $prospectos->acta_fecha = ($acta_fecha == '') ? null : $acta_fecha;
                    $prospectos->acta_libro = ($acta_libro > 0) ? $acta_libro : null;
                    $prospectos->acta_rpp = ($acta_rpp == '') ? null : $acta_rpp;
                    $prospectos->acta_giro = ($acta_giro == '') ? null : $acta_giro;

                    // var_dump($request);
                    // $prospectos->ejecutivo_id = $id;

                    if ($prospectos->update()) {
                        //
                        $prospect_id = $request['cultivo']['prospecto_id'];
                        $anio_administracion = intval($request['cultivo']['anio_administracion']);
                        $ciudad_origen = intval($request['cultivo']['ciudad_origen']);
                        $estado_origen = intval($request['cultivo']['estado_origen']);
                        $cita_confirmada = trim($request['cultivo']['cita_confirmada']);
                        $acreditaciones = trim($request['cultivo']['acreditaciones']);
                        $toma_desiciones = trim($request['cultivo']['toma_desiciones']);
                        $consideraciones = trim($request['cultivo']['consideraciones']);
                        $contrato_proveedores = trim($request['cultivo']['contrato_proveedores']);
                        $enfoque_cierre = trim($request['cultivo']['enfoque_cierre']);
                        $esquema = trim($request['cultivo']['esquema']);
                        $fecha_cumpleanios = trim($request['cultivo']['fecha_cumpleanios']);
                        $monto_asignado = intval($request['cultivo']['monto_asignado']);
                        $partido_politico = trim($request['cultivo']['partido_politico']);
                        $contacto = trim($request['cultivo']['contacto']);
                        $informacion_enviada = trim($request['cultivo']['informacion_enviada']);
                        $necesidades = trim($request['cultivo']['necesidades']);
                        $taller = trim($request['cultivo']['taller']);
                        $tipo_recurso = trim($request['cultivo']['tipo_recurso']);
                        $tipo_servicio = trim($request['cultivo']['tipo_servicio']);
                        $prospecto_id = intval($request['cultivo']['prospecto_id']);
                        $cultivo = Cultivos::findFirst('prospecto_id='.$prospect_id);
                        if ($cultivo) {
                            $cultivo->setTransaction($tx);
                            $cultivo->anio_administracion = ($anio_administracion == '') ? null : $anio_administracion;
                            $cultivo->ciudad_origen = ($ciudad_origen == '') ? null : $ciudad_origen;
                            $cultivo->estado_origen = ($estado_origen == '') ? null : $estado_origen;
                            $cultivo->cita_confirmada = ($cita_confirmada == '') ? null : $cita_confirmada;
                            $cultivo->acreditaciones = ($acreditaciones == '') ? null : $acreditaciones;
                            $cultivo->toma_desiciones = ($toma_desiciones == '') ? null : $toma_desiciones;
                            $cultivo->consideraciones = ($consideraciones == '') ? null : $consideraciones;
                            $cultivo->contrato_proveedores = ($contrato_proveedores == '') ? null : $contrato_proveedores;
                            $cultivo->enfoque_cierre = ($enfoque_cierre == '') ? null : $enfoque_cierre;
                            $cultivo->esquema = ($esquema == '') ? null : $esquema;
                            $cultivo->fecha_cumpleanios = ($fecha_cumpleanios == '') ? null : $fecha_cumpleanios;
                            $cultivo->monto_asignado = ($monto_asignado == '') ? null : $monto_asignado;
                            $cultivo->partido_politico = ($partido_politico == '') ? null : $partido_politico;
                            $cultivo->contacto = ($contacto == '') ? null : $contacto;
                            $cultivo->informacion_enviada = ($informacion_enviada == '') ? null : $informacion_enviada;
                            $cultivo->necesidades  = ($necesidades == '') ? null : $necesidades;
                            $cultivo->taller  = ($taller == '') ? null : $taller;
                            $cultivo->tipo_recurso = ($tipo_recurso == '') ? null : $tipo_recurso;
                            $cultivo->tipo_servicio  = ($tipo_servicio == '') ? null : $tipo_servicio;
                            $cultivo->prospecto_id  = ($prospecto_id == '') ? null : $prospecto_id;
                            if ($cultivo->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($cultivo);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el prospecto'];
                                $tx->rollback();
                            }
                        } else {
                            $cultivo = new Cultivos();
                            $cultivo->setTransaction($tx);
                            $cultivo->anio_administracion = ($anio_administracion == '') ? null : $anio_administracion;
                            $cultivo->ciudad_origen = ($ciudad_origen == '') ? null : $ciudad_origen;
                            $cultivo->estado_origen = ($estado_origen == '') ? null : $estado_origen;
                            $cultivo->cita_confirmada = ($cita_confirmada == '') ? null : $cita_confirmada;
                            $cultivo->acreditaciones = ($acreditaciones == '') ? null : $acreditaciones;
                            $cultivo->toma_desiciones = ($toma_desiciones == '') ? null : $toma_desiciones;
                            $cultivo->consideraciones = ($consideraciones == '') ? null : $consideraciones;
                            $cultivo->contrato_proveedores = ($contrato_proveedores == '') ? null : $contrato_proveedores;
                            $cultivo->enfoque_cierre = ($enfoque_cierre == '') ? null : $enfoque_cierre;
                            $cultivo->esquema = ($esquema == '') ? null : $esquema;
                            $cultivo->fecha_cumpleanios = ($fecha_cumpleanios == '') ? null : $fecha_cumpleanios;
                            $cultivo->monto_asignado = ($monto_asignado == '') ? null : $monto_asignado;
                            $cultivo->partido_politico = ($partido_politico == '') ? null : $partido_politico;
                            $cultivo->contacto = ($contacto == '') ? null : $contacto;
                            $cultivo->informacion_enviada = ($informacion_enviada == '') ? null : $informacion_enviada;
                            $cultivo->necesidades  = ($necesidades == '') ? null : $necesidades;
                            $cultivo->taller  = ($taller == '') ? null : $taller;
                            $cultivo->tipo_recurso = ($tipo_recurso == '') ? null : $tipo_recurso;
                            $cultivo->tipo_servicio  = ($tipo_servicio == '') ? null : $tipo_servicio;
                            $cultivo->prospecto_id  = ($prospecto_id == '') ? null : $prospecto_id;
                            if ($cultivo->create()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($cultivo);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el prospecto'];
                                $tx->rollback();
                            }
                        }
                        //
                        if ($this->content['result'] === 'success') {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el prospecto'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($prospectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el prospecto'];
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
                    $prospectos = Prospectos::findFirst($request['id']);
                    $prospectos->setTransaction($tx);
                    if ($prospectos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($prospectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el prospecto.'];
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