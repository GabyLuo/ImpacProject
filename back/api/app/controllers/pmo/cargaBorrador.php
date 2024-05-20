<?php

use Phalcon\Mvc\Controller;

define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE) . chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define ('UTF8_BOM'               , chr(0xEF) . chr(0xBB) . chr(0xBF));

class CargaBorrador extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT *
                from pmo_carga
                order by id";
        $this->content['cargas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function autoUTF($s)
    {
        // detect UTF-8
        if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s))
            return $s;

        // detect WINDOWS-1250
        if (preg_match('#[\x7F-\x9F\xBC]#', $s))
            return iconv('WINDOWS-1250', 'UTF-8', $s);

        // assume ISO-8859-2
        return iconv('ISO-8859-2', 'UTF-8', $s);
    }

    function detect_utf_encoding($filename) {

        $text = file_get_contents($filename);
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 3);
        
        if ($first3 == UTF8_BOM) return 'UTF-8';
        elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
        elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
        elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
        elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
    }

    private function detect_SJIS_win($filename){
        $file = null;
        if  (($file = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                foreach ($row as $r)  {
                    if(strtoupper(mb_detect_encoding($r, "ASCII, UTF-8, UNICODE, sjis-win, ISO-8859-1")) === strtoupper('SJIS-win')){
                        fclose($file);
                        return true;
                    }
                }
            }
            fclose($file);
        }
        return false;
    }

    private function detect_utf_8_bom($filename) {
        $str = file_get_contents($filename);
        $bom = pack("CCC", 0xef, 0xbb, 0xbf);
        if (0 === strncmp($str, $bom, 3)) {
            return true;
        }
        return false;
    }

    public function uploadCsv ()
    {
        $content = $this->content;
        $isCsv = true;
        
        if ($this->request->hasFiles())  {
            $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/';
            if (!is_dir($upload_dir))  {
                mkdir($upload_dir, 0777);
            }
            $fullPath = '';
            foreach ($this->request->getUploadedFiles() as $file) {
                
                if  ($file->getExtension() !== 'csv')  {
                    $isCsv = false;
                    break;
                }
                $fullPath = $upload_dir . 'archivocarga.' . $file->getExtension();
                if  (file_exists($fullPath))  {
                    @unlink($fullPath);
                }
                $file->moveTo($fullPath);
            }

            $file = null;

            /*$str = file_get_contents($fullPath);
            $bom = pack("CCC", 0xef, 0xbb, 0xbf);
            if (0 === strncmp($str, $bom, 3)) {
            $content['todo1']="BOM detectado";
            $str = substr($str, 3);
            }

            $content['todo2']=$str;*/
            
            /*Aún no
            if (($buffer = fgets(fopen($fullPath, 'r'), 4096)) !== false)
            {
                $buffer = $this->autoUTF($buffer);
                $prob=str_getcsv($buffer, ',');
            }
            $content['todo']=$buffer;*/

            $f=$this->detect_utf_8_bom($fullPath);
            $g=$this->detect_SJIS_win($fullPath);

            $content['utf_8'] = $f;
            $content['win'] = $g;

            
            if  (($file = fopen($fullPath, 'r')) !== FALSE) {
                $csvData = [];
                $filterData = [];
                while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                    $aux = [];
                    //$toodo= [];

                    //return explode($separator, iconv("CP1251", "UTF-8", $buffer));

                    //var_dump(mb_detect_encoding($row[1][8], 'UTF-8', 'ISO-8859-1'));
                    //var_dump(mb_detect_encoding($row[8]));
                    //$content['todo1']=mb_detect_encoding($row[0]);
                    
                    //$aux = mb_convert_encoding($row, "UTF-8", "ISO-8859-15");
                    foreach ($row as $r)  {
                        /*Funciona para todos
                        $aux[] = preg_replace('/[\x00-\x1F\x7F]/u', '', utf8_encode($r));*/

                        //$aux[]=iconv('Windows-1250', 'utf-8', $r); 
                        
                        //$toodo[] = mb_detect_encoding($r, "ASCII, UTF-8, UNICODE, sjis-win, ISO-8859-1");
                        //$aux[] = preg_replace('/[[:^print:]]/', '', utf8_encode($r));
                        //$aux[]=preg_replace('/[\x00-\x1F\x7F]/u', '', utf8_encode($r));
                        //Enviados pone un signo de ?  y no 
                        //$aux[] = utf8_encode($r);
                        //$aux[]=mb_convert_encoding($r, "UTF-8");
                        //$aux[]=mb_convert_encoding($r, "UTF-8");
                        //$aux[]=utf8_encode($r);
                        //$aux[] = $r;
                        //if(utf8_encode($r) === utf8_decode($r))
                        //$aux[] = preg_replace('/[^[:print:]\r\n]/','',iconv("UTF-8", "ISO-8859-1//TRANSLIT", $r));
                        //$aux[] = $r;
                        //$aux[]=iconv("UTF-8", "cp1252",$r);
                    }
                    //$too[] = $toodo;
                    $csvData[] = $aux;
                }
                fclose($file);

                array_shift($csvData);
                //csvData
                //$content['todo']=$csvData;
                //$content['todo2'] = $too;
                $rowNum = 2;
                $content['err'] = '';
                if(array_key_exists(9,$csvData[0])) {
                    foreach  ($csvData  as  $row)  {
                        $_1 = trim($row[0]);
                        $_2 = trim($row[1]);
                        $_3 = trim($row[2]);
                        $_4 = trim($row[3]);
                        $_5 = trim($row[4]);
                        $_6 = trim($row[5]);
                        $_7 = trim($row[6]);
                        $_8 = trim($row[7]);
                        $_9 = trim($row[8]);
                        $_10 = trim($row[9]);
                        $auxToFilter  =  [
                            'nombre' => '',  
                            'porcentaje_completado' => 0,  
                            'comienzo' => '',  
                            'fin' => '',  
                            'costo' => 0,  
                            'fin_real' => '',  
                            'edt' => '',
                            'resumen' => '',
                            'duracion' => '',
                            'duracion_restante' => '',
                        ];

                        $auxToFilter['nombre'] = $_1;
                        if(strpos($_2,'%') === false){
                            $auxToFilter['porcentaje_completado'] = floatval($_2);
                        } else {
                            $auxToFilter['porcentaje_completado'] = floatval(str_replace('%','',$_2));
                        }
                        $auxToFilter['comienzo'] = DateTime::createFromFormat('d/m/y',explode(" ",$_3)[1])->format('Y/m/d');
                        $auxToFilter['fin'] = DateTime::createFromFormat('d/m/y',explode(" ",$_4)[1])->format('Y/m/d');
                        if(strpos($_5,'$') === false) {
                            $auxToFilter['costo'] = floatval($_5);
                        } else {
                            $auxToFilter['costo'] = floatval(str_replace('$','',str_replace(',','',$_5)));
                        }
                        $auxToFilter['fin_real'] = $_6;
                        $auxToFilter['edt'] = $_7;
                        if($_8 === 'Sí') {
                            $auxToFilter['resumen'] = true;
                        } else if ($_8 === 'No') {
                            $auxToFilter['resumen'] = false;
                        }
                        $auxToFilter['duracion'] = $_9;
                        $auxToFilter['duracion_restante'] = $_10;
                
                        $filterData[]  =  $auxToFilter;
                        $rowNum++;
                    }
                } else {
                    $content['err'] =  "Error en el número de columnas";
                    $content['message']  =  'Error en el csv';    
                }
                
                
                if  ($content['err']  ===  '')  {
                    $content['result']  =  'success';
                    $content['message']  =  'Se  han  registrado  todos  los  registros.';

                    if  (count($filterData)  >  0)  {
                        foreach  ($filterData  as  $a)  {
                            $prueba =  new Cargacsv();
                            $prueba->nombre  =  $a['nombre'];
                            $prueba->porcentaje_completado  =  $a['porcentaje_completado'];
                            $prueba->comienzo =  $a['comienzo'];
                            $prueba->fin =  $a['fin'];
                            $prueba->costo =  $a['costo'];
                            $prueba->fin_real  =  $a['fin_real'];
                            $prueba->edt  =  $a['edt'];
                            $prueba->resumen =  $a['resumen'];
                            $prueba->duracion =  $a['duracion'];
                            $prueba->duracion_restante =  $a['duracion_restante'];

                            if  ($prueba->create()  !==  false)  {
                                $content['result']  =  'success';
                            }  else  {
                                $content['message']  =  'No  se  pudo  guardar  registro.';
                            }
                        }
                    }  else  {
                    	$content['message']  =  'Sin  datos  a  insertar.';                                                
                    }
                }
                if  (!$isCsv)  {
                    $content['message']  =  'No  es  un  .csv  válido!';
                }
            }
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
}