<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 17/09/17
 * Time: 11:42 AM
 */

use Phalcon\Mvc\Controller;

class DocumentsController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];
    
    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();

            if ($request['documento_id']&&$request['opcion']) {
                if($request['opcion']==='operadores'){
                    $operador = Operadores::findFirst($request['id']);
                } else if ($request['opcion']==='unidades'){
                    $operador = Unidades::findFirst($request['id']);
                }
                if($operador){
                    $operador->setTransaction($tx);
                    if($request['opcion']==='operadores'){
                        $operador->licencia_imagen_id = null;
                    }else if ($request['opcion']==='unidades'){
                        if($request['campo']==='poliza'){
                            $operador->poliza_imagen_id = null;
                        } else if($request['campo']==='unidadImagen'){
                            $operador->imagen_id = null;
                        } else if($request['campo']==='tarjeta'){
                            $operador->tarjeta_circulacion_id = null;
                        }
                    }
                    if ($operador->update()) {
                            $documento = SysDocuments::findFirst($request['documento_id']);
                            $documento->setTransaction($tx);
                            if($documento->delete()){
                                $this->content['result'] = 'success';
                            }else{
                                $this->content['err'] = 'Error';
                                $this->content['error'] = Helpers::getErrors($documento);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        
                        if ($this->content['result'] === 'success') {
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha borrado el archivo.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($operador);
                        if ($this->content['err'] === 'Error') {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo realizar esta acción'];
                        }
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
    
}