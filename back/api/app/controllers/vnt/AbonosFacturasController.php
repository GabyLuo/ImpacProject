<?php

use Phalcon\Mvc\Controller;

class AbonosFacturasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,
            vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,
            vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
            vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
            vnt_clientes.razon_social as cliente
            from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id
            left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id
            order by vnt_contrato.id";
        $this->content['abonos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function historialAbonos ($factura_id) {
        $sql = "SELECT vnt_facturas_abonos.id, vnt_facturas_abonos.factura_id, vnt_facturas_abonos.fecha, vnt_facturas_abonos.monto, vnt_facturas_abonos.transaccion, sys_users.nickname, false as xml, 'xml' as doc_type FROM vnt_facturas_abonos inner join sys_users on sys_users.id = vnt_facturas_abonos.user_id and vnt_facturas_abonos.factura_id = $factura_id

            union

            select x.id, x.id as factura_id, vnt_contratos_facturas.fecha_factura as fecha, x.monto_factura, 'DESCONTADO' as transaccion, sys_users.nickname , true as xml, sys_documents.doc_type
            from vnt_contratos_facturas as x
            inner join sys_documents on sys_documents.id = x.document_id
            inner join vnt_contratos_facturas on vnt_contratos_facturas.uuid = x.factura_relacionada and vnt_contratos_facturas.id = $factura_id
            inner join sys_users on sys_users.id = sys_documents.created_by 

            order by fecha asc";
        $this->content['abonos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function filtrar () {
        $tx = $this->transactions->get();
        $request = $this->request->getPost();

        $proyecto_id = intval($request['proyecto_id']);
        if(intval($proyecto_id) !== 0) {
            
            $where=($id_proyecto!=null?'where vnt_contrato.recurso_id='.$id_proyecto:'');
            $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,
            vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,
            vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
            vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
            vnt_clientes.razon_social as cliente
            from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
            join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
            join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id
            left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id
            ".$where."
            order by vnt_contrato.id";
        }

        $this->content['abonos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

	public function save ()
    {	
    	try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $validUser = Auth::getUserData($this->config);
            $id = $validUser->user_id;
                $abonos = new AbonosFacturas();
                $abonos->setTransaction($tx);
                $abonos->factura_id = strtoupper($request['factura_id']);
                $abonos->fecha = date("Y/m/d", strtotime($request['fecha_abono']));
                $abonos->monto = floatval($request['monto_abonar']);
                if (trim($request['transaccion']) === '') {
                    $abonos->transaccion = null;
                } else {
                    $abonos->transaccion = trim($request['transaccion']);
                }
                $abonos->user_id = $id;
                $suma_abonos = floatval($request['monto_abonar']) + floatval($request['monto_total_abonado']);
                if ($abonos->create()) {
                    $contratoFactura = contratosFacturas::findFirst($request['factura_id']);
                    if ($contratoFactura) {
                        $contratoFactura->setTransaction($tx);
                        if ($contratoFactura->status != 'DESCONTADO') {
                            if ((floatval($suma_abonos) === floatval(($contratoFactura->monto_factura) - 0.2)) || (floatval($suma_abonos) > floatval($contratoFactura->monto_factura)) || (floatval($suma_abonos) === floatval($contratoFactura->monto_factura))) {
                                $contratoFactura->status='PAGADO';
                            } else {
                                $contratoFactura->status='ABONADO';
                            }
                        }
                        if ($contratoFactura->update()) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha guardado el abono de la factura.'];
                            $tx->commit();
                        } else {
                            $this->content['error'] = Helpers::getErrors($contratoFactura);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = 'Error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la factura.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($abonos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
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

                $abonos = AbonosFacturas::findFirst($request['id']);
                if ($abonos) {
                    $abonos->setTransaction($tx);
                    $abonos->monto = floatval($request['monto']);

                    if ($abonos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el abono de la factura.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($abonos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el abono de la factura.'];
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
                    $tipos = AbonosFacturas::findFirst($request['id']);
                    $tipos->setTransaction($tx);

                    if ($tipos->delete()) {
                        $factura = $tipos->factura_id;
                        $montos = AbonosFacturas::find([
                            'factura_id = :factura_id: ',
                                'bind' => [
                                    'factura_id' => $factura
                                ]
                            ]
                        );
                        $contratoFactura = contratosFacturas::findFirst($factura);
                        if ($contratoFactura) {
                            $contratoFactura->setTransaction($tx);
                            if (sizeof($montos) > 0) {
                                if ($contratoFactura->status === 'PAGADO') {
                                    $contratoFactura->status='ABONADO';
                                }
                            } else {
                                if ($contratoFactura->status !== 'DESCONTADO') {
                                    $contratoFactura->status='EMITIDO';
                                }
                            }
                            if ($contratoFactura->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($contratoFactura);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el abono de la factura.'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['error'] = 'Error';
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se encontró la factura'];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($tipos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el abono.'];
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