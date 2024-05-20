<?php
use Phalcon\Mvc\Controller;

class PDFCotizacion extends FPDF
{

    function encabezado($id_cotizacion, $empresa_id) {
        $this->SetFont('Nunito-Regular','',12);

        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/LogosERP/';
        $logo=$image_path.$empresa_id.'.png';
        if(file_exists($logo)){
            $this->Image($logo,5,5,0,30);
        }
        $this->SetXY(5,10);
        $this->SetFont('Nunito-Regular','',14);

        $cotizacion = Cotizaciones::findFirst($id_cotizacion);
        $cotizacion_id = $cotizacion->id;
        $empresa_id = $cotizacion->empresa_id;
        $empresa = Empresa::findFirst($empresa_id);

        $this->SetTextColor(0);

        $this->SetXY(150,10);
        $this->SetFont('Arial','B',9);
        $this->MultiCell(65,5, utf8_decode($empresa->razon_social), 0, 'L');
        $this->SetFont('Nunito-Regular','',9);

        $this->SetAutoPageBreak(false, 0.10);

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
                $this->SetXY(150,$this->getY()+2);
                $this->MultiCell(50,5, utf8_decode('R.F.C: ' . $empresa->rfc), 0, 'L');
                $this->SetFont('Nunito-Regular','',8);
                $this->SetXY(10,260);
                $this->MultiCell(100,5, utf8_decode($direccion->calle . ' No.' . $direccion->num_ext), 0, 'L');
                $this->SetXY(10,265);
                $this->MultiCell(100,5, utf8_decode($direccion->colonia . '., ' . $localiad), 0, 'L');
                $this->SetXY(10,270);
                $this->MultiCell(100,5, utf8_decode('C.P: ' . $direccion->cp), 0, 'L');
                $this->SetXY(130,265);
                $this->MultiCell(90,5, utf8_decode('Teléfono(s): ' . $empresa->telefono), 0, 'L');
                $this->SetXY(130,270);
                $this->MultiCell(90,5, utf8_decode('Correo: ' . $empresa->correo), 0, 'L');
            }
        }

        /* $this->SetXY(120,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C');

        $this->SetXY(171,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C');

        $this->SetXY(222,10);
        $this->Cell(51, 50, '' ,1 , 0, 'C'); */

        $this->SetFont('Arial','B',9);
        $this->SetXY(121,10);

        /* $this->SetFont('Arial','B',9);
        $this->SetXY(172,10);
        $this->MultiCell(49,5,utf8_decode('CONDICIONES DE PAGO:'),0,'L');
        $this->SetFont('Nunito-Regular','',9);
        $this->SetXY(172,$this->getY());
        $this->MultiCell(49,5,utf8_decode($cotizacion->condiciones_pago),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Arial','B',9);
        $this->MultiCell(49,5,utf8_decode('CONDICIONES DE ENTREGA:'),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Nunito-Regular','',9);
        $this->MultiCell(49,5,utf8_decode($cotizacion->condiciones_entrega),0,'L');
        $this->SetFont('Arial','B',9);
        $this->SetXY(172,$this->getY());
        $this->MultiCell(49,5,utf8_decode('TRANSPORTE:'),0,'L');
        $this->SetXY(172,$this->getY());
        $this->SetFont('Nunito-Regular','',9);
        $this->MultiCell(49,5,utf8_decode($cotizacion->transporte),0,'L');

        $this->SetFont('Arial','B',9);
        $this->SetXY(223,10);
        $this->MultiCell(49,5,utf8_decode('EFECTUAR ENTREGA EN:'),0,'L'); */
        //
        /* $empresa = Empresa::findFirst($cotizacion->empresa_id);

        
                    $direccion_entrega = Direccion::findFirst($cotizacion->empresa_id);
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
                    } */
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
        $this->MultiCell(49,5,utf8_decode($cotizacion->transporte),0,'L'); */

        /* $this->SetXY(6,65);
        $this->Cell(0,0,'Folio: '.$cotizacion->folio); */
        
        /* $this->SetXY(50,65);
        $this->Cell(0,0,'Fecha: '.$cotizacion->fecha); */

        /* $this->SetXY(121,65);
        $this->Cell(0,0,'Estatus: '.$cotizacion->status); */
        

        /* $this->SetXY(172,62);
        if ($cotizacion->project_id != null) {
            $this->MultiCell(110,5,'Presupuesto: '.utf8_decode(Proyecto::findFirst($cotizacion->project_id)->nombre),0,'L');
        } else {
            $this->MultiCell(60,5,'Presupuesto: ',0,'L');
        } */
    }

    function tablePdf ($x, $y) {
        $this->SetXY($x, $y);
        $header = array('Lote/progresivo','Cantidad','Unidad de medida',utf8_decode('Artículo/Servicio'),utf8_decode('Descripción'),'Costo unitario','Importe');
        // $this->SetXY(5,80);
        $this->SetFillColor(8, 85, 134);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Nunito-Regular','',9);
        // Header
        // $x = 168;
        $i = 0;
        // $w=array(5,20,25,30,25,30,35,20,15);
        $w=array(25,19,30,40,40,21,25);
        foreach($header as $col) {
            if($i<=6){
                $this->Cell($w[$i],7,$col,1,0,'C',true);
            }
            $x=$x+5;
            $i++;
        }
        $this->SetTextColor(0);
    }

    // Page footer
    //
    function Footer()
    {
        // Position at 1.5 cm from bottom
        // $this->SetY(-15);
        // Arial italic 8
        // $this->SetFont('Nunito-Regular','',8);
        // Page number
        // $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
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
class CotizacionesController extends Controller {

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * from crm_cotizaciones";
        $this->content['cotizaciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }


    public function getByOportunidad ($oportunidad_id)
    {
        $sql = "SELECT * from crm_cotizaciones where oportunidad_id = $oportunidad_id";
        $this->content['cotizaciones'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
  
    public function save () {
        try{
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $cotizaciones= new Cotizaciones();
            $cotizaciones->setTransaction($tx);
            $cotizaciones->fecha=date('Y-m-d',strtotime($request['fecha']));
            $cotizaciones->titulo=trim($request['titulo']) == '' ? null : trim($request['titulo']);
            $cotizaciones->oportunidad_id=intval($request['oportunidad_id']);
            $cotizaciones->empresa_id=intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
            $cotizaciones->titulo=trim($request['titulo']) == '' ? null : trim($request['titulo']);
            $cotizaciones->cuerpo=trim($request['cuerpo']) == '' ? null : trim($request['cuerpo']);
            $cotizaciones->encabezado=trim($request['encabezado']) == '' ? null : trim($request['encabezado']);
            $cotizaciones->lugar=trim($request['lugar']) == '' ? null : trim($request['lugar']);
            $cotizaciones->total_letra=trim($request['total_letra']) == '' ? null : trim($request['total_letra']);
            $cotizaciones->tiempo_entrega=trim($request['tiempo_entrega']) == '' ? null : trim($request['tiempo_entrega']);
            $cotizaciones->condiciones_pago=trim($request['condiciones_pago']) == '' ? null : trim($request['condiciones_pago']);
            $cotizaciones->cuerpo_final=trim($request['cuerpo_final']) == '' ? null : trim($request['cuerpo_final']);
            $cotizaciones->dirigido=trim($request['dirigido']) == '' ? null : trim($request['dirigido']);
            if ($cotizaciones->create()) {
            	$this->content['result']='success';
                $this->content['id']=$cotizaciones->id;
                $this->content['message']= ['title' => '¡Exito!', 'content' => 'Se ha guardado la cotización'];
                $tx->commit();
            } else {
            	$this->content['error'] = Helpers::getErrors($cotizaciones);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar la cotización'];
                $tx->rollback();
            }
        } catch  (Throwable $e) {
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

            $cotizaciones = Cotizaciones::findFirst($request['id']);
            if ($cotizaciones) {
                $cotizaciones->setTransaction($tx);
                $cotizaciones->fecha=date('Y-m-d',strtotime($request['fecha']));
                $cotizaciones->titulo=trim($request['titulo']) == '' ? null : trim($request['titulo']);
                $cotizaciones->oportunidad_id=intval($request['oportunidad_id']);
                $cotizaciones->empresa_id=intval($request['empresa_id']) > 0 ? intval($request['empresa_id']) : null;
                $cotizaciones->titulo=trim($request['titulo']) == '' ? null : trim($request['titulo']);
                $cotizaciones->cuerpo=trim($request['cuerpo']) == '' ? null : trim($request['cuerpo']);
                $cotizaciones->encabezado=trim($request['encabezado']) == '' ? null : trim($request['encabezado']);
                $cotizaciones->lugar=trim($request['lugar']) == '' ? null : trim($request['lugar']);
                $cotizaciones->total_letra=trim($request['total_letra']) == '' ? null : trim($request['total_letra']);
                $cotizaciones->tiempo_entrega=trim($request['tiempo_entrega']) == '' ? null : trim($request['tiempo_entrega']);
                $cotizaciones->condiciones_pago=trim($request['condiciones_pago']) == '' ? null : trim($request['condiciones_pago']);
                $cotizaciones->cuerpo_final=trim($request['cuerpo_final']) == '' ? null : trim($request['cuerpo_final']);
                $cotizaciones->dirigido=trim($request['dirigido']) == '' ? null : trim($request['dirigido']);
                if ($cotizaciones->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la cotización.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($cotizaciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la cotización.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                $cotizaciones = Cotizaciones::findFirst($request['id']);
                $cotizaciones->setTransaction($tx);
                if ($cotizaciones->delete()) {
                    $this->content['result'] = 'success';
                } else {
                    $this->content['error'] = Helpers::getErrors($cotizaciones);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la cotización.'];
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function exportPDF_cotizacion($id_cotizacion) {
        $pdf = new PDFCotizacion();
        $pdf->AddFont('Nunito-Regular','','Nunito-Regular.php');
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetLineWidth(0.2);
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';

        $cotizacion = Cotizaciones::findFirst($id_cotizacion);
        $cotizacion_id = $cotizacion->id;
        $empresa_id = $cotizacion->empresa_id;
        $empresa = Empresa::findFirst($empresa_id);

        $pdf->encabezado($id_cotizacion, $empresa_id);

        $pdf->SetXY(10,40);
        $pdf->SetFont('Nunito-Regular','',9);
        if ($cotizacion->encabezado !== null) {
            $pdf->MultiCell(190,5, utf8_decode($cotizacion->encabezado), 0, 'C');
        }
        if ($cotizacion->titulo !== null) {
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(10,$pdf->getY()+5);
            $pdf->MultiCell(200,5, utf8_decode($cotizacion->titulo), 0, 'C');
        }

        $sql = "SELECT case date_part('month',fecha) 
            WHEN 1 THEN 'ENERO'
            WHEN 2 THEN  'FEBRERO'
            WHEN 3 THEN 'MARZO' 
            WHEN 4 THEN 'ABRIL' 
            WHEN 5 THEN 'MAYO'
            WHEN 6 THEN 'JUNIO'
            WHEN 7 THEN 'JULIO'
            WHEN 8 THEN 'AGOSTO'
            WHEN 9 THEN 'SEPTIEMBRE'
            WHEN 10 THEN 'OCTUBRE'
            WHEN 11 THEN 'NOVIEMBRE'
            WHEN 12 THEN 'DICIEMBRE'
            END mes, date_part('day', fecha) as dia, date_part('year', fecha) as year
            from crm_cotizaciones where id = $cotizacion_id";

        $date = $this->db->query($sql)->fetchAll();

        if ($cotizacion->lugar !== null) {
            $pdf->SetFont('Nunito-Regular','',9);
            $pdf->SetXY(10,$pdf->getY()+5);
            $pdf->MultiCell(195,5, utf8_decode($cotizacion->lugar) . ' a ' . $date[0]['dia'] . ' de ' . $date[0]['mes'] . ' del ' . $date[0]['year'], 0, 'R');
        } else {
            $pdf->SetFont('Nunito-Regular','',9);
            $pdf->SetXY(10,$pdf->getY()+5);
            $pdf->MultiCell(195,5, $date[0]['dia'] . ' de ' . $date[0]['mes'] . ' del ' . $date[0]['year'], 0, 'R');
        }

        if ($cotizacion->lugar !== null) {
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(10,$pdf->getY()+5);
            $pdf->MultiCell(195,5, utf8_decode($cotizacion->dirigido), 0, 'L');
        }

        if ($cotizacion->cuerpo !== null) {
            $pdf->SetFont('Nunito-Regular','',10);
            $pdf->SetXY(10,$pdf->getY()+5);
            $pdf->MultiCell(195,5, utf8_decode($cotizacion->cuerpo), 0, 'J');
        }

        $pdf->tablePdf(10, $pdf->getY()+5);
        


        $sql = "SELECT crm_cotizaciones_detalles.*, (cantidad * precio) as total from crm_cotizaciones_detalles where cotizacion_id = $cotizacion_id";
        $data = $this->db->query($sql)->fetchAll();

        foreach ($data as $key => $detalle) {
            $data[$key]['cantidad_formato'] = number_format(floatval($detalle['cantidad']),2,'.',',');
            $data[$key]['precio_formato'] = number_format(floatval($detalle['precio']),2,'.',',');
            $data[$key]['subtotal'] = $detalle['total'];
            $data[$key]['total'] = number_format(floatval($detalle['total']),2,'.',',');
        }

        $pdf->SetXY(10,$pdf->getY()+7);
        $pdf->SetFont('Nunito-Regular','',9);

        $pdf->SetWidths(array(25,19,30,40,40,21,25));
        $i=1;
        $w = array(25,19,30,40,40,21,25);
        $subtotal = 0;
        $iva = 0;
        $total = 0;
        $k = 0;
        foreach($data as $row)
        {
            $pdf->SetFont('Nunito-Regular','',8);
            $subtotal = $subtotal + $row['subtotal'];
            if($pdf->getY()>=200){
                $pdf->AddPage('P', 'Letter');
                $pdf->encabezado($id_cotizacion, $empresa_id);
                $pdf->tablePdf(10,40);
            }
            $pdf->SetAligns(array('L', 'R', 'L', 'L', 'L', 'R', 'R'));
            $pdf->Row(array($row['lote_progresivo'],$row['cantidad_formato'],utf8_decode($row['unidad']),utf8_decode($row['articulo']),utf8_decode($row['descripcion']),'$'.$row['precio_formato'],'$'.$row['total']));
            $pdf->SetXY(10,$pdf->getY());
            $i++;
        }
        if($pdf->getY() >= 200) {
            $pdf->AddPage('P', 'Letter');
            $pdf->encabezado($id_cotizacion, $empresa_id);
            $pdf->SetFont('Nunito-Regular','',9);
            $pdf->tablePdf(10,40);
        }
        // $subtotal = 80;
        $iva = $subtotal * .16;
        $total = $subtotal + $iva;
        $pdf->SetWidths(array(21,25));
        $i=1;
        $w = array(30,30);
        $pdf->SetAligns(array('L', 'R'));
        $y_inicial = $pdf->getY();
        if (count($data) > 0) {
            $pdf->SetXY(164,$pdf->getY());
        } else {
            $pdf->SetXY(164,$pdf->getY()+7);
        }
        $pdf->Row(array('RESULTADO: ', number_format($subtotal,2,'.',',')));
        $pdf->SetXY(164,$pdf->getY());
        $pdf->Row(array('SUBTOTAL: ', number_format($subtotal,2,'.',',')));
        $pdf->SetXY(164,$pdf->getY());
        $pdf->Row(array('IVA 16%: ', number_format($iva,2,'.',',')));
        $pdf->SetXY(164,$pdf->getY());
        $pdf->Row(array('TOTAL: ', number_format($total,2,'.',',')));
        $y_final = $pdf->getY();

        if ($cotizacion->total_letra !== null) {
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(10,$pdf->getY()+2);
            $pdf->MultiCell(195,5, 'CANTIDAD CON LETRA: ' . utf8_decode($cotizacion->total_letra), 0, 'L');
        }

        if ($cotizacion->tiempo_entrega !== null) {
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(10,$pdf->getY()+2);
            $pdf->MultiCell(195,5, 'TIEMPO DE ENTREGA: ' . utf8_decode($cotizacion->tiempo_entrega), 0, 'L');
        }

        if ($cotizacion->condiciones_pago !== null) {
            $pdf->SetFont('Arial','B',9);
            $pdf->SetXY(10,$pdf->getY()+2);
            $pdf->MultiCell(195,5, 'CONDICIONES DE PAGO: ' . utf8_decode($cotizacion->condiciones_pago), 0, 'L');
        }

        if ($cotizacion->cuerpo_final !== null) {
            $pdf->SetFont('Nunito-Regular','',10);
            $pdf->SetXY(10,$pdf->getY()+7);
            $pdf->MultiCell(195,5, utf8_decode($cotizacion->cuerpo_final), 0, 'J');
        }

        //sonia es el user 107
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(10,$pdf->getY()+10);
        $pdf->MultiCell(200,5, 'ATENTAMENTE', 0, 'C');
        $pdf->SetXY(10,$pdf->getY()+10);
        $pdf->MultiCell(200,5, '______________________________________________________', 0, 'C');
        $pdf->SetXY(10,$pdf->getY());
        $pdf->MultiCell(200,5, $empresa->rep_legal, 0, 'C');
        $pdf->SetFont('Nunito-Regular', '', 10);
        $pdf->MultiCell(200,5, utf8_decode('Representante legal'), 0, 'C');


        $pdf->SetTitle(utf8_decode('Compra'));
        $pdf->Output('I', 'reporte_compra.pdf', true);
        $response = new Phalcon\Http\Response();
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'inline; filename=Reporte_compra.pdf');
        
        return $response;
    }
}