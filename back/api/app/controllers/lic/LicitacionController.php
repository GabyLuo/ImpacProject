<?php

use Phalcon\Mvc\Controller;

class LicitacionController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT lic_licitacion.id,lic_licitacion.recurso_id,vnt_recurso.codigo as recurso,
                lic_licitacion.empresa_id,vnt_empresa.razon_social as empresa,lic_licitacion.fecha_inicio,lic_licitacion.fecha_fallo,lic_licitacion.fecha_presentacion,
                lic_licitacion.folio,lic_licitacion.titulo,lic_licitacion.status, lic_licitacion.entidad_id, lic_licitacion.descripcion, lic_licitacion.contrato, lic_licitacion.monto_inicial, lic_licitacion.monto_adjudicado, lic_licitacion.orden_compra, lic_licitacion.campo_anticipo, lic_licitacion.porcentaje_anticipo, lic_licitacion.monto_anticipo, lic_licitacion.comprador, lic_licitacion.documento_final, lic_licitacion.extension, lic_licitacion.responsable, lic_licitacion.responsable_gdp
                from lic_licitacion join vnt_recurso on lic_licitacion.recurso_id = vnt_recurso.id
                 left join vnt_empresa on vnt_empresa.id = lic_licitacion.empresa_id
                order by lic_licitacion.id DESC";
        $licitaciones = $this->db->query($sql)->fetchAll();
        $nuevo = [];
        foreach ($licitaciones as $elemento){
            $l=(object)array();
            $l->id = $elemento['id'];
            $l->recurso_id = $elemento['recurso_id'];
            $l->recurso = $elemento['recurso'];
            $l->empresa_id = $elemento['empresa_id'];
            $l->empresa = $elemento['empresa'];
            $l->fecha_inicio = $elemento['fecha_inicio'];
            $l->fecha_presentacion = $elemento['fecha_presentacion'];
            $l->fecha_fallo = $elemento['fecha_fallo'];
            $l->folio = $elemento['folio'];
            $l->titulo = $elemento['titulo'];
            $l->status = $elemento['status'];
            $l->entidad_id = $elemento['entidad_id'];
            $l->descripcion = $elemento['descripcion'];
            $l->contrato = $elemento['contrato'];
            $l->orden_compra = $elemento['orden_compra'];
            $l->monto_inicial = $elemento['monto_inicial'];
            $l->monto_adjudicado = $elemento['monto_adjudicado'];
            $l->porcentaje_anticipo = $elemento['porcentaje_anticipo'];
            $l->monto_anticipo = $elemento['monto_anticipo'];
            $l->campo_anticipo = $elemento['campo_anticipo'];
            $l->comprador = $elemento['comprador'];
            $l->responsable = $elemento['responsable'];
            $l->responsable_gdp = $elemento['responsable_gdp'];
            array_push($nuevo,$l);
        }
        $this->content['licitaciones'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getEmpresaConcursanteByIdLicitacion ($id) {
        $sql = "SELECT lic_licitacion.empresa_id, vnt_empresa.razon_social
                FROM lic_licitacion
                inner join vnt_empresa on lic_licitacion.empresa_id = vnt_empresa.id
                and lic_licitacion.id = $id";

        $empresa = $this->db->query($sql)->fetchAll();
        $this->content['empresa'] = $empresa;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function filtrar ()
    {
        $tx = $this->transactions->get();
        $request = $this->request->getPost();

        $fecha_inicio = $request['fecha_inicio'];
        $fecha_fallo = $request['fecha_fallo'];
        $status = $request['status'];
        $year = $request['year'];
        // var_dump($request);

        if ($fecha_inicio !== 'i') {
            $fecha_inicio = "'" .date('Y-m-d', strtotime($request['fecha_inicio'])) . "'";
        }
        if ($fecha_fallo !== 'i') {
            $fecha_fallo = "'" .date('Y-m-d', strtotime($request['fecha_fallo'])) . "'";
        }

        $sql = "SELECT lic_licitacion.id,lic_licitacion.recurso_id,(select vnt_recurso.codigo from vnt_recurso where lic_licitacion.recurso_id = vnt_recurso.id) as recurso, lic_licitacion.empresa_id,vnt_empresa.razon_social as empresa,lic_licitacion.fecha_inicio,lic_licitacion.fecha_fallo,lic_licitacion.fecha_presentacion,lic_licitacion.folio,lic_licitacion.titulo,lic_licitacion.status, lic_licitacion.entidad_id, lic_licitacion.descripcion, lic_licitacion.contrato, lic_licitacion.monto_inicial, lic_licitacion.monto_adjudicado, lic_licitacion.orden_compra, lic_licitacion.porcentaje_anticipo, lic_licitacion.monto_anticipo, lic_licitacion.campo_anticipo, lic_licitacion.comprador, lic_licitacion.documento_final, lic_licitacion.extension, lic_licitacion.empresa, lic_licitacion.observaciones_creada, lic_licitacion.observaciones_proceso, lic_licitacion.observaciones_presentada, lic_licitacion.observaciones_adjudicada, lic_licitacion.observaciones_no_adjudicada, lic_licitacion.observaciones_cancelada, lic_licitacion.responsable, lic_licitacion.responsable_gdp
            from lic_licitacion
            left join vnt_empresa on vnt_empresa.id = lic_licitacion.empresa_id";

        if ($status !== 'Todas') {
            $sql = $sql . " where lic_licitacion.status = '$status'";

            if ($fecha_inicio !== 'i' && $fecha_fallo !== 'i') {
                $sql = $sql . " and (fecha_inicio >= date($fecha_inicio) or fecha_fallo >= date($fecha_fallo))";
            }
            if ($fecha_inicio !== 'i' && $fecha_fallo === 'i') {
                $sql = $sql . " and fecha_inicio >= date($fecha_inicio)";
            }
            if ($fecha_inicio === 'i' && $fecha_fallo !== 'i') {
                $sql = $sql . " and fecha_fallo >= date($fecha_fallo)";
            }
        }
        if ($status === 'Todas') {
            if ($fecha_inicio !== 'i' && $fecha_fallo !== 'i') {
                $sql = $sql . " where (fecha_inicio >= date($fecha_inicio) or fecha_fallo >= date($fecha_fallo))";
            }
            if ($fecha_inicio !== 'i' && $fecha_fallo === 'i') {
                $sql = $sql . " where fecha_inicio >= date($fecha_inicio)";
            }
            if ($fecha_inicio === 'i' && $fecha_fallo !== 'i') {
                $sql = $sql . " where fecha_fallo >= date($fecha_fallo)";
            }
        }
        if ($status === 'Todas' && $fecha_inicio === 'i' && $fecha_fallo === 'i') {
            $sql = $sql . " where lic_licitacion.year = '$year' and lic_licitacion.status_mostrado = 'ACTIVO'";
        } else {
            $sql = $sql . " and lic_licitacion.year = '$year' and lic_licitacion.status_mostrado = 'ACTIVO'";
        }
        $sql = $sql . " order by lic_licitacion.id DESC";

        
        // print_r($sql);

        $this->content['licitaciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByRecurso ($recurso_id)
    {
        $sql = "SELECT lic_licitacion.id,lic_licitacion.recurso_id,(select vnt_recurso.codigo from vnt_recurso where lic_licitacion.recurso_id = vnt_recurso.id) as recurso,
                lic_licitacion.empresa_id,vnt_empresa.razon_social as empresa,lic_licitacion.fecha_inicio,lic_licitacion.fecha_presentacion,
                lic_licitacion.folio,lic_licitacion.titulo,lic_licitacion.fecha_fallo, lic_licitacion.status, lic_licitacion.entidad_id, lic_licitacion.descripcion, lic_licitacion.contrato, lic_licitacion.monto_inicial, lic_licitacion.monto_adjudicado, lic_licitacion.orden_compra, lic_licitacion.porcentaje_anticipo, lic_licitacion.monto_anticipo, lic_licitacion.campo_anticipo, lic_licitacion.comprador
                from lic_licitacion
                left join vnt_empresa on vnt_empresa.id = lic_licitacion.empresa_id
                -- where (lic_licitacion.recurso_id = $recurso_id or lic_licitacion.recurso_id is null)
                order by lic_licitacion.id DESC";
        $this->content['licitaciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function busquedaLicitaciones ($recurso_id,$folio) {
        $recurso = Recurso::findFirst($recurso_id);
        $cliente = VntClientes::findFirst($recurso->cliente_id);

        $licitaciones = Licitacion::find();

        foreach ($licitaciones as $licitacion) {
            $cliente_licitacion = VntClientes::findFirst(Recurso::findFirst($licitacion->recurso_id)->cliente_id);
            if(intval($cliente_licitacion->estado_id) === intval($cliente->estado_id) && strtoupper($licitacion->folio) === strtoupper($folio)){
                return true;
            }
        }
        return false;
    }

    private function busquedaLicitaciones2 ($recurso_id,$folio,$licitacion_id) {
        $recurso = Recurso::findFirst($recurso_id);
        $cliente = VntClientes::findFirst($recurso->cliente_id);

        $licitaciones = Licitacion::find(
            [
                'id != :id:',
                'bind' => [
                    'id' => intval($licitacion_id)
                ]
            ]
        );

        foreach ($licitaciones as $licitacion) {
            $cliente_licitacion = VntClientes::findFirst(Recurso::findFirst($licitacion->recurso_id)->cliente_id);
            if(intval($cliente_licitacion->estado_id) === intval($cliente->estado_id) && strtoupper($licitacion->folio) === strtoupper($folio)){
                return true;
            }
        }
        return false;
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $folio = Licitacion::findFirst(
                [
                    'folio = :folio:',
                    'bind' => [
                        'folio' => strtoupper($request['folio'])
                    ]
                ]
            );
            if (!$folio) {
                $licitaciones = new Licitacion();
                $licitaciones->setTransaction($tx);
                if(intval($request['recurso_id']) === 0) {
                    $licitaciones->recurso_id = null;
                } else {
                    $licitaciones->recurso_id = intval($request['recurso_id']);
                }
                $licitaciones->empresa_id = intval($request['empresa_id']);
                $licitaciones->folio = strtoupper($request['folio']);
                $licitaciones->titulo = strtoupper($request['titulo']);
                $licitaciones->status = $request['status'];
                $licitaciones->entidad_id = intval($request['entidad_id']);
                $licitaciones->descripcion = trim($request['descripcion']);

                $licitaciones->contrato = trim($request['contrato']);
                $licitaciones->monto_inicial = floatval($request['monto_inicial']);
                $licitaciones->monto_adjudicado = floatval($request['monto_adjudicado']);
                $licitaciones->year = $request['year'];

                if($request['campo_anticipo'] === 'SI') {
                    $licitaciones->campo_anticipo = 'SI';
                    $licitaciones->porcentaje_anticipo = floatval($request['porcentaje_anticipo']);
                    $licitaciones->monto_anticipo = floatval($request['monto_anticipo']);
                } else {
                    $licitaciones->campo_anticipo = 'NO';
                    $licitaciones->porcentaje_anticipo = 0;
                    $licitaciones->monto_anticipo = 0;
                }
                if(trim($request['comprador']) === "") {
                    $licitaciones->comprador = null;
                } else {
                    $licitaciones->comprador = trim($request['comprador']);
                }
                if(trim($request['empresa']) === "") {
                    $licitaciones->empresa = null;
                } else {
                    $licitaciones->empresa = trim($request['empresa']);
                }
                if(trim($request['contrato'] === "")) {
                    $licitaciones->contrato = null;
                } else {
                    $licitaciones->contrato = trim($request['contrato']);
                }
                if($request['fecha_inicio'] === "") {
                    $licitaciones->fecha_inicio = null;
                } else {
                    $licitaciones->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
                }
                if($request['fecha_fallo'] === "") {
                    $licitaciones->fecha_fallo = null;
                } else {
                    $licitaciones->fecha_fallo = date("Y/m/d", strtotime($request['fecha_fallo']));
                }
                if($request['fecha_presentacion'] === "") {
                    $licitaciones->fecha_presentacion = null;
                } else {
                    $licitaciones->fecha_presentacion = date("Y/m/d", strtotime($request['fecha_presentacion']));
                }
                if(trim($request['orden_compra'] === "")) {
                    $licitaciones->orden_compra = "N/A";
                } else {
                    $licitaciones->orden_compra = trim($request['orden_compra']);
                }
                if($request['observaciones_creada'] === "") {
                    $licitaciones->observaciones_creada = null;
                } else {
                    $licitaciones->observaciones_creada = $request['observaciones_creada'];
                }
                if($request['observaciones_proceso'] === "") {
                    $licitaciones->observaciones_proceso = null;
                } else {
                    $licitaciones->observaciones_proceso = $request['observaciones_proceso'];
                }
                if($request['observaciones_presentada'] === "") {
                    $licitaciones->observaciones_presentada = null;
                } else {
                    $licitaciones->observaciones_presentada = $request['observaciones_presentada'];
                }
                if($request['observaciones_adjudicada'] === "") {
                    $licitaciones->observaciones_adjudicada = null;
                } else {
                    $licitaciones->observaciones_adjudicada = $request['observaciones_adjudicada'];
                }
                if($request['observaciones_no_adjudicada'] === "") {
                    $licitaciones->observaciones_no_adjudicada = null;
                } else {
                    $licitaciones->observaciones_no_adjudicada = $request['observaciones_no_adjudicada'];
                }
                if($request['observaciones_cancelada'] === "") {
                    $licitaciones->observaciones_cancelada = null;
                } else {
                    $licitaciones->observaciones_cancelada = $request['observaciones_cancelada'];
                }
                $licitaciones->responsable = trim(strtoupper($request['responsable'])) == '' ? null : trim(strtoupper($request['responsable']));
                $licitaciones->responsable_gdp = trim(strtoupper($request['responsable_gdp'])) == '' ? null : trim(strtoupper($request['responsable_gdp']));
                if ($licitaciones->create()) {
                    //
                    if (intval($request['respaldo1']) !== -1) {
                        $respaldo1 = new Respaldo();
                        $respaldo1->setTransaction($tx);
                        $respaldo1->licitacion_id = $licitaciones->id;
                        $respaldo1->empresa_id = intval($request['respaldo1']);
                        $respaldo1->empresa = trim($request['empresa_respaldo_1']);
                        if ($respaldo1->create()) {
                            $this->content['result'] = 'success';
                        }
                    }
                    if (intval($request['respaldo2']) !== -1) {
                        $respaldo2 = new Respaldo();
                        $respaldo2->setTransaction($tx);
                        $respaldo2->licitacion_id = $licitaciones->id;
                        $respaldo2->empresa_id = intval($request['respaldo2']);
                        $respaldo2->empresa = trim($request['empresa_respaldo_2']);
                        if ($respaldo2->create()) {
                            $this->content['result'] = 'success';
                        }
                    }
                    if (intval($request['respaldo3']) !== -1) {
                        $respaldo3 = new Respaldo();
                        $respaldo3->setTransaction($tx);
                        $respaldo3->licitacion_id = $licitaciones->id;
                        $respaldo3->empresa_id = intval($request['respaldo3']);
                        $respaldo3->empresa = trim($request['empresa_respaldo_3']);
                        if ($respaldo3->create()) {
                            $this->content['result'] = 'success';
                        }
                    }
                    if (intval($request['respaldo4']) !== -1) {
                        $respaldo4 = new Respaldo();
                        $respaldo4->setTransaction($tx);
                        $respaldo4->licitacion_id = $licitaciones->id;
                        $respaldo4->empresa_id = intval($request['respaldo4']);
                        $respaldo4->empresa = trim($request['empresa_respaldo_4']);
                        if ($respaldo4->create()) {
                            $this->content['result'] = 'success';
                        }
                    }
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la licitación.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($licitaciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la licitación.'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este folio de licitacion ya esta registrado.'];
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

            //$recurso = Recurso::findFirst($request['recurso_id']);
            //$cliente_id = $recurso->cliente_id;
            //$subprograma_id = $recurso->subprograma_id;

            //$cliente = VntClientes::findFirst($cliente_id);
            //$subprograma = Subprograma::findFirst($subprograma_id);

            //$array_codigo = explode("-",$recurso->codigo);

            //if(count($array_codigo) === 4) {

                //if($cliente->codigo !== "" && $subprograma->codigo !=="" && $array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo) {
                    
                    if(!($this->busquedaLicitaciones2($request['recurso_id'],$request['folio'],$request['id']))){

                        $licitaciones = Licitacion::findFirst($request['id']);
                        if ($licitaciones) {
                            $licitaciones->setTransaction($tx);
                            if(intval($request['recurso_id']) === 0){
                                $licitaciones->recurso_id = null;
                            } else {
                                $licitaciones->recurso_id = intval($request['recurso_id']);
                            }
                            $licitaciones->empresa_id = intval($request['empresa_id']);
                            $licitaciones->folio = strtoupper($request['folio']);
                            $licitaciones->titulo = strtoupper($request['titulo']);
                            $licitaciones->status = $request['status'];
                            $licitaciones->entidad_id = intval($request['entidad_id']);
                            $licitaciones->descripcion = trim($request['descripcion']);
                            $licitaciones->contrato = trim($request['contrato']);
                            $licitaciones->monto_inicial = floatval($request['monto_inicial']);
                            $licitaciones->monto_adjudicado = floatval($request['monto_adjudicado']);
                            if($request['fecha_inicio'] === ""){
                                $licitaciones->fecha_inicio = null;
                            } else {
                                $licitaciones->fecha_inicio = date("Y/m/d", strtotime($request['fecha_inicio']));
                            }
                            /* if($request['fecha_termino'] === ""){
                                $licitaciones->fecha_termino = null;
                            } else {
                                $licitaciones->fecha_termino = date("Y/m/d", strtotime($request['fecha_termino']));
                            } */
                            if($request['fecha_fallo'] === ""){
                                $licitaciones->fecha_fallo = null;
                            } else {
                                $licitaciones->fecha_fallo = date("Y/m/d", strtotime($request['fecha_fallo']));
                            }
                            if($request['fecha_presentacion'] === ""){
                                $licitaciones->fecha_presentacion = null;
                            } else {
                                $licitaciones->fecha_presentacion = date("Y/m/d", strtotime($request['fecha_presentacion']));
                            }
                            if(trim($request['orden_compra'] === "")){
                                $licitaciones->orden_compra = "N/A";
                            } else {
                                $licitaciones->orden_compra = trim($request['orden_compra']);
                            }
                            if(trim($request['comprador']) === ""){
                                $licitaciones->comprador = null;
                            } else {
                                $licitaciones->comprador = trim($request['comprador']);
                            }
                            if(trim($request['empresa']) === ""){
                                $licitaciones->empresa = null;
                            } else {
                                $licitaciones->empresa = trim($request['empresa']);
                            }
                            if($request['campo_anticipo'] === 'SI') {
                                $licitaciones->campo_anticipo = 'SI';
                                $licitaciones->porcentaje_anticipo = floatval($request['porcentaje_anticipo']);
                                $licitaciones->monto_anticipo = floatval($request['monto_anticipo']);
                            } else {
                                $licitaciones->campo_anticipo = 'NO';
                                $licitaciones->porcentaje_anticipo = 0;
                                $licitaciones->monto_anticipo = 0;
                            }
                            if($request['observaciones_creada'] === ""){
                                $licitaciones->observaciones_creada = null;
                            } else {
                                $licitaciones->observaciones_creada = $request['observaciones_creada'];
                            }
                            if($request['observaciones_proceso'] === ""){
                                $licitaciones->observaciones_proceso = null;
                            } else {
                                $licitaciones->observaciones_proceso = $request['observaciones_proceso'];
                            }
                            if($request['observaciones_presentada'] === ""){
                                $licitaciones->observaciones_presentada = null;
                            } else {
                                $licitaciones->observaciones_presentada = $request['observaciones_presentada'];
                            }
                            if($request['observaciones_adjudicada'] === ""){
                                $licitaciones->observaciones_adjudicada = null;
                            } else {
                                $licitaciones->observaciones_adjudicada = $request['observaciones_adjudicada'];
                            }
                            if($request['observaciones_no_adjudicada'] === ""){
                                $licitaciones->observaciones_no_adjudicada = null;
                            } else {
                                $licitaciones->observaciones_no_adjudicada = $request['observaciones_no_adjudicada'];
                            }
                            if($request['observaciones_cancelada'] === ""){
                                $licitaciones->observaciones_cancelada = null;
                            } else {
                                $licitaciones->observaciones_cancelada = $request['observaciones_cancelada'];
                            }
                            $licitaciones->responsable = trim(strtoupper($request['responsable'])) == '' ? null : trim(strtoupper($request['responsable']));
                            $licitaciones->responsable_gdp = trim(strtoupper($request['responsable_gdp'])) == '' ? null : trim(strtoupper($request['responsable_gdp']));
                            if ($licitaciones->update()) {
                                //
                                if (intval($request['id_respaldo1']) !== 0){
                                    $respaldo1 = Respaldo::findFirst(intval($request['id_respaldo1']));
                                    if ($respaldo1){
                                        $respaldo1->setTransaction($tx);
                                        $respaldo1->empresa_id = intval($request['respaldo1']);
                                        $respaldo1->empresa = trim($request['empresa_respaldo_1']);
                                        if ($respaldo1->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($respaldo1);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la empresa de respaldo.'];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    if (intval($request['respaldo1']) !== -1) {
                                        $respaldo1 = new Respaldo();
                                        $respaldo1->setTransaction($tx);
                                        $respaldo1->licitacion_id = $licitaciones->id;
                                        $respaldo1->empresa_id = intval($request['respaldo1']);
                                        $respaldo1->empresa = trim($request['empresa_respaldo_1']);
                                        if ($respaldo1->create()) {
                                            $this->content['result'] = 'success';
                                        }
                                    }
                                }
                                if (intval($request['id_respaldo2']) !== 0){
                                    $respaldo2 = Respaldo::findFirst(intval($request['id_respaldo2']));
                                    if ($respaldo2) {
                                        $respaldo2->setTransaction($tx);
                                        $respaldo2->empresa_id = intval($request['respaldo2']);
                                        $respaldo2->empresa = trim($request['empresa_respaldo_2']);
                                        if ($respaldo2->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($respaldo2);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la empresa de respaldo.'];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    if (intval($request['respaldo2']) !== -1) {
                                        $respaldo2 = new Respaldo();
                                        $respaldo2->setTransaction($tx);
                                        $respaldo2->licitacion_id = $licitaciones->id;
                                        $respaldo2->empresa_id = intval($request['respaldo2']);
                                        $respaldo2->empresa = trim($request['empresa_respaldo_2']);
                                        if ($respaldo2->create()) {
                                            $this->content['result'] = 'success';
                                        }
                                    }
                                }
                                if (intval($request['id_respaldo3']) !== 0){
                                    $respaldo3 = Respaldo::findFirst(intval($request['id_respaldo3']));
                                    if ($respaldo3){
                                        $respaldo3->setTransaction($tx);
                                        $respaldo3->empresa_id = intval($request['respaldo3']);
                                        $respaldo3->empresa = trim($request['empresa_respaldo_3']);
                                        if ($respaldo3->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($respaldo3);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la empresa de respaldo.'];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    if (intval($request['respaldo3']) !== -1) {
                                        $respaldo3 = new Respaldo();
                                        $respaldo3->setTransaction($tx);
                                        $respaldo3->licitacion_id = $licitaciones->id;
                                        $respaldo3->empresa_id = intval($request['respaldo3']);
                                        $respaldo3->empresa = trim($request['empresa_respaldo_3']);
                                        if ($respaldo3->create()) {
                                            $this->content['result'] = 'success';
                                        }
                                    }
                                }
                                if (intval($request['id_respaldo4']) !== 0){
                                    $respaldo4 = Respaldo::findFirst(intval($request['id_respaldo4']));
                                    if ($respaldo4){
                                        $respaldo4->setTransaction($tx);
                                        $respaldo4->empresa_id = intval($request['respaldo4']);
                                        $respaldo4->empresa = trim($request['empresa_respaldo_4']);
                                        if ($respaldo4->update()) {
                                            $this->content['result'] = 'success';
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($respaldo4);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la empresa de respaldo.'];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    if (intval($request['respaldo4']) !== -1) {
                                        $respaldo4 = new Respaldo();
                                        $respaldo4->setTransaction($tx);
                                        $respaldo4->licitacion_id = $licitaciones->id;
                                        $respaldo4->empresa_id = intval($request['respaldo4']);
                                        $respaldo4->empresa = trim($request['empresa_respaldo_4']);
                                        if ($respaldo4->create()) {
                                            $this->content['result'] = 'success';
                                        }
                                    }
                                }
                                //
                                $this->content['result'] = 'success';
                                
                                if ($this->content['result'] = 'success') {
                                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la licitación.'];
                                    $tx->commit();
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($licitaciones);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la licitación.'];
                                $tx->rollback();
                            }
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este folio de licitacion ya esta registrado en este estado.'];
                    } /*
                } else {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                }
            } else {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
            }*/

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
            
            $request = $this->request->getPut();
            if ($request['id']) {
                    $licitaciones = Licitacion::findFirst($request['id']);
                    $licitaciones->setTransaction($tx);
                    $licitaciones->status_mostrado = 'ELIMINADO';

                    if ($licitaciones->update()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($licitaciones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la licitación.'];
                    $tx->commit();
                }
                
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
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/bases/' .$documento->id.'.'.$documento->doc_type;
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

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();

            if ($this->request->hasFiles()) {
                // $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/';
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones/bases/';
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
                            
                            // print_r(strtolower($file->getExtension()));
                            //$doc = $request['nombre'].'_'.$request['licitacion_id']. '.' . strtolower($file->getExtension());
                            $doc = $request['nombre'];
                            $nombre = $request['nombre'];

                            if(intval($request['formato_requisito_id']) === 0){

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                $documento->doc_type = $file->getExtension();
                                $documento->name=$doc;

                                if ($documento->create()) {
                                    
                                    $id = $documento->id;
                                    $nombre_con_id = $id .'.'. $file->getExtension();

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

                                    // si se crea que pasa?
                                    $requerimientos = new Requerimientos();
                                    $requerimientos->setTransaction($tx);
                                    $requerimientos->licitacion_id = intval($request['licitacion_id']);
                                    if($request['observaciones_archivo'] === ''){
                                        $requerimientos->descripcion = null;
                                    } else {
                                        $requerimientos->descripcion = $request['observaciones_archivo'];
                                    }

                                    if($request['nombre_documento'] === ''){
                                        $requerimientos->nombre_referencia = null;
                                    } else {
                                        $requerimientos->nombre_referencia = $request['nombre_documento'];
                                    }
                                    
                                    $requerimientos->documento_id = $id;

                                    if ($requerimientos->create()) {
                                        $content['result'] = 'success';
                                        $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el formato.'];
                                        $tx->commit();
                                    } else {
                                        $content['error'] = Helpers::getErrors($requerimientos);
                                        $content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el formato al registro de la licitacion.'];
                                        $tx->rollback();
                                    }
                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }

                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .docx'];
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

    public function getFileFinal($id, $ext)
    {    
        //$cotizacion = CotizacionGastos::findFirst($id);
        $documento = Licitacion::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones_documentos_finales/' .$documento->id.'.'.$ext;
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
            $contratos = Licitacion::findFirst(intval($request['id']));

            if ($contratos) {
                $contratos->setTransaction($tx);
                $contratos->documento_final = null;
                if ($contratos->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la licitación.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($contratos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la licitación.'];
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
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/licitaciones_documentos_finales/';
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

                                    $contrato = Licitacion::findFirst(intval($request['contrato_id']));
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