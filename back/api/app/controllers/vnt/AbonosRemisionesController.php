<?php

use Phalcon\Mvc\Controller;

class AbonosRemisionesController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * from vnt_remisiones_abonos";
        $this->content['abonos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function historialAbonos ($factura_id) {
        $sql = "SELECT vnt_remisiones_abonos.id, vnt_remisiones_abonos.factura_id, vnt_remisiones_abonos.fecha, vnt_remisiones_abonos.monto, vnt_remisiones_abonos.transaccion, sys_users.nickname, false as xml, 'xml' as doc_type
            FROM vnt_remisiones_abonos 
            inner join sys_users on sys_users.id = vnt_remisiones_abonos.user_id and vnt_remisiones_abonos.factura_id = $factura_id
            union 

            select vnt_remisiones_facturas.id, vnt_remisiones_facturas.id as factura_id, vnt_remisiones_facturas.fecha_factura as fecha,vnt_remisiones_facturas.monto_factura as monto, 'DESCONTADO' as transaccion, sys_users.nickname, true as xml, vnt_remisiones_facturas.doc_type
            from vnt_remisiones_facturas 
            inner join sys_users on sys_users.id = vnt_remisiones_facturas.created_by
            inner join vnt_remisiones_facturas as x on x.remision_id = $factura_id and vnt_remisiones_facturas.factura_relacionada = x.id and vnt_remisiones_facturas.remision_id > 0

            order by fecha asc";
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
                $abonos = new AbonosRemisiones();
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
                    $contratoFactura = Remisiones::findFirst($request['factura_id']);
                    if ($contratoFactura) {
                        $contratoFactura->setTransaction($tx);
                        // var_dump($suma_abonos);
                        // var_dump($request['monto_factura']);
                        if ($contratoFactura->status_cobranza !== 'DESCONTADO') {
                            if (floatval($suma_abonos) === floatval($request['monto_factura'])) {
                                $contratoFactura->status_cobranza='PAGADO';
                            } else {
                                $contratoFactura->status_cobranza='ABONADO';
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

                $abonos = AbonosRemisiones::findFirst($request['id']);
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
                    $tipos = AbonosRemisiones::findFirst($request['id']);
                    if ($tipos) {
                        $tipos->setTransaction($tx);
                        if ($tipos->delete()) {
                            $factura = $tipos->factura_id;
                            $montos = AbonosRemisiones::find([
                                'factura_id = :factura_id: ',
                                    'bind' => [
                                        'factura_id' => $factura
                                    ]
                                ]
                            );
                            $contratoFactura = Remisiones::findFirst($factura);
                            if ($contratoFactura) {
                                $contratoFactura->setTransaction($tx);
                                if (sizeof($montos) > 0 && $contratoFactura->status_cobranza !== 'DESCONTADO') {
                                    if ($contratoFactura->status_cobranza === 'PAGADO') {
                                        $contratoFactura->status_cobranza = 'ABONADO';
                                    }
                                } else {
                                    if ($contratoFactura->status_cobranza !== 'DESCONTADO') {
                                        $contratoFactura->status_cobranza='EMITIDO';
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