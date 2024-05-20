<?php

use Phalcon\Mvc\Controller;

class LicitacionesPerfilesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_licitaciones";
        $this->content['licitaciones_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT per_licitaciones.id, per_licitaciones.licitacion_id, per_licitaciones.perfil_id, per_licitaciones.created,per_licitaciones.participacion, lic_licitacion.folio, lic_licitacion.descripcion FROM per_licitaciones
            inner join lic_licitacion on lic_licitacion.id = per_licitaciones.licitacion_id and perfil_id = $perfil_id
            ORDER BY lic_licitacion.descripcion";
        $this->content['licitaciones_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByLicitacion ($licitacion_id)
    {
        $sql = "SELECT per_licitaciones.id, per_licitaciones.licitacion_id, per_licitaciones.perfil_id, per_licitaciones.created, per_licitaciones.participacion, per_perfiles_expertos.nombre, per_perfiles_expertos.apellido_paterno, per_perfiles_expertos.licenciatura, per_perfiles_expertos.apellido_materno, (select per_areas.nombre as area from per_areas where per_areas.id = per_perfiles_expertos.area_id) FROM per_licitaciones
            inner join per_perfiles_expertos on per_perfiles_expertos.id = per_licitaciones.perfil_id and licitacion_id = $licitacion_id
            ORDER BY per_licitaciones.perfil_id";
        $this->content['licitaciones_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $proyectos = LicitacionesPerfiles::findFirst(
                [
                    'licitacion_id = :licitacion_id: AND (perfil_id = :perfil_id:)',
                    'bind' => [
                        'licitacion_id' => intval($request['licitacion_id']),
                        'perfil_id' => intval($request['perfil_id'])
                    ]
                ]
            );
            if ($proyectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este perfil ya está involucrado en la licitación'];
            } else {
                $proyectos = new LicitacionesPerfiles();
                $proyectos->setTransaction($tx);
                $proyectos->perfil_id = intval($request['perfil_id']);
                $proyectos->licitacion_id = intval($request['licitacion_id']);
                $proyectos->participacion = trim(strtoupper($request['participacion']));
                if ($proyectos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha agregado el perfil a la licitación.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proyectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el perfil a la licitación.'];
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

            $proyectos = LicitacionesPerfiles::findFirst(
                [
                    'id != :id: AND (licitaciones_id = :licitaciones_id:) AND (perfil_id = :perfil_id:)',
                    'bind' => [
                        'licitaciones_id' => intval($request['licitaciones_id']),
                        'perfil_id' => intval($request['perfil_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($proyectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe ese perfil involucrado a esta licitación'];
            } else {
                $proyectos = LicitacionesPerfiles::findFirst($request['id']);
                if ($proyectos) {
                    $proyectos->setTransaction($tx);
                    $proyectos->perfil_id = intval($request['perfil_id']);
                    $proyectos->participacion = intval($request['participacion']);
                    if ($proyectos->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el perfil.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($proyectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el perfil'];
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
                    $proyectos = LicitacionesPerfiles::findFirst($request['id']);
                    $proyectos->setTransaction($tx);

                    if ($proyectos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($proyectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el perfil de esta licitación.'];
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