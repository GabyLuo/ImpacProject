<?php
use Phalcon\Mvc\Controller;

class CotizacionesDetallesController extends Controller {

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * from crm_cotizaciones_detalles";
        $this->content['detalles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }


    public function getByCotizacion ($cotizacion_id)
    {
        $sql = "SELECT crm_cotizaciones_detalles.*, (cantidad * precio) as total from crm_cotizaciones_detalles where cotizacion_id = $cotizacion_id";
        $data = $this->db->query($sql)->fetchAll();

        foreach ($data as $key => $detalle) {
        	$data[$key]['cantidad_formato'] = number_format(floatval($detalle['cantidad']),2,'.',',');
        	$data[$key]['precio_formato'] = number_format(floatval($detalle['precio']),2,'.',',');
            $data[$key]['total'] = number_format(floatval($detalle['total']),2,'.',',');
        }

        $this->content['detalles'] = $data;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
  
    public function save () {
        try{
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $detalles= new CotizacionesDetalles();
            $detalles->setTransaction($tx);
            $detalles->cantidad = floatval($request['cantidad']);
            $detalles->precio = floatval($request['precio']);
            $detalles->lote_progresivo=trim($request['lote_progresivo']) == '' ? null : trim($request['lote_progresivo']);
            $detalles->cotizacion_id=intval($request['cotizacion_id']);
            $detalles->articulo=trim($request['articulo']) == '' ? null : trim($request['articulo']);
            $detalles->descripcion=trim($request['descripcion']) == '' ? null : trim($request['descripcion']);
            $detalles->unidad=trim($request['unidad']) == '' ? null : trim($request['unidad']);
            if ($detalles->create()) {
            	$this->content['result']='success';
                $this->content['message']= ['title' => '¡Exito!', 'content' => 'Se ha guardado el detalle'];
                $tx->commit();
            } else {
            	$this->content['error'] = Helpers::getErrors($detalles);
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el detalle'];
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

            $detalles = CotizacionesDetalles::findFirst($request['id']);
            if ($detalles) {
                $detalles->setTransaction($tx);
                $detalles->cantidad = floatval($request['cantidad']);
	            $detalles->precio = floatval($request['precio']);
	            $detalles->lote_progresivo=trim($request['lote_progresivo']) == '' ? null : trim($request['lote_progresivo']);
	            $detalles->cotizacion_id=intval($request['cotizacion_id']);
	            $detalles->articulo=trim($request['articulo']) == '' ? null : trim($request['articulo']);
	            $detalles->descripcion=trim($request['descripcion']) == '' ? null : trim($request['descripcion']);
	            $detalles->unidad=trim($request['unidad']) == '' ? null : trim($request['unidad']);
                if ($detalles->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el detalle.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($detalles);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el detalle.'];
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
                $detalles = CotizacionesDetalles::findFirst($request['id']);
                $detalles->setTransaction($tx);
                if ($detalles->delete()) {
                    $this->content['result'] = 'success';
                } else {
                    $this->content['error'] = Helpers::getErrors($detalles);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                    $tx->rollback();
                }
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el detalle.'];
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