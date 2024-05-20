<?php

use Phalcon\Mvc\Controller;

class ComisionesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *, (select nombre from com_aliados where id = com_comisiones.aliado_id) as aliado, case when com_comisiones.tipo = 'PORCENTAJE' then porcentaje else com_comisiones.monto end as cantidad_com, status, (select coalesce(0, sum (monto)) from com_comisiones_abono where com_comisiones_abono.comision_id = com_comisiones.id) as monto_total_abonado
            FROM com_comisiones
            ORDER BY com_comisiones.id";
        $this->content['comisiones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto ($proyecto_id)
    {
        $sql = "SELECT com_comisiones.*, 
            case when com_comisiones.asignado = 'ALIADO' then (select nombre 
                from com_aliados where id = com_comisiones.aliado_id) else (select sys_users.nickname 
                from sys_users where id = com_comisiones.vendedor_id) end as aliado, 
            case when com_comisiones.tipo = 'PORCENTAJE' then porcentaje else com_comisiones.monto end as cantidad_com, status, 
            (select coalesce(0, sum (monto)) 
                from com_comisiones_abono where com_comisiones_abono.comision_id = com_comisiones.id) as monto_total_abonado,
            case when com_comisiones.aplicado = 'AL CONTRATO' then 
                (select vnt_contrato.num_contrato 
                    from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) else 'NO APLICA' end as contrato,
                vnt_recurso.codigo as codigo_proyecto, vnt_recurso.nombre as nombre_proyecto, 
            (select vnt_contrato.monto_total 
                from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) as monto_contrato,
            case when com_comisiones.aplicado = 'A LA UTILIDAD' then vnt_recurso.monto else (select vnt_contrato.monto_total from vnt_contrato where vnt_contrato.id = com_comisiones.contrato_id) end as monto_bolsa,
            (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal 
            from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1')
            FROM com_comisiones
            inner join vnt_recurso on vnt_recurso.id = com_comisiones.proyecto_id
            WHERE proyecto_id = $proyecto_id
            ORDER BY id";
        $comisiones = $this->db->query($sql)->fetchAll();
        foreach ($comisiones as $key => $com) {
            $monto_bolsa = $com['monto_bolsa'];
            if ($com['iva'] === 'SUBTOTAL') {
                $monto_base = $com['monto_bolsa'] / 1.16;
            } else {
                $monto_base = $com['monto_bolsa'];
            }
            $subtotal_monto_bolsa = number_format(floatval(round($com['monto_bolsa'] / 1.16, 2)),2,'.',',');
            $utilidad = $monto_base - $com['presupuesto_actividad_principal'];
            if ($com['aplicado'] == 'AL CONTRATO') {
                if ($com['tipo'] == 'PORCENTAJE') {
                    if ($com['monto_bolsa'] > 0) {
                        $monto_subtotal_comision = ($monto_base * ($com['porcentaje'] / 100));
                    } else {
                        $monto_subtotal_comision = 0;
                    }
                } else {
                    $monto_subtotal_comision = $com['cantidad_com'];
                }
            }
            if ($com['aplicado'] == 'A LA UTILIDAD') {
                $utility = $monto_base - $com['presupuesto_actividad_principal'];
                if ($com['tipo'] == 'PORCENTAJE') {
                    if ($utility > 0) {
                        $monto_subtotal_comision = ($utility * ($com['porcentaje'] / 100));
                    } else {
                        $monto_subtotal_comision = 0;
                    }
                } else {
                    $monto_subtotal_comision = $com['cantidad_com'];
                }
            }
            $iva = number_format(floatval($com['monto_bolsa'] * .16),2,'.',',');
            $monto_total_comision = $monto_subtotal_comision;
            $comisiones[$key]['monto_total_comision'] = number_format(floatval($monto_total_comision),2,'.',',');
            //
            $sql_documentos = "SELECT * from com_comisiones_files where comision_id=" . $com['id'];
            $documentos = $this->db->query($sql_documentos)->fetchAll();
            $array_documentos = [];
            foreach ($documentos as $doc_elemento){
            $doc=(object)array();
            $doc->comision_id = $doc_elemento['comision_id'];
            $doc->nombre = $doc_elemento['nombre'];
            $doc->extension = $doc_elemento['extension'];
            $doc->id = $doc_elemento['id'];
            if ($doc_elemento['extension'] === 'docx') {
                  $doc->color = 'blue-9';
                  $doc->icon = 'fas fa-file-word';
                } else if ($doc_elemento['extension'] === 'pdf' || $doc_elemento['extension'] === 'PDF') {
                  $doc->color = 'red-10';
                  $doc->icon = 'fas fa-file-pdf';
                } else if ($doc_elemento['extension'] === 'xml' || $doc_elemento['extension'] === 'XML') {
                  $doc->color = 'light-green';
                  $doc->icon = 'fas fa-file-code';
                } else if ($doc_elemento['extension'] === 'jpg' || $doc_elemento['extension'] === 'jpeg' || $doc_elemento['extension'] === 'png' || $doc_elemento['extension'] === 'JPG' || $doc_elemento['extension'] === 'JPEG' || $doc_elemento['extension'] === 'PNG') {
                  $doc->color = 'amber';
                  $doc->icon = 'fas fa-file-image';
                }
                array_push($array_documentos,$doc);
            }
            $comisiones[$key]['documentos'] = $array_documentos;
        }

        $this->content['comisiones'] = $comisiones;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $comisiones = new Comisiones();
            $comisiones->setTransaction($tx);
            $comisiones->proyecto_id = intval($request['proyecto_id']);
            $comisiones->aliado_id = intval($request['aliado_id']) > 0 ? intval($request['aliado_id']) : null;
            $comisiones->vendedor_id = intval($request['vendedor_id']) > 0 ? intval($request['vendedor_id']) : null;
            $comisiones->tipo = trim(strtoupper($request['tipo']));
            $comisiones->porcentaje = floatval($request['porcentaje']);
            $comisiones->monto = floatval($request['monto_evaluado']);
            $comisiones->metodo_pago = trim(strtoupper($request['metodo_pago']));
            if (trim(strtoupper($request['aplicado'])) === 'A LA UTILIDAD') {
                $comisiones->contrato_id = null;
            } else {
                $comisiones->contrato_id = intval($request['contrato_id']);
            }
            $comisiones->iva = $request['iva'];
            $comisiones->aplicado = $request['aplicado'];
            $comisiones->tiempo_pago = $request['tiempo_pago'];
            $comisiones->observaciones = trim(strtoupper($request['observaciones']));
            $comisiones->status = 'NUEVO';
            $comisiones->year = $request['year'];
            $comisiones->asignado = $request['asignado'];
            if ($comisiones->create()) {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado la comisión.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($comisiones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar la comisión.'];
                $tx->rollback();
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

            $abonos = AbonosComisiones::find('comision_id = '.$request['id']);
            $suma = 0;
            foreach ($abonos as $abono) {
                $suma = $suma + $abono->monto;
            }
            $comision_suma = Comisiones::findFirst($request['id'])->monto;
            if ($suma > $comision_suma) {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La suma de abonos supera el monto de comisión, debe eliminarlos para continuar.'];
            } else {
                $comisiones = Comisiones::findFirst($request['id']);
                if ($comisiones) {
                    $comisiones->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $comisiones->proyecto_id = intval($request['proyecto_id']);
                    $comisiones->aliado_id = intval($request['aliado_id']) > 0 ? intval($request['aliado_id']) : null;
                    $comisiones->vendedor_id = intval($request['vendedor_id']) > 0 ? intval($request['vendedor_id']) : null;
                    $comisiones->tipo = trim(strtoupper($request['tipo']));
                    $comisiones->porcentaje = floatval($request['porcentaje']);
                    $comisiones->monto = floatval($request['monto_evaluado']);
                    $comisiones->metodo_pago = trim(strtoupper($request['metodo_pago']));
                    if (trim(strtoupper($request['aplicado'])) === 'A LA UTILIDAD') {
                        $comisiones->contrato_id = null;
                    } else {
                        $comisiones->contrato_id = intval($request['contrato_id']);
                    }
                    $comisiones->iva = $request['iva'];
                    $comisiones->aplicado = $request['aplicado'];
                    $comisiones->tiempo_pago = $request['tiempo_pago'];
                    $comisiones->observaciones = trim(strtoupper($request['observaciones']));
                    $comisiones->asignado = $request['asignado'];
                    if ($comisiones->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la comisión.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos_movimientos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la comisión'];
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
                    $comisiones = Comisiones::findFirst($request['id']);
                    $comisiones->setTransaction($tx);

                    if ($comisiones->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($comisiones);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la comisión.'];
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