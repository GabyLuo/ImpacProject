<?php

use Phalcon\Mvc\Controller;

class PDFShopping extends FPDF
{

    function encabezado($id_compra, $empresa_id) {
        $this->SetFont('Nunito-Regular','',12);

        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/LogosERP/';
        $logo=$image_path.$empresa_id.'.png';
        if(file_exists($logo)){
            $this->Image($logo,5,10,60,40);
        }
        $this->SetXY(($this->GetPageWidth()/2)+58,14);
        $this->SetFont('Nunito-Regular','',14);
        /* $this->SetTextColor(8,85,134);
        $this->Cell(0,0,'Orden de compra');

        $this->SetXY(($this->GetPageWidth()/2)+64,15);
        $this->SetFont('Nunito-Regular','',10);
        $this->SetTextColor(0);
        $this->Cell(0,10,'Fecha: '.date('d').'-'.date('m').'-'.date('Y')); */

        $header = array('#','Producto',utf8_decode('Presentación'),'Cantidad','Costo unitario','SubTotal');
        $this->SetXY(5,80);
        $this->SetFillColor(8, 85, 134);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Nunito-Regular','',9);
        // Header
        $x = 168;
        $i = 0;
        // $w=array(5,20,25,30,25,30,35,20,15);
        $w=array(10,158,27,17,28,28);
        foreach($header as $col) {
            if($i<=6){
                $this->Cell($w[$i],7,$col,1,0,'C',true);
            }
            $x=$x+5;
            $i++;
        }

        $compra = Compras::findFirst($id_compra);
        $compra_id = $compra->id;
        $empresa_id = $compra->empresa_id;
        $empresa = Empresa::findFirst($empresa_id);

        $this->SetTextColor(0);

        $this->SetXY(211,5);
        $this->SetFont('Arial','B',11);
        $this->MultiCell(65,5, 'ORDEN DE COMPRA: '.utf8_decode($compra->folio), 0, 'L');
        
        $this->SetFont('Nunito-Regular','',9);
        $this->SetTextColor(0);
        $this->SetXY(5,10);
        $this->Cell(115, 50, '' ,1 , 0, 'C');

        $this->SetXY(67,10);
        $this->SetFont('Arial','B',9);
        $this->MultiCell(50,5, utf8_decode($empresa->razon_social), 0, 'L');
        $this->SetFont('Nunito-Regular','',9);

        $direccion = Direccion::findFirst('empresa_id ='.$empresa_id);
        if ($direccion) {
            $estado = Estado::findFirst($direccion->estado_id);
            $municipio = Municipio::findFirst($direccion->municipio_id);
            $localiad = "";
            if ($municipio) {
                $localiad = $municipio->nombre;
            }
            if ($estado) {
                $localiad = $localiad . ', ' . $estado->nombre;
            }
            if ($direccion) {
                $this->SetXY(67,$this->getY());
                $this->MultiCell(50,5, utf8_decode($direccion->calle . ' No.' . $direccion->num_ext), 0, 'L');
                $this->SetXY(67,$this->getY());
                $this->MultiCell(50,5, utf8_decode($direccion->colonia . '., ' . $localiad), 0, 'L');
                $this->SetXY(67,$this->getY());
                $this->MultiCell(50,5, utf8_decode('R.F.C: ' . $empresa->rfc), 0, 'L');
                $this->SetXY(67,$this->getY());
                $this->MultiCell(50,5, utf8_decode('C.P: ' . $direccion->cp), 0, 'L');
            }
        }

        $this->SetXY(120,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C');

        $this->SetXY(171,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C');

        $this->SetXY(222,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C');

        $this->SetFont('Arial','B',9);
        $this->SetXY(121,10);
        $proveedor = Proveedores::findFirst($compra->proveedor_id);
        if ($proveedor) {
            $this->MultiCell(49,5,utf8_decode($proveedor->razon_social),0,'L');
            $this->SetFont('Nunito-Regular','',9);
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,utf8_decode($proveedor->direccion),0,'L');
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,'R.F.C. '.utf8_decode($proveedor->rfc),0,'L');
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,'Tel. '.utf8_decode($proveedor->telefono),0,'L');
        }

        $this->SetFont('Arial','B',9);
        $this->SetXY(172,10);
        $this->MultiCell(49,5,utf8_decode('CONDICIONES DE PAGO:'),0,'L');
        $this->SetFont('Nunito-Regular','',9);
        $this->SetXY(172,$this->getY());
        $this->MultiCell(49,5,utf8_decode($compra->condiciones_pago),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Arial','B',9);
        $this->MultiCell(49,5,utf8_decode('CONDICIONES DE ENTREGA:'),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Nunito-Regular','',9);
        $this->MultiCell(49,5,utf8_decode($compra->condiciones_entrega),0,'L');
        $this->SetFont('Arial','B',9);
        $this->SetXY(172,$this->getY());
        $this->MultiCell(49,5,utf8_decode('TRANSPORTE:'),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Nunito-Regular','',9);
        $this->MultiCell(49,5,utf8_decode($compra->transporte),0,'L');

        $this->SetFont('Arial','B',9);
        $this->SetXY(223,10);
        $this->MultiCell(49,5,utf8_decode('EFECTUAR ENTREGA EN:'),0,'L');
        //
        $empresa = Empresa::findFirst($compra->empresa_id);
        if ($compra->entrega === 'OTRO') {
            $this->SetXY(223,$this->getY());
            $this->SetFont('Nunito-Regular','',10);
            $this->MultiCell(49,5,utf8_decode($compra->direccion_entrega),0,'L');
        } else {
            if ($compra->entrega === 'EMPRESA') {
                if ($compra->direccion_id !== null) {
                    $direccion_entrega = Direccion::findFirst($compra->direccion_id);
                    if ($direccion_entrega) {
                        $estado = Estado::findFirst($direccion_entrega->estado_id);
                        $municipio = Municipio::findFirst($direccion_entrega->municipio_id);
                        $localiad = "";
                        if ($municipio) {
                            $localiad = $municipio->nombre;
                        }
                        if ($estado) {
                            $localiad = $localiad . ', ' . $estado->nombre;
                        }
                        if ($direccion_entrega) {
                            $this->SetFont('Nunito-Regular','',9);
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode($direccion_entrega->calle . ' No.' . $direccion_entrega->num_ext), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode($direccion_entrega->colonia . '., ' . $localiad), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode('R.F.C: ' . $empresa->rfc), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode('C.P: ' . $direccion_entrega->cp), 0, 'L');
                        }
                    }
                }
            }
            if ($compra->entrega === 'SUCURSAL') {
                if ($compra->sucursal_id !== null) {
                    $sucursal_entrega = EmpresasSucursales::findFirst($compra->sucursal_id);
                    if ($sucursal_entrega) {
                        $estado = Estado::findFirst($sucursal_entrega->estado_id);
                        $municipio = Municipio::findFirst($sucursal_entrega->municipio_id);
                        $localiad = "";
                        if ($municipio) {
                            $localiad = $municipio->nombre;
                        }
                        if ($estado) {
                            $localiad = $localiad . ', ' . $estado->nombre;
                        }
                        if ($sucursal_entrega) {
                            $this->SetFont('Nunito-Regular','',9);
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode($sucursal_entrega->calle . ' No.' . $sucursal_entrega->num_ext), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode($sucursal_entrega->colonia . '., ' . $localiad), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode('R.F.C: ' . $empresa->rfc), 0, 'L');
                            $this->SetXY(223,$this->getY());
                            $this->MultiCell(49,5, utf8_decode('C.P: ' . $sucursal_entrega->cp), 0, 'L');
                        }
                    }
                }
            }
        }
        /* if ($empresa) {
            $this->MultiCell(49,5,utf8_decode($proveedor->razon_social),0,'L');
            $this->SetFont('Nunito-Regular','',9);
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,utf8_decode($proveedor->direccion),0,'L');
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,'R.F.C. '.utf8_decode($proveedor->rfc),0,'L');
            $this->SetXY(121,$this->getY());
            $this->MultiCell(49,5,'Tel. '.utf8_decode($proveedor->telefono),0,'L');
        }

        $this->SetXY(223,$this->getY());
        $this->SetFont('Nunito-Regular','',9);
        $this->MultiCell(49,5,utf8_decode($compra->transporte),0,'L'); */

        $this->SetXY(6,65);
        $this->Cell(0,0,'Folio: '.$compra->folio);
        
        $this->SetXY(50,65);
        $this->Cell(0,0,'Fecha: '.$compra->fecha);

        $this->SetXY(121,65);
        $this->Cell(0,0,'Estatus: '.$compra->status);
        

        $this->SetXY(172,62);
        if ($compra->project_id != null) {
            $this->MultiCell(110,5,'Presupuesto: '.utf8_decode(Proyecto::findFirst($compra->project_id)->nombre),0,'L');
        } else {
            $this->MultiCell(60,5,'Presupuesto: ',0,'L');
        }
    }

    // Page footer
    //
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Nunito-Regular','',8);
        // Page number
        $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
    }

    //Multicell
    //
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowMovimientos($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            if($i==0) {
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            } else if ($i>0 && $i<3) {
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            } else if ($i>=3){
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'R';
            }
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}
class ComprasController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT cmp_compras.*, to_char(cmp_compras.fecha, 'DD-MM-YYYY') as fecha_compra, (select pmo_proveedores.razon_social as proveedor from pmo_proveedores where pmo_proveedores.id = cmp_compras.proveedor_id), vnt_empresa.razon_social as empresa
            FROM cmp_compras
            inner join vnt_empresa on vnt_empresa.id = cmp_compras.empresa_id
            ORDER BY id";
        $this->content['compras'] = $this->db->query($sql)->fetchAll();
        foreach ($this->content['compras'] as $key => $compra) {
            $compra_id = $compra['id'];
            if ($compra['status'] == 'NUEVO') {
                $this->content['compras'][$key]['icon'] = 'add';
                $this->content['compras'][$key]['color'] = 'amber';
            }
            if ($compra['status'] == 'SOLICITADO') {
                $this->content['compras'][$key]['icon'] = 'fas fa-arrow-circle-right';
                $this->content['compras'][$key]['color'] = 'cyan';
            }
            if ($compra['status'] == 'ORDEN DE COMPRA') {
                $this->content['compras'][$key]['icon'] = 'done';
                $this->content['compras'][$key]['color'] = 'lime-10';
            }
            if ($compra['status'] == 'COMPRA') {
                $this->content['compras'][$key]['icon'] = 'fas fa-check-circle';
                $this->content['compras'][$key]['color'] = 'teal-10';
            }
        }
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFolioConsecutivo () {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM cmp_compras where folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = date('Y') . '00001';
        }
        
        $this->content['folio'] = $nuevo_folio;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getFolio () {
        $anio = date('Y');
        $sql = "SELECT max(folio) as folio FROM cmp_compras where folio like '$anio%'";
        $data = $this->db->query($sql)->fetchAll();

        if (sizeof($data) > 0) {
            $folio = $data[0]['folio'];
        } else {
            $folio = null;
        }

        if (is_numeric($folio)) {
            $nuevo_folio = intval($folio) + 1;
        } else {
            $nuevo_folio = date('Y') . '00001';
        }
        return $nuevo_folio;
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $compras = new Compras();
            $compras->proveedor_id = intval($request['proveedor_id']);
            $compras->fecha = date("Y/m/d", strtotime($request['fecha'])) . ' ' . date("H:i:s");
            $compras->status = 'NUEVO';
            $compras->folio = $this->getFolio();
            $compras->project_id = intval($request['project_id']) > 0 ? intval($request['project_id']) : null;
            $compras->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
            $compras->condiciones_pago = trim(strtoupper($request['condiciones_pago'])) ?? null;
            $compras->condiciones_entrega = trim(strtoupper($request['condiciones_entrega'])) ?? null;
            $compras->transporte = trim(strtoupper($request['transporte'])) ?? null;
            $compras->entrega = trim(strtoupper($request['entrega'])) ?? null;
            $compras->direccion_id = intval($request['direccion_id']) > 0 ? intval($request['direccion_id']) : null;
            $compras->sucursal_id = intval($request['sucursal_id']) > 0 ? intval($request['sucursal_id']) : null;
            $compras->direccion_entrega = trim(strtoupper($request['direccion_entrega'])) ?? null;
            $compras->observaciones = trim(strtoupper($request['observaciones'])) ?? null;
            if ($compras->create()) {
                $this->content['id'] = $compras->id;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la compra.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($compras);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la compra.'];
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
            $compras = Compras::findFirst($request['id']);
            $compras->setTransaction($tx);
            $compras->proveedor_id = intval($request['proveedor_id']);
            // $compras->fecha = date("Y-m-d H:i:s");
            $compras->status = $request['status'];
            $compras->project_id = intval($request['project_id']) > 0 ? intval($request['project_id']) : null;
            $compras->empresa_id = intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
            $compras->condiciones_pago = trim(strtoupper($request['condiciones_pago'])) ?? null;
            $compras->condiciones_entrega = trim(strtoupper($request['condiciones_entrega'])) ?? null;
            $compras->transporte = trim(strtoupper($request['transporte'])) ?? null;
            $compras->entrega = trim(strtoupper($request['entrega'])) ?? null;
            $compras->direccion_id = intval($request['direccion_id']) > 0 ? intval($request['direccion_id']) : null;
            $compras->sucursal_id = intval($request['sucursal_id']) > 0 ? intval($request['sucursal_id']) : null;
            $compras->direccion_entrega = trim(strtoupper($request['direccion_entrega'])) ?? null;
            $compras->observaciones = trim(strtoupper($request['observaciones'])) ?? null;
            /* $email = null;
            if (intval($request['proveedor_id']) > 0) {
                $proveedor = Proveedores::findFirst(intval($request['proveedor_id']));
                $email = $proveedor->email;
            }
            $usuarios = SysUsers::findFirst($compras->created_by); */
            if ($compras->update()) {
                /* if ($request['status'] === 'COMPRA' && $email !== null) {
                    $this->exportPDF($compras->id);
                    $this->sendEmailCompra($proveedor->email, $usuarios->nickname, $compras->folio, $proveedor->razon_social);
                } */
                $this->content['status'] = $compras->status;
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la compra.'];
                $tx->commit();
            } else {
                $this->content['error'] = Helpers::getErrors($compras);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la compra'];
                $tx->rollback();
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function sendEmailCompra($userDestino, $autor, $solicitud, $proveedor) {
        $mailer = new Mailer();
        $mailer->from = 'donotreply@ant.com.mx';
        $mailer->tousers = $userDestino;
        $mailer->subject = 'Orden de compra';
        $mailer->template = 'compra.xml';
        $mailer->type = 'shopping';
        $mailer->misc = [
            'solicitud' => $solicitud,
            'autor' => $autor,
            'logo' => 'http://api.impact.beta.antfarm.mx/public/assets/images/logo.png',
            'proveedor' => $proveedor
        ];
        $carpeta = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/compras_pdf/compra.pdf';
        $mailer->archivo = $carpeta;

        $result_message = null;
        try {
            $result_message = $mailer->sendEmail();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function exportPDF($id_compra) {
        $pdf = new PDFShopping();
        $pdf->AddFont('Nunito-Regular','','Nunito-Regular.php');
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetLineWidth(0.2);
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';

        $compra = Compras::findFirst($id_compra);
        $compra_id = $compra->id;
        
        $pdf->encabezado();
        
        $pdf->SetFont('Nunito-Regular','',9);
        $pdf->SetTextColor(0);

        $pdf->SetXY(10,40);
        $pdf->Cell(0,0,'Folio: '.$compra->folio);
        
        $pdf->SetXY(90,40);
        $pdf->Cell(0,0,'Fecha: '.$compra->fecha);

        $pdf->SetXY(173,40);
        $pdf->Cell(0,0,'Estatus: '.$compra->status);

        $pdf->SetXY(10,43);
        $pdf->MultiCell(70,5,'Proveedor: '.utf8_decode(Proveedores::findFirst($compra->proveedor_id)->razon_social),0,'L');

        $pdf->SetXY(90,43);
        $pdf->MultiCell(60,5,'Presupuesto: '.utf8_decode(Proyecto::findFirst($compra->project_id)->nombre),0,'L');

        $sql = "SELECT cmp_compras_detalles.id, cmp_compras_detalles.created, cmp_compras_detalles.created_by, cmp_compras_detalles.modified, cmp_compras_detalles.modified_by, cmp_compras_detalles.compra_id, cmp_compras_detalles.presentacion_id, cmp_compras_detalles.cantidad, cmp_compras_detalles.costo, cmp_compras_detalles.producto_id, cmp_compras_detalles.descripcion, inv_productos.nombre as producto, (cmp_compras_detalles.cantidad * cmp_compras_detalles.costo) as importe, inv_tipos_presentaciones.nombre as presentacion
            FROM cmp_compras_detalles
            inner join inv_productos on inv_productos.id = cmp_compras_detalles.producto_id and cmp_compras_detalles.compra_id = $compra_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id";
        $data = $this->db->query($sql)->fetchAll();

        $pdf->SetXY(10,82);
        $pdf->SetFont('','',8);

        $pdf->SetWidths(array(10,97,22,22,22,22));
        $i=1;
        $w = array(10,70,35,20,30,30);
        foreach($data as $row)
        {
            if($pdf->getY()>=$pdf->GetPageHeight()-40){
                $pdf->AddPage('P', 'Letter');
                $pdf->encabezado();
                $pdf->SetXY(10,82);
                $pdf->SetFont('','',8);
            }
            $pdf->RowMovimientos(array($i,utf8_decode($row['producto']),utf8_decode($row['presentacion']),$row['cantidad'],'$'.number_format(floatval($row['costo']),2,'.',','),'$'.number_format(floatval($row['importe']),2,'.',',')));
            $i++;
        }

        $carpeta = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/compras_pdf/';
        if (!is_dir($carpeta))  {
            mkdir($carpeta, 0777, true);
        }
        $pdf->Output('F',$carpeta.'compra.pdf',true);
    }

    public function exportPDF_compra($id_compra) {
        $pdf = new PDFShopping();
        $pdf->AddFont('Nunito-Regular','','Nunito-Regular.php');
        $pdf->AliasNbPages();
        $pdf->AddPage('L', 'Letter');
        $pdf->SetLineWidth(0.2);
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';

        $compra = Compras::findFirst($id_compra);
        $compra_id = $compra->id;
        $empresa_id = $compra->empresa_id;
        $empresa = Empresa::findFirst($empresa_id);

        $pdf->encabezado($id_compra, $empresa_id);

        $sql = "SELECT cmp_compras_detalles.id, cmp_compras_detalles.created, cmp_compras_detalles.created_by, cmp_compras_detalles.modified, cmp_compras_detalles.modified_by, cmp_compras_detalles.compra_id, cmp_compras_detalles.presentacion_id, cmp_compras_detalles.cantidad, cmp_compras_detalles.costo, cmp_compras_detalles.producto_id, cmp_compras_detalles.descripcion, inv_productos.nombre as producto, (cmp_compras_detalles.cantidad * cmp_compras_detalles.costo) as importe, inv_tipos_presentaciones.nombre as presentacion
            FROM cmp_compras_detalles
            inner join inv_productos on inv_productos.id = cmp_compras_detalles.producto_id and cmp_compras_detalles.compra_id = $id_compra
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id";
        $data = $this->db->query($sql)->fetchAll();

        $pdf->SetXY(5,87);
        $pdf->SetFont('Nunito-Regular','',9);

        $pdf->SetWidths(array(10,158,27,17,28,28));
        $i=1;
        $w = array(10,70,35,20,30,30);
        $subtotal = 0;
        $iva = 0;
        $total = 0;
        $k = 0;
        foreach($data as $row)
        {
            $subtotal = $subtotal + $row['importe'];
            if($pdf->getY()>=$pdf->GetPageHeight()-40){
                $pdf->AddPage('L', 'Letter');
                $pdf->encabezado($id_compra, $empresa_id);
                $pdf->SetXY(5,87);
                $pdf->SetFont('','',8);
            }
            $pdf->RowMovimientos(array($i,utf8_decode($row['producto'] . ' : ' . $row['descripcion']),utf8_decode($row['presentacion']),$row['cantidad'],'$'.number_format(floatval($row['costo']),2,'.',','),'$'.number_format(floatval($row['importe']),2,'.',',')));
            $pdf->SetXY(5,$pdf->getY());
            $i++;
        }
        if($pdf->getY() >= 140) {
            $pdf->AddPage('L', 'Letter');
            $pdf->encabezado($id_compra, $empresa_id);
            $pdf->SetXY(5,87);
            $pdf->SetFont('','',8);
        }
        $iva = $subtotal * .16;
        $total = $subtotal + $iva;
        $pdf->SetWidths(array(28,28));
        $i=1;
        $w = array(30,30);
        $pdf->SetAligns(array('L', 'R'));
        $y_inicial = $pdf->getY();
        $pdf->SetXY(217,$pdf->getY());
        $pdf->Row(array('RESULTADO: ', number_format($subtotal,2,'.',',')));
        $pdf->SetXY(217,$pdf->getY());
        $pdf->Row(array('DESCUENTO: ', number_format(0,2,'.',',')));
        $pdf->SetXY(217,$pdf->getY());
        $pdf->Row(array('SUBTOTAL: ', number_format($subtotal,2,'.',',')));
        $pdf->SetXY(217,$pdf->getY());
        $pdf->Row(array('IVA 16%: ', number_format($iva,2,'.',',')));
        $pdf->SetXY(217,$pdf->getY());
        $pdf->Row(array('TOTAL: ', number_format($total,2,'.',',')));
        $y_final = $pdf->getY();

        $pdf->SetXY(5,$y_inicial);
        $pdf->MultiCell(212,5, 'OBSERVACIONES: '.utf8_decode($compra->observaciones), 0, 'J');

        //sonia es el user 107
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(5,$y_final+15);
        $pdf->MultiCell(265,5, '______________________________________________________', 0, 'C');
        $pdf->SetXY(5,$pdf->getY());
        $pdf->MultiCell(270,5, 'SONIA ABIGAIL CAMPOS CAMPA', 0, 'C');
        $pdf->MultiCell(260,5, utf8_decode('RESPONSABLE DEL ÁREA DE COMPRAS'), 0, 'C');


        $pdf->SetTitle(utf8_decode('Compra'));
        $pdf->Output('I', 'reporte_compra.pdf', true);
        $response = new Phalcon\Http\Response();
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'inline; filename=Reporte_compra.pdf');
        
        return $response;
    }

    public function delete () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $compras = Compras::findFirst($request['id']);
                    $compras->setTransaction($tx);

                    if ($compras->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($compras);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la compra.'];
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