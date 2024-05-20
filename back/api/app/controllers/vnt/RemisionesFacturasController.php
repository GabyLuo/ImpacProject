<?php

use Phalcon\Mvc\Controller;

class RemisionesFacturasController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getFacturas ($remision)
    {
        if(intval($remision) === 0) {
            $where = " ";
        } else {
            $where = " and vnt_remisiones_facturas.remision_id = $remision ";
        }
        $sql = "SELECT to_char(vnt_remisiones.fecha_venta, 'YYYY-MM-DD') as fecha_venta, to_char(vnt_remisiones.created, 'YYYY-MM-DD HH:MI:SS') as created,vnt_remisiones_facturas.id, vnt_remisiones_facturas.remision_id, vnt_remisiones_facturas.fecha_factura, vnt_remisiones_facturas.fecha_pago, vnt_remisiones_facturas.document_id, vnt_remisiones_facturas.fecha_vencimiento, vnt_remisiones_facturas.uuid as folio_fiscal, vnt_remisiones_facturas.monto_factura, vnt_remisiones_facturas.uuid, vnt_remisiones_facturas.status, vnt_remisiones_facturas.doc_type, vnt_remisiones_facturas.name, (select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = vnt_remisiones_facturas.id), 0)) as monto_total_abonado, (vnt_remisiones_facturas.monto_factura - (select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = vnt_remisiones_facturas.id), 0))) as monto_restante
                from vnt_remisiones_facturas join vnt_remisiones on vnt_remisiones.id = vnt_remisiones_facturas.remision_id" . $where ."
                join vnt_clientes on vnt_clientes.id = vnt_remisiones.cliente_id
                join vnt_empresa on vnt_empresa.id = vnt_remisiones.empresa_id";
        $facturas = $this->content['facturas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasByContratoandId ($cliente, $empresa, $year, $status)
    {
        if(intval($empresa) === 0) {
            $where = " ";
        } else {
            $where = " and w.empresa_id = $empresa ";
        }
        if(intval($cliente) === 0) {
            $cli = " ";
        } else {
            $cli = " and w.cliente_id = $cliente ";
        }
        if($status == 'all') {
            $sta = " ";
        } else {
            $sta = " and w.status_cobranza = '$status'";
        }

        $sql = "SELECT w.id, w.fecha_venta,w.created, w.tipo, vnt_remisiones_facturas.uuid ,SUBSTRING(vnt_remisiones_facturas.descripcion, 0, 80) as name, vnt_remisiones_facturas.folio_xml as folio, vnt_remisiones_facturas.monto_factura as monto_factura, status_cobranza,(select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0)) as monto_total_abonado, case when vnt_remisiones_facturas.factura_relacionada is not null then 0 else (vnt_remisiones_facturas.monto_factura - ((select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0)) + (select COALESCE((SELECT sum(monto_factura) from vnt_remisiones_facturas as x where x.factura_relacionada = vnt_remisiones_facturas.id and x.remision_id > 0 and x.cancelado = false), 0)))) end as monto_restante, vnt_clientes.razon_social as cliente, vnt_empresa.razon_social as empresa, w.cliente_id, w.empresa_id, case when vnt_remisiones_facturas.repetido = true then (select vnt_contrato.nombre from vnt_contrato where vnt_contrato.id = vnt_remisiones_facturas.contrato_id) else '' end as contrato, case when vnt_remisiones_facturas.repetido = true then 'SI' else 'NO' end as duplicada,vnt_remisiones_facturas.id as factura_id,vnt_remisiones_facturas.doc_type as doc_type, vnt_remisiones_facturas.cancelado
            FROM vnt_remisiones_facturas inner join vnt_remisiones as w on w.id = vnt_remisiones_facturas.remision_id
            join vnt_clientes on vnt_clientes.id = w.cliente_id".$cli." {$sta}
            join vnt_empresa on vnt_empresa.id = w.empresa_id " . $where." and w.tipo = 'HISTÓRICO' and date_part('year', w.fecha_venta) = $year";
        $facturas = $this->db->query($sql)->fetchAll();
        $this->content['facturas'] = $facturas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasDuplicadas ($cliente, $empresa, $year, $status)
    {
        if(intval($empresa) === 0) {
            $where = " ";
        } else {
            $where = " and w.empresa_id = $empresa ";
        }
        if(intval($cliente) === 0) {
            $cli = " ";
        } else {
            $cli = " and w.cliente_id = $cliente ";
        }
        if($status == 'all') {
            $sta = " ";
        } else {
            $sta = " and w.status_cobranza = '$status'";
        }

        $sql = "SELECT * from (SELECT w.id, w.fecha_venta,w.created, w.tipo, (select uuid as folio_fiscal from vnt_remisiones_facturas where remision_id = w.id),(select SUBSTRING(descripcion, 0, 80) as name from vnt_remisiones_facturas where remision_id = w.id), w.folio, coalesce((select sum(vnt_remisiones_facturas.monto_factura) from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),0) as monto_factura, status_cobranza,(select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0)) as monto_total_abonado, (coalesce((select sum(vnt_remisiones_facturas.monto_factura) from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),0) - (select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0))) as monto_restante, vnt_clientes.razon_social as cliente, vnt_empresa.razon_social as empresa, w.cliente_id, w.empresa_id, (select case when vnt_remisiones_facturas.repetido = true then  (select vnt_recurso.codigo from vnt_recurso inner join vnt_contrato on vnt_contrato.recurso_id = vnt_recurso.id and vnt_contrato.id = vnt_remisiones_facturas.contrato_id) || '-' || (select vnt_contrato.nombre from vnt_contrato where vnt_contrato.id = vnt_remisiones_facturas.contrato_id) else '' end as contrato from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id), (select case when vnt_remisiones_facturas.repetido = true then 'SI' else 'NO' end as duplicada from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),(select vnt_remisiones_facturas.id as factura_id from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),
            (select vnt_remisiones_facturas.doc_type as doc_type from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id) FROM public.vnt_remisiones as w
            join vnt_clientes on vnt_clientes.id = w.cliente_id".$cli." {$sta}
            join vnt_empresa on vnt_empresa.id = w.empresa_id " . $where." and w.tipo = 'HISTÓRICO' and date_part('year', w.fecha_venta) = $year) as x where x.duplicada = 'SI'";
        $facturas = $this->db->query($sql)->fetchAll();
        $this->content['facturas'] = $facturas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasDuplicadasCsv ($cliente, $empresa, $year, $status)
    {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Folio','Empresa', 'Cliente', 'Concepto', 'Folio fiscal', 'Creado', 'Fecha venta', 'Tipo', 'Total', 'Abonado', 'Restante', 'Status', 'Contrato'), ',');

        if(intval($empresa) === 0) {
            $where = " ";
        } else {
            $where = " and w.empresa_id = $empresa ";
        }
        if(intval($cliente) === 0) {
            $cli = " ";
        } else {
            $cli = " and w.cliente_id = $cliente ";
        }
        if($status == 'all') {
            $sta = " ";
        } else {
            $sta = " and w.status_cobranza = '$status'";
        }

        $sql = "SELECT * from (SELECT w.id, w.fecha_venta,w.created, w.tipo, (select uuid as folio_fiscal from vnt_remisiones_facturas where remision_id = w.id),(select SUBSTRING(descripcion, 0, 80) as name from vnt_remisiones_facturas where remision_id = w.id), w.folio, coalesce((select sum(vnt_remisiones_facturas.monto_factura) from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),0) as monto_factura, status_cobranza,(select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0)) as monto_total_abonado, (coalesce((select sum(vnt_remisiones_facturas.monto_factura) from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),0) - (select COALESCE((SELECT sum(monto) from vnt_remisiones_abonos where factura_id = w.id), 0))) as monto_restante, vnt_clientes.razon_social as cliente, vnt_empresa.razon_social as empresa, w.cliente_id, w.empresa_id, (select case when vnt_remisiones_facturas.repetido = true then  (select vnt_recurso.codigo from vnt_recurso inner join vnt_contrato on vnt_contrato.recurso_id = vnt_recurso.id and vnt_contrato.id = vnt_remisiones_facturas.contrato_id) || '-' || (select vnt_contrato.nombre from vnt_contrato where vnt_contrato.id = vnt_remisiones_facturas.contrato_id) else '' end as contrato from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id), (select case when vnt_remisiones_facturas.repetido = true then 'SI' else 'NO' end as duplicada from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),(select vnt_remisiones_facturas.id as factura_id from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id),
            (select vnt_remisiones_facturas.doc_type as doc_type from vnt_remisiones_facturas where vnt_remisiones_facturas.remision_id = w.id) FROM public.vnt_remisiones as w
            join vnt_clientes on vnt_clientes.id = w.cliente_id".$cli." {$sta}
            join vnt_empresa on vnt_empresa.id = w.empresa_id " . $where." and w.tipo = 'HISTÓRICO' and date_part('year', w.fecha_venta) = $year) as x where x.duplicada = 'SI'";
        $facturas = $this->db->query($sql)->fetchAll();

        foreach($facturas as $elemento){
            fputcsv($fp, [
                $elemento['folio'],
                $elemento['empresa'],
                $elemento['cliente'],
                $elemento['name'],
                $elemento['folio_fiscal'],
                $elemento['created'],
                $elemento['fecha_venta'],
                $elemento['tipo'],
                number_format(floatval($elemento['monto_factura']),2,'.',','),
                number_format(floatval($elemento['monto_total_abonado']),2,'.',','),
                number_format(floatval($elemento['monto_restante']),2,'.',','),
                $elemento['status_cobranza'],
                $elemento['contrato']
                ], ',');
        }
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Facturas_duplicadas'. '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    public function uploadArchivo()
    {   
        $content = $this->content;
        $request = $this->request->getPost();
        $tx = $this->transactions->get();

        try {
            if ($this->request->hasFiles())  {
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas_remisiones/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                foreach ($this->request->getUploadedFiles() as $file) {
                    $existe_factura = false;
                    $total_factura = 0;
                    $uuid = '';
                    $sobrepasa_monto = false;

                    $doc = trim(strtoupper($request['nombre']));
                    $fecha_factura = trim(strtoupper($request['fecha_factura']));
                    $fecha_pago = trim(strtoupper($request['fecha_pago']));
                    $fecha_vencimiento = trim(strtoupper($request['fecha_vencimiento']));
                    $remision_id = trim(strtoupper($request['remision_id']));
                    $folio_fiscal = trim(strtoupper($request['folio_fiscal']));
                    $validUser = Auth::getUserData($this->config);
                    $contratoFactura= new RemisionesFacturas();
                    $contratoFactura->setTransaction($tx);
                    $contratoFactura->remision_id=$remision_id;
                    $contratoFactura->fecha_factura=$fecha_factura;
                    $contratoFactura->fecha_pago=$fecha_pago;
                    $contratoFactura->fecha_vencimiento=$fecha_vencimiento;
                    $contratoFactura->folio_fiscal=$folio_fiscal;
                    $contratoFactura->account_id = $validUser->account_id;
                    $contratoFactura->doc_type = $file->getExtension();
                    $contratoFactura->name=$doc;
                    $contratoFactura->year = date('Y');

                    if ($contratoFactura->create()) {
                        $doc_id=$contratoFactura->id;
                        $fullPath = $upload_dir . $doc_id.'.'. $file->getExtension();
                        $file->moveTo($fullPath);

                        if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                            $xml = simplexml_load_file($fullPath);
                            $ns = $xml->getNamespaces(true);
                            $xml->registerXPathNamespace('c', $ns['cfdi']);
                            $xml->registerXPathNamespace('t', $ns['tfd']);

                            $total_factura = 0;          
                            foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                                $total_factura = $cfdiComprobante['Total']; 
                            }
                            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                                $contratoFactura->uuid = $tfd['UUID'];
                                $uuid = $tfd['UUID'];
                                $contratoFactura->monto_factura = floatval($total_factura);
                            }
                        } else {
                            $contratoFactura->uuid = null;
                            $contratoFactura->monto_factura = 0;
                        }
                        $contratoFactura = RemisionesFacturas::findFirst($doc_id);
                        $contratoFactura->setTransaction($tx);
                        $contratoFactura->uuid = ''.$uuid;
                        $contratoFactura->monto_factura = floatval($total_factura);

                        $existe_factura = false;
                        if ($uuid !== '') {
                            $existe_factura = RemisionesFacturas::findFirst(
                            [
                                'id != :id: AND uuid = :uuid:',
                                'bind' => [
                                    'id' => $doc_id,
                                    'uuid' => $uuid
                                ]
                            ]);
                            if ($existe_factura !== false) {
                                $id_remision_factura = $existe_factura->remision_id;
                                $remision = Remisiones::findFirst($id_remision_factura);
                                // var_dump($existe_factura);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en la remision '. $remision->id . '.'];
                            }
                        }
                        if ($existe_factura == false /* && $sobrepasa_monto == false */ && $contratoFactura->update()) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El Archivo se ha subido con éxito'];                            
                            $tx->commit();
                        }
                    } else {
                        unlink($fullPath);
                        $this->content['error'] = Helpers::getErrors($contratoFactura);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe'];
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

    public function uploadArchivoMasivo()
    {   
        $content = $this->content;
        $request = $this->request->getPost();
        $tx = $this->transactions->get();
        try {
            if ($this->request->hasFiles())  {
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas_remisiones/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                foreach ($this->request->getUploadedFiles() as $file) {
                    $total_factura = 0;
                    $uuid = '';
                    $rfc_emisor = '';
                    $rfc_receptor = '';
                    $metodo_pago = '';
                    $fecha_timbrado = '';
                    $descripcion = '';
                    $folio_xml = '';
                    $uuid_relacionado = '';

                    $doc = trim(strtoupper($request['nombre']));
                    $validUser = Auth::getUserData($this->config);
                    $contratoFactura= new RemisionesFacturas();
                    $contratoFactura->setTransaction($tx);
                    $contratoFactura->remision_id = 0;
                    $contratoFactura->account_id = $validUser->account_id;
                    $contratoFactura->doc_type = $file->getExtension();
                    $contratoFactura->name=$doc;
                    $contratoFactura->year = date('Y');

                    if ($contratoFactura->create()) {
                        $doc_id=$contratoFactura->id;
                        $fullPath = $upload_dir . $doc_id.'.'. $file->getExtension();
                        $file->moveTo($fullPath);

                        if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                            $xml = simplexml_load_file($fullPath);
                            $ns = $xml->getNamespaces(true);
                            $xml->registerXPathNamespace('c', $ns['cfdi']);
                            $xml->registerXPathNamespace('t', $ns['tfd']);

                            $total_factura = 0;          
                            foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                                $total_factura = $cfdiComprobante['Total'];
                                $metodo_pago = $cfdiComprobante['MetodoPago'];
                                $folio_xml = ''.$cfdiComprobante['Folio'];
                            }
                            foreach ($xml->xpath('//cfdi:Emisor') as $cfdiEmisor) { 
                                $rfc_emisor = $cfdiEmisor['Rfc'];
                            }
                            foreach ($xml->xpath('//cfdi:Receptor') as $cfdiReceptor) { 
                                $rfc_receptor = $cfdiReceptor['Rfc'];
                            }
                            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                                $uuid = $tfd['UUID'];
                                $fecha_timbrado = $tfd['FechaTimbrado'];
                            }
                            foreach ($xml->xpath('//cfdi:Concepto') as $cfdiConceptos) { 
                                $descripcion = ''.$cfdiConceptos['Descripcion'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            foreach ($xml->xpath('//cfdi:CfdiRelacionado') as $CfdiRelacionado) { 
                                $uuid_relacionado = ''.$CfdiRelacionado['UUID'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            $fecha_valida = explode('T', ''.$fecha_timbrado);
                        } else {
                            $contratoFactura->uuid = null;
                            $contratoFactura->monto_factura = 0;
                        }
                        //
                        $cadena_nombre = strtoupper($descripcion);
                        $mystring = 'AMORTI';
                        $mystring2 = 'NOTA DE CR';
                        $amortizacion = strpos($cadena_nombre, $mystring);
                        $nota_credito = strpos($cadena_nombre, $mystring2);
                        //
                        $contratoFactura = RemisionesFacturas::findFirst($doc_id);
                        $contratoFactura->setTransaction($tx);
                        $contratoFactura->uuid = ''.$uuid;
                        $contratoFactura->monto_factura = floatval($total_factura);
                        $contratoFactura->fecha_factura = ''.$fecha_valida[0];
                        $contratoFactura->descripcion = ''.$descripcion;
                        $contratoFactura->folio_xml = ''.$folio_xml;
                        if ($amortizacion !== false || $nota_credito !== false) {
                            $contratoFactura->amortizacion = true;
                            $bandera_amortizacion = true;
                        } else {
                            $contratoFactura->amortizacion = false;
                            $bandera_amortizacion = false;
                        }
                        if ($uuid_relacionado !== '') {
                            $facturaRelacionada = RemisionesFacturas::findFirst(
                            [
                                'id != :id: AND uuid = :uuid: AND remision_id != 0',
                                'bind' => [
                                    'id' => $doc_id,
                                    'uuid' => $uuid_relacionado
                                ]
                            ]);
                            if ($facturaRelacionada) {
                                $contratoFactura->factura_relacionada = $facturaRelacionada->id;
                            } else {
                                $contratoFactura->factura_relacionada = null;
                            }
                        } else {
                            $contratoFactura->factura_relacionada = null;
                        }

                        $existe_factura = false;
                        $existe_factura_contrato = false;
                        if ($uuid !== '') {
                            $existe_factura = RemisionesFacturas::findFirst(
                            [
                                'id != :id: AND uuid = :uuid: AND remision_id != 0',
                                'bind' => [
                                    'id' => $doc_id,
                                    'uuid' => $uuid
                                ]
                            ]);
                            if ($existe_factura !== false) {
                                $id_remision_factura = $existe_factura->remision_id;
                                $remision = Remisiones::findFirst($id_remision_factura);
                                if ($remision){
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en la remision ' . $remision->folio . ', por lo tanto debe descartarla.'];
                                }
                                    $this->content['result'] = 'success';
                                    $this->content['id'] = 0;
                                    $this->content['uuid'] = ''.$uuid;
                                    $this->content['monto_factura'] = ''.$total_factura;
                                    $this->content['metodo_pago'] = ''.$metodo_pago;
                                    $this->content['rfc_emisor'] = ''.$rfc_emisor; // el emisor es incompleto
                                    $this->content['rfc_receptor'] = ''.$rfc_receptor;
                                    $this->content['fecha_valida'] = ''.$fecha_valida[0];
                                    $this->content['id_empresa'] = $this->getEmpresa(str_split(''.$rfc_emisor, 5)); 
                                    $this->content['id_cliente'] = $this->getCliente(''.$rfc_receptor);
                                    $this->content['nombre'] = $descripcion;
                                    $this->content['amortizacion'] = $bandera_amortizacion;
                                    $tx->commit();
                            }
                            $existe_factura_contrato = contratosFacturas::findFirst(
                            [
                                'uuid = :uuid:',
                                'bind' => [
                                    'uuid' => $uuid
                                ]
                            ]);
                            if ($existe_factura_contrato !== false) {
                                $id_contrato_factura = $existe_factura_contrato->contrato_id;
                                $contrato = Contrato::findFirst($id_contrato_factura);
                                $proyecto = Recurso::findFirst($contrato->recurso_id);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en el contrato '. $contrato->nombre . ' del proyecto '. $proyecto->codigo . ' ' .$proyecto->nombre . '.'];
                                $this->content['result'] = 'success';
                                    $this->content['id'] = 0;
                                    $this->content['uuid'] = ''.$uuid;
                                    $this->content['monto_factura'] = ''.$total_factura;
                                    $this->content['metodo_pago'] = ''.$metodo_pago;
                                    $this->content['rfc_emisor'] = ''.$rfc_emisor; // el emisor es incompleto
                                    $this->content['rfc_receptor'] = ''.$rfc_receptor;
                                    $this->content['fecha_valida'] = ''.$fecha_valida[0];
                                    $this->content['id_empresa'] = $this->getEmpresa(str_split(''.$rfc_emisor, 5)); 
                                    $this->content['id_cliente'] = $this->getCliente(''.$rfc_receptor);
                                    $this->content['nombre'] = $descripcion;
                                    $this->content['amortizacion'] = $bandera_amortizacion;
                                    $tx->rollback();
                            }
                        }
                        if ($existe_factura == false && $contratoFactura->update() && $existe_factura_contrato == false) {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El archivo se ha procesado'];
                            $this->content['id'] = $contratoFactura->id;
                            $this->content['uuid'] = ''.$uuid;
                            $this->content['monto_factura'] = ''.$total_factura;
                            $this->content['metodo_pago'] = ''.$metodo_pago;
                            $this->content['rfc_emisor'] = ''.$rfc_emisor; // el emisor es incompleto
                            $this->content['rfc_receptor'] = ''.$rfc_receptor;
                            $this->content['fecha_valida'] = ''.$fecha_valida[0];
                            $this->content['id_empresa'] = $this->getEmpresa(str_split(''.$rfc_emisor, 5)); 
                            $this->content['id_cliente'] = $this->getCliente(''.$rfc_receptor);
                            $this->content['nombre'] = $descripcion;
                            $this->content['amortizacion'] = $bandera_amortizacion;                  
                            $tx->commit();
                        }
                    } else {
                        unlink($fullPath);
                        $this->content['error'] = Helpers::getErrors($contratoFactura);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe'];
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

    private function getFolio ($tipo) {
        if ($tipo == 'HISTÓRICO') {
            $anio = '2020';
        } else {
            $anio = date('Y');
        }
        $sql = "SELECT max(folio) as folio FROM vnt_remisiones where tipo = '$tipo' and folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = $anio . '00001';
        }
        return $nuevo_folio;
    }

    public function uploadAmortizacion()
    {   
        $content = $this->content;
        $request = $this->request->getPost();
        $tx = $this->transactions->get();
        try {
            if ($this->request->hasFiles())  {
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas_remisiones/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                foreach ($this->request->getUploadedFiles() as $file) {
                    $total_factura = 0;
                    $uuid = '';
                    $rfc_emisor = '';
                    $rfc_receptor = '';
                    $metodo_pago = '';
                    $fecha_timbrado = '';
                    $descripcion = '';
                    $folio_xml = '';
                    $uuid_relacionado = '';

                    $folio_origen = $request['folio_fiscal'];
                    $doc = trim(strtoupper($request['nombre']));
                    $validUser = Auth::getUserData($this->config);
                    $contratoFactura= new RemisionesFacturas();
                    $contratoFactura->setTransaction($tx);
                    $contratoFactura->remision_id = 0;
                    $contratoFactura->account_id = $validUser->account_id;
                    $contratoFactura->doc_type = $file->getExtension();
                    $contratoFactura->name=$doc;
                    $contratoFactura->year = date('Y');

                    if ($contratoFactura->create()) {
                        $doc_id=$contratoFactura->id;
                        $fullPath = $upload_dir . $doc_id.'.'. $file->getExtension();
                        $file->moveTo($fullPath);

                        if ($file->getExtension() === 'xml' || $file->getExtension() === 'XML') {
                            $xml = simplexml_load_file($fullPath);
                            $ns = $xml->getNamespaces(true);
                            $xml->registerXPathNamespace('c', $ns['cfdi']);
                            $xml->registerXPathNamespace('t', $ns['tfd']);

                            $total_factura = 0;          
                            foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                                $total_factura = $cfdiComprobante['Total'];
                                $metodo_pago = $cfdiComprobante['MetodoPago'];
                                $folio_xml = ''.$cfdiComprobante['Folio'];
                            }
                            foreach ($xml->xpath('//cfdi:Emisor') as $cfdiEmisor) { 
                                $rfc_emisor = $cfdiEmisor['Rfc'];
                            }
                            foreach ($xml->xpath('//cfdi:Receptor') as $cfdiReceptor) { 
                                $rfc_receptor = $cfdiReceptor['Rfc'];
                            }
                            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                                $uuid = $tfd['UUID'];
                                $fecha_timbrado = $tfd['FechaTimbrado'];
                            }
                            foreach ($xml->xpath('//cfdi:Concepto') as $cfdiConceptos) { 
                                $descripcion = ''.$cfdiConceptos['Descripcion'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            foreach ($xml->xpath('//cfdi:CfdiRelacionado') as $CfdiRelacionado) { 
                                $uuid_relacionado = ''.$CfdiRelacionado['UUID'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            $fecha_valida = explode('T', ''.$fecha_timbrado);
                        } else {
                            $contratoFactura->uuid = null;
                            $contratoFactura->monto_factura = 0;
                        }
                        //
                        $cadena_nombre = strtoupper($descripcion);
                        $mystring = 'AMORTI';
                        $mystring2 = 'NOTA DE CR';
                        $amortizacion = strpos($cadena_nombre, $mystring);
                        $nota_credito = strpos($cadena_nombre, $mystring2);
                        //
                        $contratoFactura = RemisionesFacturas::findFirst($doc_id);
                        $contratoFactura->setTransaction($tx);
                        $contratoFactura->uuid = ''.$uuid;
                        $contratoFactura->monto_factura = floatval($total_factura);
                        $contratoFactura->fecha_factura = ''.$fecha_valida[0];
                        $contratoFactura->descripcion = ''.$descripcion;
                        $contratoFactura->folio_xml = ''.$folio_xml;
                        if ($amortizacion !== false || $nota_credito !== false) {
                            $contratoFactura->amortizacion = true;
                            $bandera_amortizacion = true;
                        } else {
                            $contratoFactura->amortizacion = false;
                            $bandera_amortizacion = false;
                        }
                        if ($uuid_relacionado !== '') {
                            $facturaRelacionada = RemisionesFacturas::findFirst(
                            [
                                'id != :id: AND uuid = :uuid: AND remision_id != 0',
                                'bind' => [
                                    'id' => $doc_id,
                                    'uuid' => $uuid_relacionado
                                ]
                            ]);
                            if ($facturaRelacionada) {
                                $contratoFactura->factura_relacionada = $facturaRelacionada->id;
                            } else {
                                $contratoFactura->factura_relacionada = null;
                            }
                        } else {
                            $contratoFactura->factura_relacionada = null;
                        }

                        $existe_factura = false;
                        $existe_factura_contrato = false;
                        if ($uuid !== '') {
                            $existe_factura = RemisionesFacturas::findFirst(
                            [
                                'id != :id: AND uuid = :uuid: AND remision_id != 0',
                                'bind' => [
                                    'id' => $doc_id,
                                    'uuid' => $uuid
                                ]
                            ]);
                            if ($existe_factura !== false) {
                                $id_remision_factura = $existe_factura->remision_id;
                                $remision = Remisiones::findFirst($id_remision_factura);
                                if ($remision){
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en la remision ' . $remision->folio . ', por lo tanto debe descartarla.'];
                                }
                            }
                            $existe_factura_contrato = contratosFacturas::findFirst(
                            [
                                'uuid = :uuid:',
                                'bind' => [
                                    'uuid' => $uuid
                                ]
                            ]);
                            if ($existe_factura_contrato !== false) {
                                $id_contrato_factura = $existe_factura_contrato->contrato_id;
                                $contrato = Contrato::findFirst($id_contrato_factura);
                                $proyecto = Recurso::findFirst($contrato->recurso_id);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en el contrato '. $contrato->nombre . ' del proyecto '. $proyecto->codigo . ' ' .$proyecto->nombre . '.'];
                            }
                            /* if ($uuid_relacionado != $folio_origen) {
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta amortizacion o nota de crédito no corresponde a la factura'];
                            } */
                            if ($amortizacion === false && $nota_credito === false) {
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura no es una nota de crédito ni amortizacion, no se guardarán cambios'];
                            }
                        }
                        if ($existe_factura == false && $contratoFactura->update() && $existe_factura_contrato == false /* && ($folio_origen == $uuid_relacionado) */ ) {
                            //
                            $remisiones = new Remisiones();
                            $remisiones->setTransaction($tx);
                            $remisiones->fecha_venta = $fecha_valida[0];
                            $remisiones->empresa_id = $this->getEmpresa(str_split(''.$rfc_emisor, 5)); 
                            $remisiones->cliente_id = $this->getCliente(''.$rfc_receptor);
                            $remisiones->tipo = 'HISTÓRICO';
                            $remisiones->status = 'NUEVO';
                            $remisiones->folio = $this->getFolio('HISTÓRICO');
                            $remisiones->metodo_pago = $metodo_pago ? $metodo_pago : NULL;
                            $remisiones->status_cobranza = 'DESCONTADO';
                            if ($remisiones->create()) {
                                    $contratoFactura->setTransaction($tx);
                                    $contratoFactura->remision_id = $remisiones->id;
                                    $contratoFactura->name = $descripcion;
                                    if ($contratoFactura->update()) {
                                        $facturaRelacionada = RemisionesFacturas::findFirst(
                                        [
                                            'id != :id: AND uuid = :uuid: AND remision_id != 0',
                                            'bind' => [
                                                'id' => $doc_id,
                                                'uuid' => $uuid_relacionado
                                            ]
                                        ]);
                                        $remision_origen = Remisiones::findFirst($facturaRelacionada->remision_id);
                                        $remision_origen->setTransaction($tx);
                                        $remision_origen->status_cobranza = 'DESCONTADO';
                                        if ($remision_origen->update()) {
                                            $this->content['result'] = 'success';
                                            $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'La nota de crédito/amortización ha sido subida, la factura pasará a ser descontada']; 
                                            $tx->commit();
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($remision_origen);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
                                            $tx->rollback();
                                        }
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($contratoFactura);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
                                        $tx->rollback();
                                    }
                                // }
                            } else {
                                $this->content['error'] = Helpers::getErrors($remisiones);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la remisión.'];
                                $tx->rollback();
                            }
                        }
                    } else {
                        unlink($fullPath);
                        $this->content['error'] = Helpers::getErrors($contratoFactura);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe'];
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

    public function agregarNombre () {
        $content = $this->content;
        $tx = $this->transactions->get();
        try {
            $facturas = RemisionesFacturas::find();
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas_remisiones/';
            foreach ($facturas as $factura) {
                $doc = 0;
                $doc = $factura->id;
                $doc_type = $factura->doc_type;
                $factura->setTransaction($tx);
                $fullPath = $upload_dir . $doc.'.'. $doc_type;
                $descripcion = '';

                $xml = simplexml_load_file($fullPath);
                $ns = $xml->getNamespaces(true);
                $xml->registerXPathNamespace('c', $ns['cfdi']);
                $xml->registerXPathNamespace('t', $ns['tfd']);

                $total_factura = 0;          
                foreach ($xml->xpath('//cfdi:Concepto') as $cfdiConceptos) { 
                    $descripcion = ''.$cfdiConceptos['Descripcion'];
                    // var_dump($cfdiConceptos['Descripcion']);
                }
                foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                    $folio_xml = ''.$cfdiComprobante['Folio'];
                    // var_dump($cfdiConceptos['Descripcion']);
                }
                $factura->descripcion = $descripcion;
                $factura->folio_xml = $folio_xml;
                if ($factura->update()) {
                    $this->content['result'] = 'success';
                } else {
                    $this->content['error'] = Helpers::getErrors($factura);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la factura'];
                    $tx->rollback();
                }
            }
            if ($this->content['result'] == 'success') {
                $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El archivo se ha procesado'];
                $tx->commit();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function agregarNombreFacturas () {
        $content = $this->content;
        $tx = $this->transactions->get();
        try {
            $facturas = contratosFacturas::find();
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas/';
            foreach ($facturas as $factura) {
                $doc = 0;
                $doc = $factura->document_id;
                $documento = SysDocuments::findFirst($doc);
                $doc_type = $documento->doc_type;
                $factura->setTransaction($tx);
                $fullPath = $upload_dir . $doc.'.'. $doc_type;
                $descripcion = '';

                $xml = simplexml_load_file($fullPath);
                $ns = $xml->getNamespaces(true);
                $xml->registerXPathNamespace('c', $ns['cfdi']);
                $xml->registerXPathNamespace('t', $ns['tfd']);

                $total_factura = 0;          
                foreach ($xml->xpath('//cfdi:Concepto') as $cfdiConceptos) { 
                    $descripcion = ''.$cfdiConceptos['Descripcion'];
                    // var_dump($cfdiConceptos['Descripcion']);
                }
                foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) { 
                    $folio_xml = ''.$cfdiComprobante['Folio'];
                    $subtotal_monto_factura = floatval($cfdiComprobante['SubTotal']);
                    // var_dump($cfdiConceptos['Descripcion']);
                }
                $factura->descripcion = $descripcion;
                $factura->folio_xml = $folio_xml;
                $factura->subtotal_monto_factura = floatval($subtotal_monto_factura);
                if ($factura->update()) {
                    $this->content['result'] = 'success';
                } else {
                    $this->content['error'] = Helpers::getErrors($factura);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la factura'];
                    $tx->rollback();
                }
            }
            if ($this->content['result'] == 'success') {
                $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El archivo se ha procesado'];
                $tx->commit();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getEmpresa ($rfc) {
        $rfc = $rfc[0];
        $sql = "SELECT id from vnt_empresa where rfc like '%$rfc%' and padre_id is null";
        $data = $this->db->query($sql)->fetchAll();
        if ($data) {
            return $data[0]['id'];
        } else {
            return 0;
        }
    }

    private function getCliente ($rfc) {
        $sql = "SELECT id from vnt_clientes where rfc like '%$rfc%'";
        $data = $this->db->query($sql)->fetchAll();
        if ($data) {
            return $data[0]['id'];
        } else {
            return 0;
        }
    }
    
    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                $remisionesFacturas = RemisionesFacturas::findFirst($request['id']);
                if ($remisionesFacturas) {
                    $remisionesFacturas->setTransaction($tx);
                    if ($remisionesFacturas->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($remisionesFacturas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el documento.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
    public function getFile($id,$ext)
    {    
        $remisionesFacturas = RemisionesFacturas::findFirst($id);

        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas_remisiones/' .$remisionesFacturas->id.'.'.$ext;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, "\"".$remisionesFacturas->name.'.'.$ext."\"");
        $response->send();
        return $response;
    }

    public function getRemisionesRepetidas () {
        $content = $this->content;
        $tx = $this->transactions->get();
        try {
            // $tx = $this->transactions->get();
            $remisionesFacturas = RemisionesFacturas::find();
            if ($remisionesFacturas) {
                foreach ($remisionesFacturas as $remisionF) {
                    $remisionF->setTransaction($tx);
                    $existe_factura = contratosFacturas::findFirst(
                        [
                            'uuid = :uuid:',
                                'bind' => [
                                    'uuid' => ''.$remisionF->uuid
                                ]
                        ]
                    );
                    if ($existe_factura) {
                        $id_remision_factura = $remisionF->remision_id;
                        $remision = Remisiones::findFirst($id_remision_factura);
                        if ($remision) {
                            $remisionF->repetido = true;
                            $remisionF->contrato_id = $existe_factura->contrato_id;
                            if ($remisionF->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($remisionF);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta remision no se pudo actualizar'];
                                $tx->rollback();
                            }
                        } else {
                            $remisionF->repetido = true;
                            $remisionF->contrato_id = $existe_factura->contrato_id;
                        }
                    } else {
                    }
                }
            }
            if ($this->content['result'] === 'success') {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El Archivo se ha subido con éxito'];                            
                $tx->commit();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}
