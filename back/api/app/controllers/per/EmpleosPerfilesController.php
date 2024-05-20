<?php

use Phalcon\Mvc\Controller;

class EmpleosPerfilesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT id, perfil_id, empresa, puesto, to_char(fecha_ingreso,'DD/MM/YYYY') as fecha_ingreso, to_char(fecha_egreso,'DD/MM/YYYY') as fecha_egreso, laborando 
            FROM per_empleos
            ORDER BY empresa";
        $this->content['empleos'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT id, perfil_id, empresa, puesto, to_char(fecha_ingreso,'DD/MM/YYYY') as fecha_ingreso, to_char(fecha_egreso,'DD/MM/YYYY') as fecha_egreso, laborando 
            FROM per_empleos
            where perfil_id = $perfil_id
            ORDER BY empresa";
        $empleos = $this->db->query($sql)->fetchAll();

        $nuevo = [];
        foreach ($empleos as $elemento){
            $empleo=(object)array();
            $empleo->id = $elemento['id'];
            $empleo->perfil_id = $elemento['perfil_id'];
            $empleo->empresa = $elemento['empresa'];
            $empleo->puesto = $elemento['puesto'];
            $empleo->fecha_ingreso = $elemento['fecha_ingreso'];
            $empleo->fecha_egreso = $elemento['fecha_egreso'];
            $empleo->laborando = $elemento['laborando'];
            $empleo_id = $elemento['id'];
            $sql_cartas = "SELECT * 
                FROM per_cartas
                where perfil_id = $perfil_id and empleo_id = $empleo_id
                ORDER BY id";
            $cartas = $this->db->query($sql_cartas)->fetchAll();
            $nuevo_cartas = [];
            foreach ($cartas as $element){
                $carta=(object)array();
                $carta->id = $element['id'];
                $carta->perfil_id = $element['perfil_id'];
                $carta->archivo = $element['archivo'];
                $carta->tipo = $element['tipo'];
                $carta->extension = $element['extension'];
                if ($element['extension'] === 'docx') {
                    $carta->color = 'blue-9';
                    $carta->icon = 'fas fa-file-word';
                } else if ($element['extension'] === 'pdf' || $element['extension'] === 'PDF') {
                    $carta->color = 'red-10';
                    $carta->icon = 'fas fa-file-pdf';
                } else if ($element['extension'] === 'xml' || $element['extension'] === 'XML') {
                    $carta->color = 'light-green';
                    $carta->icon = 'fas fa-file-code';
                } else if ($element['extension'] === 'jpg' || $element['extension'] === 'jpeg' || $element['extension'] === 'png' || $element['extension'] === 'JPG' || $element['extension'] === 'JPEG' || $element['extension'] === 'PNG') {
                    $carta->color = 'amber';
                    $carta->icon = 'fas fa-file-image';
                }
                array_push($nuevo_cartas,$carta);
            }
            $empleo->cartas = $nuevo_cartas;
            
            array_push($nuevo,$empleo);
        }
        $this->content['empleos'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $empleos = EmpleosPerfiles::findFirst(
                [
                    'puesto = :puesto: AND (perfil_id = :perfil_id:) AND (empresa = :empresa:)',
                    'bind' => [
                        'puesto' => strtoupper($request['puesto']),
                        'perfil_id' => intval($request['perfil_id']),
                        'empresa' => strtoupper($request['empresa'])
                    ]
                ]
            );
            if ($empleos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un empleo con este nombre para este perfil'];
            } else {
                $empleos = new EmpleosPerfiles();
                $empleos->setTransaction($tx);
                $empleos->perfil_id = intval($request['perfil_id']);
                $empleos->empresa = trim(strtoupper($request['empresa']));
                $empleos->puesto = trim(strtoupper($request['puesto']));
                $empleos->fecha_ingreso = date("Y/m/d", strtotime($request['fecha_ingreso']));
                if ($request['fecha_egreso'] === '') {
                    $empleos->fecha_egreso = null;
                    $empleos->laborando = true;
                } else {
                    $empleos->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                    $empleos->laborando = false;
                }
                if ($empleos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el empleo.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($empleos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el empleo.'];
                    $tx->rollback();
                }
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

            $empleos = EmpleosPerfiles::findFirst(
                [
                    'id != :id: AND (empresa = :empresa:) AND (perfil_id = :perfil_id:) AND (puesto = :puesto:)',
                    'bind' => [
                        'empresa' => strtoupper($request['empresa']),
                        'puesto' => strtoupper($request['puesto']),
                        'perfil_id' => intval($request['perfil_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($empleos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un empleo con este nombre para este perfil'];
            } else {
                $empleos = EmpleosPerfiles::findFirst($request['id']);
                if ($empleos) {
                    $empleos->setTransaction($tx);
                    $empleos->perfil_id = intval($request['perfil_id']);
                    $empleos->empresa = trim(strtoupper($request['empresa']));
                    $empleos->puesto = trim(strtoupper($request['puesto']));
                    $empleos->fecha_ingreso = date("Y/m/d", strtotime($request['fecha_ingreso']));
                    if ($request['fecha_egreso'] === '') {
                        $empleos->fecha_egreso = null;
                        $empleos->laborando = true;
                    } else {
                        $empleos->fecha_egreso = date("Y/m/d", strtotime($request['fecha_egreso']));
                        $empleos->laborando = false;
                    }
                    if ($empleos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el empleo.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($empleos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el empleo'];
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

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $empleos = EmpleosPerfiles::findFirst($request['id']);
                    $empleos->setTransaction($tx);

                    if ($empleos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($empleos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el empleo.'];
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