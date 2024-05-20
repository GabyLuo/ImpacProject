<?php

use Phalcon\Mvc\Controller;

class CultivosController extends Controller
{
    public $content = ['result' => 'error', 'message' => ''];

    public function getAll() 
    {
            $sql = "SELECT * FROM crm_cultivos";
            $this->content['cultivo'] = $this->db->query($sql)->fetchAll();
            $this->content['result'] = 'success';
            $this->response->setJsonContent($this->content);
            $this->response->send();
    }

    public function getById($id) 
    {
        $sql = "SELECT *
            FROM crm_cultivos where prospecto_id = $id";
        $v = $this->db->query($sql)->fetchAll();
        $this->content['cultivo'] = $v;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $anio_administracion = intval($request['anio_administracion']);
            $ciudad_origen = intval($request['ciudad_origen']);
            $estado_origen = intval($request['estado_origen']);
            $cita_confirmada = trim($request['cita_confirmada']);
            $acreditaciones = trim($request['acreditaciones']);
            $toma_desiciones = trim($request['toma_desiciones']);
            $consideraciones = trim($request['consideraciones']);
            $contrato_proveedores = trim($request['contrato_proveedores']);
            $enfoque_cierre = trim($request['enfoque_cierre']);
            $esquema = trim($request['esquema']);
            $fecha_cumpleanios = trim($request['fecha_cumpleanios']);
            $monto_asignado = intval($request['monto_asignado']);
            $partido_politico = trim($request['partido_politico']);
            $contacto = trim($request['contacto']);
            $informacion_enviada = trim($request['informacion_enviada']);
            $necesidades = trim($request['necesidades']);
            $taller = trim($request['taller']);
            $tipo_recurso = trim($request['tipo_recurso']);
            $tipo_servicio = trim($request['tipo_servicio']);
            $prospecto_id = intval($request['prospecto_id']);

            $cultivos = new Cultivos();
            $cultivos->setTransaction($tx);
            $cultivos->anio_administracion = ($anio_administracion == '') ? null : $anio_administracion;
            $cultivos->ciudad_origen = ($ciudad_origen == '') ? null : $ciudad_origen;
            $cultivos->estado_origen = ($estado_origen == '') ? null : $estado_origen;
            $cultivos->cita_confirmada = ($cita_confirmada == '') ? null : $cita_confirmada;
            $cultivos->acreditaciones = ($acreditaciones == '') ? null : $acreditaciones;
            $cultivos->toma_desiciones = ($toma_desiciones == '') ? null : $toma_desiciones;
            $cultivos->consideraciones = ($consideraciones == '') ? null : $consideraciones;
            $cultivos->contrato_proveedores = ($contrato_proveedores == '') ? null : $contrato_proveedores;
            $cultivos->enfoque_cierre = ($enfoque_cierre == '') ? null : $enfoque_cierre;
            $cultivos->esquema = ($esquema == '') ? null : $esquema;
            $cultivos->fecha_cumpleanios = ($fecha_cumpleanios == '') ? null : $fecha_cumpleanios;
            $cultivos->monto_asignado = ($monto_asignado == '') ? null : $monto_asignado;
            $cultivos->partido_politico = ($partido_politico == '') ? null : $partido_politico;
            $cultivos->contacto = ($contacto == '') ? null : $contacto;
            $cultivos->informacion_enviada = ($informacion_enviada == '') ? null : $informacion_enviada;
            $cultivos->necesidades  = ($necesidades == '') ? null : $necesidades;
            $cultivos->taller  = ($taller == '') ? null : $taller;
            $cultivos->tipo_recurso = ($tipo_recurso == '') ? null : $tipo_recurso;
            $cultivos->tipo_servicio  = ($tipo_servicio == '') ? null : $tipo_servicio;
            $cultivos->prospecto_id  = ($prospecto_id == '') ? null : $prospecto_id;

            if ($cultivos->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha guardado la información.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($cultivos);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar la información.'];
                $tx->rollback();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
}
