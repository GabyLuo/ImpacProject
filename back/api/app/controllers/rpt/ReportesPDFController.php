<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 17/09/17
 * Time: 11:42 AM
 */
// require('../../../vendor/setasign/fpdf/fpdf.php');
use Phalcon\Mvc\Controller;

class PDF extends FPDF
{

    function encabezado($tipo) {
        $this->SetFont('Nunito-Regular','',12);

        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';
        $logo=$image_path.'logo.png';
        if(file_exists($logo)){
            $this->Image($logo,10,10,60,15);
        }
        $this->SetXY(($this->GetPageWidth()/2)+44,14);
        $this->SetFont('Nunito-Regular','',14);
        $this->SetTextColor(8,85,134);
        $this->Cell(0,0,'Reporte de movimiento');

        $this->SetXY(($this->GetPageWidth()/2)+64,15);
        $this->SetFont('Nunito-Regular','',10);
        $this->SetTextColor(0);
        $this->Cell(0,10,'Fecha: '.date('d').'-'.date('m').'-'.date('Y'));
            
        $header = array('#','Producto',utf8_decode('Presentación'),'Cantidad','Costo','Importe');
        if (intval($tipo) == 5) {
            $this->SetXY(10,75);
        } else {
            $this->SetXY(10,60);
        }
        $this->SetFillColor(8, 85, 134);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Nunito-Regular','',8);
        // Header
        $x = 168;
        $i = 0;
        // $w=array(5,20,25,30,25,30,35,20,15);
        $w=array(10,97,22,22,22,22);
        foreach($header as $col) {
            if($i<=6){
                $this->Cell($w[$i],7,$col,1,0,'C',true);
            }
            $x=$x+5;
            $i++;
        }
    }

    function encabezadoOrden() {
        $this->SetFont('Nunito-Regular','',12);

        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';
        $logo=$image_path.'logo.png';
        if(file_exists($logo)){
            $this->Image($logo,10,10,60,15);
        }
        $this->SetXY(($this->GetPageWidth()/2)+30,14);
        $this->SetFont('Nunito-Regular','',12);
        $this->SetTextColor(8,85,134);
        $this->Cell(0,0,utf8_decode('Reporte de orden de producción'));

        $this->SetXY(($this->GetPageWidth()/2)+30,15);
        $this->SetFont('Nunito-Regular','',10);
        $this->SetTextColor(0);
        $this->Cell(0,10,'Fecha: '.date('d').'-'.date('m').'-'.date('Y'));

        $header = array('#','Producto','Cantidad');
        $this->SetXY(10,60);

        $this->SetFillColor(8, 85, 134);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('Nunito-Regular','',8);
        // Header
        $x = 168;
        $i = 0;
        // $w=array(5,20,25,30,25,30,35,20,15);
        $w=array(10,155,30);
        foreach($header as $col) {
            if($i<=6){
                $this->Cell($w[$i],7,$col,1,0,'C',true);
            }
            $x=$x+5;
            $i++;
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
    //Rotar texto
    //
    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

    var $angle=0;

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        parent::_endpage();
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

    function RowOrdenes($data)
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
            } else if ($i==1) {
                $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            } else if ($i==2){
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

class ReportesPDFController extends Controller {

    public $content = ['result' => 'error', 'message' => ''];

    public function exportPDF_movimiento($movimiento) {
        $pdf = new PDF();
        $pdf->AddFont('Nunito-Regular','','Nunito-Regular.php');
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetLineWidth(0.2);
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';

        $movimiento = Movimientos::findFirst($movimiento);
        $movimiento_id = $movimiento->id;
        $tipo_movimiento = $movimiento->tipo_id;

        $pdf->encabezado($movimiento->tipo_id);
        
        $pdf->SetFont('Nunito-Regular','',9);
        $pdf->SetTextColor(0);

        $pdf->SetXY(10,40);
        $pdf->Cell(0,0,'Folio: '.$movimiento->folio);
        
        $pdf->SetXY(90,40);
        $pdf->Cell(0,0,'Movimiento: '.TiposMovimientos::findFirst($movimiento->tipo_id)->nombre);

        $pdf->SetXY(173,40);
        $pdf->Cell(0,0,'Estatus: '.$movimiento->status);

        $pdf->SetXY(10,43);
        $pdf->MultiCell(70,5,'Empresa: '.utf8_decode(Empresa::findFirst($movimiento->empresa_id)->razon_social),0,'L');
        // $pdf->Cell(0,0,'Empresa: '.utf8_decode(Empresa::findFirst($movimiento->empresa_id)->razon_social));

        $pdf->SetXY(90,43);
        $pdf->MultiCell(60,5,utf8_decode('Almacén: '.Almacenes::findFirst($movimiento->almacen_id)->nombre),0,'L');
        // $pdf->Cell(0,0,utf8_decode('Almacén: '.Almacenes::findFirst($movimiento->almacen_id)->nombre));

        $pdf->SetXY(162,45);
        $pdf->Cell(0,0,utf8_decode('Fecha ejecución: '.date('d-m-Y',strtotime($movimiento->fecha_ejecutado))));

        if ($tipo_movimiento == 5) {
            $movimiento_entrada = Movimientos::findFirst(
                [
                'movimiento_id = :movimiento_id:',
                    'bind' => [
                        'movimiento_id' => $movimiento_id
                    ]
                ]
            );

            // var_dump($movimiento_entrada);

            $pdf->SetFont('Nunito-Regular','',9);
            $pdf->SetTextColor(0);

            $pdf->SetXY(10,55);
            $pdf->Cell(0,0,'Folio: '.$movimiento_entrada->folio);
            
            $pdf->SetXY(90,55);
            $pdf->Cell(0,0,'Movimiento: '.TiposMovimientos::findFirst($movimiento_entrada->tipo_id)->nombre);

            $pdf->SetXY(173,55);
            $pdf->Cell(0,0,'Estatus: '.$movimiento_entrada->status);

            $pdf->SetXY(10,58);
            $pdf->MultiCell(70,5,'Empresa: '.utf8_decode(Empresa::findFirst($movimiento->empresa_id)->razon_social),0,'L');
            // $pdf->Cell(0,0,'Empresa: '.utf8_decode(Empresa::findFirst($movimiento_entrada->empresa_id)->razon_social));

            $pdf->SetXY(90,58);
            $pdf->MultiCell(60,5,utf8_decode('Almacén: '.Almacenes::findFirst($movimiento_entrada->almacen_id)->nombre),0,'L');
            // $pdf->Cell(0,0,utf8_decode('Almacén: '.Almacenes::findFirst($movimiento_entrada->almacen_id)->nombre));

            $pdf->SetXY(162,60);
            $pdf->Cell(0,0,utf8_decode('Fecha ejecución: '.date('d-m-Y',strtotime($movimiento->fecha_ejecutado))));
        }
        
        /* if(intval($unidad_id) === 0){
            $unidad_id = 'null';
        } else {
            $unidad_id = intval($unidad_id);
        }
        if($fecha_inicio === 'null') {
            $pdf->SetXY(10,45);
            $pdf->Cell(0,0,'Fecha-Inicio: ');
        } else {
            $fecha_inicio = date('Y-m-d',strtotime($fecha_inicio));
            $pdf->SetXY(10,45);
            $pdf->Cell(0,0,'Fecha-Inicio: '.$fecha_inicio);
        }
        if($fecha_fin === 'null') {
            $pdf->SetXY(70,45);
            $pdf->Cell(0,0,'Fecha-Fin: ');
        } else {
            $fecha_fin = date('Y-m-d',strtotime($fecha_fin));
            $pdf->SetXY(70,45);
            $pdf->Cell(0,0,'Fecha-Fin: '.$fecha_fin);
        }
        if(intval($unidad_id) === 0 && intval($cliente_id) !== 0){
            $unidad_id = 'null';
        } */
        

        $sql = "SELECT inv_movimientos_detalles.id, inv_movimientos_detalles.created, inv_movimientos_detalles.created_by, inv_movimientos_detalles.modified, inv_movimientos_detalles.modified_by, inv_movimientos_detalles.movimiento_id, inv_movimientos_detalles.presentacion_id, inv_movimientos_detalles.cantidad, inv_movimientos_detalles.costo, inv_movimientos_detalles.producto_id, inv_productos.nombre as producto, (inv_movimientos_detalles.cantidad * inv_movimientos_detalles.costo) as importe, inv_tipos_presentaciones.nombre as presentacion
            FROM inv_movimientos_detalles
            inner join inv_productos on inv_productos.id = inv_movimientos_detalles.producto_id and inv_movimientos_detalles.movimiento_id = $movimiento_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_movimientos_detalles.presentacion_id";
        $data = $this->db->query($sql)->fetchAll();

        if ($tipo_movimiento != 5) {
            $pdf->SetXY(10,67);
        } else {
            $pdf->SetXY(10,82);
        }
        $pdf->SetFont('','',8);

        $pdf->SetWidths(array(10,97,22,22,22,22));
        $i=1;
        $w = array(10,70,35,20,30,30);
        foreach($data as $row)
        {
            if($pdf->getY()>=$pdf->GetPageHeight()-40){
                $pdf->AddPage('P', 'Letter');
                $pdf->encabezado($tipo_movimiento);
                if ($tipo_movimiento != 5) {
                    $pdf->SetXY(10,67);
                } else {
                    $pdf->SetXY(10,82);
                }
                $pdf->SetFont('','',8);
            }
            $pdf->RowMovimientos(array($i,utf8_decode($row['producto']),utf8_decode($row['presentacion']),$row['cantidad'],'$'.number_format(floatval($row['costo']),2,'.',','),'$'.number_format(floatval($row['importe']),2,'.',',')));
            $i++;
        }

        $pdf->SetTitle(utf8_decode('Reporte de movimiento'));
        $pdf->Output('I', 'reporte_movimiento.pdf', true);
        $response = new Phalcon\Http\Response();
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'inline; filename=Reporte_movimiento.pdf');
        
        return $response;
    }

    public function exportPDF_orden($orden_id) {
        $pdf = new PDF();
        $pdf->AddFont('Nunito-Regular','','Nunito-Regular.php');
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'Letter');
        $pdf->SetLineWidth(0.2);
        $image_path = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/images/';

        $orden = Ordenes::findFirst($orden_id);
        $orden_id = $orden->id;

        $pdf->encabezadoOrden();
        
        $pdf->SetFont('Nunito-Regular','',9);
        $pdf->SetTextColor(0);

        $pdf->SetXY(10,40);
        $pdf->Cell(0,0,'Folio: '.$orden->folio);
        
        $pdf->SetXY(90,40);
        $pdf->Cell(0,0,utf8_decode('Fecha de producción: ').$orden->fecha_produccion);

        $pdf->SetXY(153,40);
        $pdf->Cell(0,0,'Estatus: '.$orden->status);

        $pdf->SetXY(10,43);
        $pdf->MultiCell(70,5,'Empresa: '.utf8_decode(Empresa::findFirst($orden->empresa_id_destino)->razon_social),0,'L');
        // $pdf->Cell(0,0,'Empresa: '.utf8_decode(Empresa::findFirst($orden->empresa_id_destino)->razon_social));

        $pdf->SetXY(90,43);
        $pdf->MultiCell(58,5,utf8_decode('Almacén: '.Almacenes::findFirst($orden->almacen_id_origen)->nombre),0,'L');
        // $pdf->Cell(0,0,utf8_decode('Almacén: '.Almacenes::findFirst($orden->almacen_id_origen)->nombre));

        $pdf->SetXY(153,43);
        $pdf->MultiCell(58,5,utf8_decode('Almacén: '.Almacenes::findFirst($orden->almacen_id_destino)->nombre),0,'L');
        // $pdf->Cell(0,0,utf8_decode('Almacén: '.Almacenes::findFirst($orden->almacen_id_destino)->nombre));

        $pdf->SetXY(10,$pdf->getY()+3);
        $pdf->Cell(0,0,'Cliente: '.utf8_decode(VntClientes::findFirst($orden->cliente_id)->razon_social));

        $sql = "SELECT pro_ordenes_detalles.id, pro_ordenes_detalles.orden_id, pro_ordenes_detalles.producto_id,pro_ordenes_detalles.cantidad, inv_productos.nombre, inv_tipos_articulos.id as categoria_id, inv_lineas.id as linea_id, inv_tipos_presentaciones.id as presentacion_id
                FROM pro_ordenes_detalles
                inner join inv_productos on inv_productos.id = pro_ordenes_detalles.producto_id
                inner join inv_tipos_articulos on inv_tipos_articulos.id = inv_productos.tipo_id
                inner join inv_lineas on inv_lineas.id = inv_productos.linea_id
                inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = inv_productos.presentacion_id
                and pro_ordenes_detalles.orden_id = $orden_id";

        $data = $this->db->query($sql)->fetchAll();

        // $pdf->SetFont('','',8);

        $pdf->SetWidths(array(10,155,30));
        $i=1;
        $pdf->SetXY(10,67);
        $w = array(10,155,30);
        foreach($data as $row)
        {
            if($pdf->getY()>=$pdf->GetPageHeight()-40){
                $pdf->AddPage('L', 'Letter');
                $pdf->encabezado();
                $pdf->SetXY(10,67);
                // $pdf->SetFont('','',8);
            }
            $pdf->RowOrdenes(array($i,utf8_decode($row['nombre']),$row['cantidad']));
            $i++;
        }
        ////////////////////////////////////////////////////////////
        $pdf->SetXY(($pdf->GetPageWidth()/2)-15,$pdf->getY()+8);
        $pdf->SetFont('Nunito-Regular','',10);
        $pdf->SetTextColor(8,85,134);
        $pdf->Cell(0,0,utf8_decode('Billete de materiales'));
        ////////////////////////////////////////////////////////////
        $header = array('#','Producto','Total requerido');
        $pdf->SetXY(10, $pdf->getY()+10);

        $pdf->SetFillColor(8, 85, 134);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Nunito-Regular','',8);
        // Header
        $x = 168;
        $k = 0;
        // $w=array(5,20,25,30,25,30,35,20,15);
        $w=array(10,155,30);
        foreach($header as $col) {
            if($k<=6){
                $pdf->Cell($w[$k],7,$col,1,0,'C',true);
            }
            $x=$x+5;
            $k++;
        }
        /////////////////////////////////////////////////////////////
        $sql_compacto = "SELECT inv_bom.material_id, sum(inv_bom.cantidad), x.nombre as nombre, 
            inv_tipos_articulos.nombre as categoria, inv_lineas.nombre as linea, inv_lineas.id as linea_id,
            inv_tipos_articulos.id as categoria_id, inv_tipos_presentaciones.id as presentacion_id, 
            inv_tipos_presentaciones.nombre as unidad, sum(pro_ordenes_detalles.cantidad), sum(inv_bom.cantidad * pro_ordenes_detalles.cantidad) as total_necesario
            FROM inv_bom
            inner join inv_productos as x on x.id = inv_bom.material_id
            inner join inv_tipos_articulos on inv_tipos_articulos.id = x.tipo_id
            inner join inv_lineas on inv_lineas.id = x.linea_id
            inner join inv_tipos_presentaciones on inv_tipos_presentaciones.id = x.presentacion_id
            inner join inv_productos on inv_productos.id = inv_bom.producto_id
            inner join pro_ordenes_detalles on pro_ordenes_detalles.producto_id = inv_productos.id
            inner join pro_ordenes on pro_ordenes.id = pro_ordenes_detalles.orden_id
            and pro_ordenes.id = $orden_id
            group by inv_bom.material_id,x.nombre,inv_tipos_articulos.nombre,inv_lineas.nombre,
            inv_lineas.id,inv_tipos_articulos.id,inv_tipos_presentaciones.id";

        $data_compacto = $this->db->query($sql_compacto)->fetchAll();
        $array_compacto = [];
        $almacen_origen = $orden->almacen_id_origen;
        foreach ($data_compacto as $compacto) {
            $c=(object)array();
            $c->nombre = $compacto['nombre'].' ('.$compacto['unidad'].')';
            $c->total_necesario = round($compacto['total_necesario'],3);
            $producto_existencia = $compacto['material_id'];
            $sql_existencias = "SELECT * from get_existencias($almacen_origen, $producto_existencia, null, null)";
            $existencia = $this->db->query($sql_existencias)->fetchAll();
            if (count($existencia) > 0) {
                $c->existencia = $existencia[0]['existencia'];
            } else {
                $c->existencia = 0;
            }
            if ($compacto['total_necesario'] > $c->existencia) {
                $cantidades_suficientes = 'no';
                $producto = Productos::findFirst($producto_existencia)->nombre;
            }
            $c->expend = false;
            $c->color = 'orange-5';
            array_push($array_compacto, $c);
        }

        // $pdf->SetFont('','',8);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);

        $pdf->SetFont('Nunito-Regular','',8);
        $pdf->SetWidths(array(10,155,30));
        $j=1;
        $pdf->SetXY(10,$pdf->getY()+7);
        $w = array(10,155,30);
        foreach($array_compacto as $row)
        {
            // var_dump($row);
            if($pdf->getY()>=$pdf->GetPageHeight()-40){
                $pdf->AddPage('L', 'Letter');
                $pdf->encabezado();
                $pdf->SetXY(10,67);
                $pdf->SetFont('','',8);
            }
            $pdf->RowOrdenes(array($j,utf8_decode($row->nombre),$row->total_necesario));
            $j++;
        }

        $pdf->SetTitle(utf8_decode('Reporte de orden de produccion'));
        $pdf->Output('I', 'reporte_orden.pdf', true);
        $response = new Phalcon\Http\Response();
        $response->setHeader('Content-Type', 'application/pdf');
        $response->setHeader('Content-Disposition', 'inline; filename=Reporte_orden.pdf');
        
        return $response;
    }
}
?>