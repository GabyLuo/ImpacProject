<?php

use Phalcon\Mvc\Controller;

class ContratoFacturas extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getFacturas ($contrato)
    {
        if(intval($contrato) === 0) {
            $where = " ";
        } else {
            $where = " and vnt_contratos_facturas.contrato_id = $contrato ";
        }
        $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,
                vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,
                vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
                vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
                vnt_clientes.razon_social as cliente,vnt_contratos_facturas.id as factura_id,vnt_contratos_facturas.folio_xml, vnt_contratos_facturas.contrato_id,vnt_contratos_facturas.fecha_factura,vnt_contratos_facturas.fecha_pago,vnt_contratos_facturas.fecha_vencimiento,vnt_contratos_facturas.document_id,sys_documents.doc_type,sys_documents.name,vnt_contratos_facturas.monto_factura,vnt_contratos_facturas.uuid as folio_fiscal,vnt_contratos_facturas.status, (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id), 0)) as monto_total_abonado, (vnt_contratos_facturas.monto_factura - (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id), 0))) as monto_restante, vnt_recurso.codigo, vnt_contratos_facturas.cancelada, sy.id as pdf_id, sy.doc_type as doc_type_pdf, sy.name as name_pdf, case when (vnt_contratos_facturas.status = 'DESCONTADO' or factura_relacionada is not null) then true else false end as nota_credito
                from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id
                join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id
                join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id
                join vnt_contratos_facturas on vnt_contratos_facturas.contrato_id = vnt_contrato.id" . $where ."
                join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
                left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id
                left join sys_documents as sy on sy.id = vnt_contratos_facturas.document_pdf_id order by vnt_contratos_facturas.id
                --and vnt_contratos_facturas.contrato_id = $contrato";

        $sql_total = "SELECT ((select coalesce(sum(vnt_contratos_facturas.monto_factura), 0)
                from vnt_contratos_facturas where contrato_id  = $contrato and cancelada = false) -
                (select coalesce(sum(vnt_contratos_facturas.monto_factura), 0) as total
                from vnt_contratos_facturas where contrato_id  = $contrato and status = 'DESCONTADO')) as total
                from vnt_contratos_facturas where contrato_id  = $contrato";
        $facturas = $this->content['facturas'] = $this->db->query($sql)->fetchAll();
        $total = $this->db->query($sql_total)->fetchAll()[0]['total'];
        $this->content['total'] = number_format($total,2,'.',',');
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasByContrato ($contrato)
    {
        if(intval($contrato) === 0) {
            $where = " ";
        } else {
            $where = " and vnt_contratos_facturas.contrato_id = $contrato ";
        }
        $sql = "SELECT vnt_contratos_facturas.id,sys_documents.name from vnt_contratos_facturas
                join vnt_contrato on vnt_contratos_facturas.contrato_id = vnt_contrato.id" . $where ."
                join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id";
        $facturas = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($facturas as $factura) {
            $array['label'] = '' . $factura['name'];
            $array['value'] = $factura['id'];
            array_push($niveles, $array);
        }
        $this->content['facturasOptions'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFacturasByContratoandId ($proyecto, $cliente, $contrato, $factura, $year, $status, $empresa_id) {
        if(intval($contrato) === 0) {
            $where = " ";
        } else {
            $where = " and vnt_contratos_facturas.contrato_id = $contrato ";
        }
        if(intval($factura) === 0) {
            $and = " ";
        } else {
            $and = " and vnt_contratos_facturas.id = $factura";
        }
        if(intval($proyecto) === 0) {
            $rec = " ";
        } else {
            $rec = " and vnt_contrato.recurso_id = $proyecto ";
        }
        if(intval($empresa_id) === 0) {
            $emp = " ";
        } else {
            $emp = " and vnt_empresa.id = $empresa_id ";
        }
        if(intval($cliente) === 0) {
            $cli = " ";
        } else {
            $cli = " and vnt_recurso.cliente_id = $cliente ";
        }
        if ($status != 'all') {
            $and_status = " and vnt_contratos_facturas.status = '$status'";
        } else {
            $and_status = "";
        }

        $sql = "SELECT vnt_contrato.id,vnt_contrato.num_contrato,vnt_contrato.recurso_id,vnt_recurso.codigo as recurso,
                vnt_contrato.empresa_id,vnt_empresa.razon_social,vnt_contrato.fecha_inicio,
                vnt_contrato.fecha_fin,vnt_contrato.monto_total,vnt_contrato.rep_legal_contrato,
                vnt_contrato.licitacion_id, (lic_licitacion.folio || ' - ' ||lic_licitacion.titulo) as licitacion,
                vnt_clientes.razon_social as cliente,vnt_contratos_facturas.id as factura_id, vnt_contratos_facturas.folio_xml, vnt_contratos_facturas.contrato_id,vnt_contratos_facturas.fecha_factura,vnt_contratos_facturas.fecha_pago,vnt_contratos_facturas.fecha_vencimiento,vnt_contratos_facturas.document_id,sys_documents.doc_type,sys_documents.name,
                case when vnt_contratos_facturas.status = 'PAGADO' or vnt_contratos_facturas.status = 'DESCONTADO' then sys_documents.name else ' - ' end as name_factura,
                vnt_contratos_facturas.monto_factura,vnt_contratos_facturas.folio_fiscal,vnt_contratos_facturas.status, (select sum(suma) from (select coalesce((select sum(monto_factura) from vnt_contratos_facturas as a where a.factura_relacionada = vnt_contratos_facturas.uuid and vnt_contratos_facturas.cancelada = false),0) as suma
                union
                select coalesce((select sum(monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id),0 ) as suma) as w) as monto_total_abonado, 
                case 
                when vnt_contratos_facturas.status != 'DESCONTADO' then (vnt_contratos_facturas.monto_factura - (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id), 0))) 
                when vnt_contratos_facturas.status = 'DESCONTADO' and vnt_contratos_facturas.factura_relacionada is not null then 0
                when vnt_contratos_facturas.status = 'DESCONTADO' and vnt_contratos_facturas.factura_relacionada is null then vnt_contratos_facturas.monto_factura - ((SELECT COALESCE((select sum(a.monto_factura) from vnt_contratos_facturas as a where a.factura_relacionada = vnt_contratos_facturas.uuid and vnt_contratos_facturas.cancelada = false),0)) + (select COALESCE((SELECT sum(monto) from vnt_facturas_abonos where factura_id = vnt_contratos_facturas.id), 0))) end as monto_restante, vnt_recurso.codigo, vnt_contratos_facturas.cancelada, vnt_contratos_facturas.monto_factura as monto_total_factura, vnt_empresa.razon_social as empresa
                from vnt_contrato join vnt_recurso on vnt_contrato.recurso_id = vnt_recurso.id".$rec." and vnt_contrato.year = '$year'
                join vnt_clientes on vnt_clientes.id = vnt_recurso.cliente_id".$cli."
                join vnt_empresa on vnt_empresa.id = vnt_contrato.empresa_id {$emp}
                join vnt_contratos_facturas on vnt_contratos_facturas.contrato_id = vnt_contrato.id".$and . $where ." {$and_status}
                join sys_documents on sys_documents.id = vnt_contratos_facturas.document_id
                left join lic_licitacion on lic_licitacion.id = vnt_contrato.licitacion_id";
        $facturas = $this->db->query($sql)->fetchAll();
        foreach ($facturas as $key => $factura) {
            $facturas[$key]['monto_factura_total'] = number_format($facturas[$key]['monto_factura'],2,'.',',');
        }
        $this->content['facturas'] = $facturas;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function uploadArchivo()
    {   
        $content = $this->content;
        $request = $this->request->getPost();
        $tx = $this->transactions->get();

        try {
            if ($this->request->hasFiles())  {
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }
                foreach ($this->request->getUploadedFiles() as $file) {
                    $existe_factura = false;
                    $total_factura = 0;
                    $uuid = '';
                    $sobrepasa_monto = false;
                    $amortizacion = false;
                    $folio_xml = '';
                    $descripcion = '';
                    $fecha_timbrado = '';
                    $uuid_relacionado = null;

                    $doc = trim(strtoupper($request['nombre']));
                    $fecha_factura = trim(strtoupper($request['fecha_factura']));
                    $fecha_pago = trim(strtoupper($request['fecha_pago']));
                    $fecha_vencimiento = trim(strtoupper($request['fecha_vencimiento']));
                    $contrato_id = trim(strtoupper($request['contrato_id']));
                    $folio_fiscal = trim(strtoupper($request['folio_fiscal']));

                    $documento = new SysDocuments();
                    $documento->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $documento->account_id = $validUser->account_id;
                    $documento->doc_type = $file->getExtension();
                    $documento->name=$doc;

                    if ($documento->create()) {
                        $doc_id=$documento->id;
                        $contratoFactura= new contratosFacturas();
                        $contratoFactura->setTransaction($tx);
                        $contratoFactura->contrato_id=$contrato_id;
                        // $contratoFactura->fecha_factura=$fecha_factura;
                        $contratoFactura->fecha_pago=$fecha_pago == '' ? null : $fecha_pago;
                        $contratoFactura->fecha_vencimiento=$fecha_vencimiento == '' ? null : $fecha_vencimiento;
                        $contratoFactura->document_id=$doc_id;
                        $contratoFactura->folio_fiscal=$folio_fiscal == '' ? null : $folio_fiscal;

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
                                $subtotal_factura = $cfdiComprobante['SubTotal']; 
                                $folio_xml = ''.$cfdiComprobante['Folio'];
                            }
                            foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
                                $contratoFactura->uuid = ''.$tfd['UUID'];
                                $uuid = ''.$tfd['UUID'];
                                $contratoFactura->monto_factura = floatval($total_factura);
                                $contratoFactura->subtotal_monto_factura = floatval($subtotal_factura);
                                $fecha_timbrado = ''.$tfd['FechaTimbrado'];
                                $fecha_valida = explode('T', $fecha_timbrado);
                            }
                            foreach ($xml->xpath('//cfdi:Concepto') as $cfdiConceptos) { 
                                $descripcion = ''.$cfdiConceptos['Descripcion'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            foreach ($xml->xpath('//cfdi:CfdiRelacionado') as $CfdiRelacionado) { 
                                $uuid_relacionado = ''.$CfdiRelacionado['UUID'];
                                // var_dump($cfdiConceptos['Descripcion']);
                            }
                            $contratoFactura->folio_xml = $folio_xml;
                            $contratoFactura->descripcion = $descripcion;
                            $contratoFactura->fecha_factura = $fecha_valida[0];
                            $contratoFactura->factura_relacionada = $uuid_relacionado;
                        } else {
                            $contratoFactura->uuid = null;
                            $contratoFactura->monto_factura = 0;
                        }

                        $mystring = 'AMORTI';
                        $mystring2 = 'NOTA DE CR';
                        $amortizacion = false; // strpos($descripcion, $mystring);
                        $nota_credito = false; // strpos($descripcion, $mystring2);

                        $existe_factura = false;
                        $existe_factura_remision = false;
                        if ($uuid !== '') {
                            $existe_factura = contratosFacturas::findFirst(
                                [
                                    'uuid = :uuid:',
                                    'bind' => [
                                        'uuid' => ''.$uuid
                                    ]
                                ]
                            );
                            if ($existe_factura !== false) {
                                $id_contrato_factura = $existe_factura->contrato_id;
                                $contrato = Contrato::findFirst($id_contrato_factura);
                                $proyecto = Recurso::findFirst($contrato->recurso_id);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en el contrato '. $contrato->nombre . ' del proyecto '. $proyecto->codigo . ' ' .$proyecto->nombre . '.'];
                            }
                            $existe_factura_remision = RemisionesFacturas::findFirst(
                            [
                                'uuid = :uuid: AND remision_id != 0',
                                'bind' => [
                                    'uuid' => ''.$uuid
                                ]
                            ]);
                            if ($existe_factura_remision != false) {
                                $id_remision_factura = $existe_factura_remision->remision_id;
                                $remision = Remisiones::findFirst($id_remision_factura);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Esta factura ya existe en la remision ' . $remision->folio . '.'];
                            }
                        }
                        $sobrepasa_monto = false;
                        $monto_facturas = contratosFacturas::find(
                            [
                                "contrato_id = :contrato_id: AND cancelada = false AND descripcion NOT LIKE "."'%{$mystring}%'". " AND descripcion NOT LIKE "."'%{$mystring2}%'",
                                'bind' => [
                                    'contrato_id' => $contrato_id
                                ]
                            ]
                        );
                        $sumatoria_total_facturas = 0;
                        if (count($monto_facturas) > 0) {
                            foreach ($monto_facturas as $mf) {
                                $sumatoria_total_facturas = $sumatoria_total_facturas + $mf->monto_factura;
                            }
                            // var_dump($sumatoria_total_facturas);
                            $sumatoria_total_facturas = $sumatoria_total_facturas + floatval($total_factura);
                        }
                        // var_dump($sumatoria_total_facturas);
                        /* $contrato = Contrato::findFirst($contrato_id);
                        if ($contrato->monto_total < $sumatoria_total_facturas) {
                            $sobrepasa_monto = true;
                        } */
                        /* if ($sobrepasa_monto != false && $amortizacion == false && $nota_credito == false) {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Al agregar esta factura se sobrepasa el monto del contrato, no se guardarán los cambios.'];
                        } */
                        if ($amortizacion !== false) {
                            $contratoFactura->status = 'DESCONTADO';
                        }
                        if ($nota_credito !== false) {
                            $contratoFactura->status = 'DESCONTADO';
                        }
                        $contratoFactura->cancelada = false;
                        if (($existe_factura == false && $sobrepasa_monto == false) || ($existe_factura == false && $sobrepasa_monto != false && ($amortizacion == 0 || $nota_credito == 0))) {
                            if ($contratoFactura->create()) {
                                $documento = SysDocuments::findFirst($doc_id);
                                if ($documento) {
                                    $documento->setTransaction($tx);
                                    $documento->name = strtoupper($descripcion);
                                    if ($documento->update()) {
                                        if ($amortizacion == 0 || $nota_credito == 0) {
                                            $factura_nota = contratosFacturas::findFirst(
                                                [
                                                    'uuid = :uuid: AND cancelada = false',
                                                    'bind' => [
                                                        'uuid' => $uuid_relacionado
                                                    ]
                                                ]
                                            );
                                            if ($factura_nota) {
                                                $factura_nota->setTransaction($tx);
                                                $factura_nota->status = 'DESCONTADO';
                                                if ($factura_nota->update()) {
                                                    $this->content['result'] = 'success';
                                                    $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El Archivo se ha subido con éxito'];                            
                                                    $tx->commit();
                                                } else {
                                                    $this->content['error'] = Helpers::getErrors($factura_nota);
                                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo subir el archivo'];
                                                    $tx->rollback();
                                                }
                                            } else {
                                                $this->content['result'] = 'success';
                                                $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El Archivo se ha subido con éxito'];                            
                                                $tx->commit();
                                            }
                                        } else {
                                            $this->content['result'] = 'success';
                                            $this->content['message'] = ['title' => '¡Éxito!', 'content' =>  'El Archivo se ha subido con éxito'];                            
                                            $tx->commit();
                                        }
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($documento);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo subir el archivo'];
                                        $tx->rollback();
                                    }
                                } else {
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo subir el archivo2'];
                                }
                            } else {
                                $this->content['error'] = Helpers::getErrors($contratoFactura);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo subir el archivo'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Error.'];
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($documento);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro documentos.'];
                        $tx->rollback();
                    }
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL];
        }
       $this->response->setJsonContent($this->content);
       $this->response->send();
    }

    public function uploadArchivoPDF()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='' || strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx' || strtolower($file->getExtension())==='00' || strtolower($file->getExtension())==='txt'){
                            
                            if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                $nombrep = $request['nombre'];
                                $nombreo = explode(".", $nombrep);
                                $img = $nombreo[0];
                                $nombre = $nombreo[0];
                            } else {
                                $img = $request['nombre'];
                                $nombre = $request['nombre'];
                            }

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                    $documento->doc_type = 'pdf';
                                } else {
                                    $documento->doc_type = $file->getExtension();
                                }
                                $documento->name=$img;

                                if ($documento->create()) {
                                    $id = $documento->id;
                                    if ($file->getExtension() === '00' || $file->getExtension() === '') {
                                        $nombre_con_id = $id .'.'. 'pdf';
                                    } else {
                                        $nombre_con_id = $id .'.'. $file->getExtension();
                                    }
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
                                    }

                                    $factura_nota = contratosFacturas::findFirst($request['id']);
                                    if ($factura_nota) {
                                        $factura_nota->setTransaction($tx);
                                        $factura_nota->document_pdf_id = $documento->id;

                                        if ($factura_nota->update()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Exito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                            $this->content['error'] = Helpers::getErrors($factura_nota);
                                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la factura.'];
                                            $tx->rollback();
                                        }
                                    }
                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
                        }
                    }
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
            }
        } catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function cancelarFactura ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPut();
            if ($request['id']) {
                $contratosFacturas = contratosFacturas::findFirst($request['factura_id']);
                if ($contratosFacturas) {
                    $contratosFacturas->setTransaction($tx);
                    if ($contratosFacturas->cancelada == true) {
                        $contratosFacturas->cancelada = false;
                    } else {
                        $contratosFacturas->cancelada = true;
                    }
                    if ($contratosFacturas->update()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($contratosFacturas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la factura.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function actualizarFactura () {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPut();
            if ($request['id']) {
                $contratosFacturas = contratosFacturas::findFirst($request['factura_id']);
                if ($contratosFacturas) {
                    $contratosFacturas->setTransaction($tx);
                    if ($contratosFacturas->status == 'DESCONTADO') {
                        $contratosFacturas->factura_relacionada = null;
                        $abonos = AbonosFacturas::find('factura_id = ' . $contratosFacturas->id);
                        $suma = 0;
                        foreach ($abonos as $abono) {
                            $suma = $suma + $abono->monto;
                        }
                        if ((floatval($suma) === floatval(($contratosFacturas->monto_factura) - 0.2)) || (floatval($suma) > floatval($contratosFacturas->monto_factura)) || (floatval($suma) === floatval($contratosFacturas->monto_factura))) {
                            $contratosFacturas->status='PAGADO';
                        } else {
                            if ($suma == 0) {
                                $contratosFacturas->status='NUEVO';
                            } else {
                                $contratosFacturas->status='ABONADO'; 
                            }
                        }
                    } else {
                        $contratosFacturas->factura_relacionada = $contratosFacturas->uuid;
                        $contratosFacturas->status = 'DESCONTADO';
                    }
                    if ($contratosFacturas->update()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($contratosFacturas);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                        }
                    }                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la factura.'];
                    $tx->commit();
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
                $contratosFacturas = contratosFacturas::findFirst($request['id']);
                $uuid = $contratosFacturas->uuid;
                if ($contratosFacturas) {
                    $contratosFacturas->setTransaction($tx);
                    if ($contratosFacturas->delete()) {
                          $logs = new Logs();
                          $logs->setTransaction($tx);
                          $validUser = Auth::getUserData($this->config);
                          $logs->account_id = $validUser->user_id;
                          $logs->level_id = 14;
                          $logs->log ='ELIMINÓ LA FACTURA CON FOLIO'.$uuid;
                          $logs->fecha = date("Y-m-d H:i:s");
                          if($logs->create()) {
                            $this->content['result'] = 'success';
                          } else {
                            $this->content['error'] = Helpers::getErrors($logs);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                            $tx->rollback();
                          }
                    } else {
                        $this->content['error'] = Helpers::getErrors($contratosFacturas);
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
        // $contratosFacturas = contratosFacturas::findFirst($id);
        $documento = SysDocuments::findFirst($id);

                $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/facturas/' .$documento->id.'.'.$ext;
                $filetype = mime_content_type($path);   
                $filesize = filesize($path);       
                $response = new \Phalcon\Http\Response();
                $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
                $response->setHeader("Content-Length", $filesize);
                $response->setContentType($filetype);
                $response->setFileToSend($path, "\"".$documento->name."\"");
                $response->send();
                return $response;
    }
}
