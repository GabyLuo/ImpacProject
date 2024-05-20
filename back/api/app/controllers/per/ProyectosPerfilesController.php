<?php

use Phalcon\Mvc\Controller;

class ProyectosPerfilesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_proyectos";
        $this->content['proyectos_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT per_proyecto.id, per_proyecto.proyecto_id, per_proyecto.perfil_id, per_proyecto.created, vnt_recurso.codigo, vnt_recurso.nombre FROM per_proyecto
            inner join vnt_recurso on vnt_recurso.id = per_proyecto.proyecto_id and perfil_id = $perfil_id
            ORDER BY vnt_recurso.nombre";
        $this->content['proyectos_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProject ($proyecto_id)
    {
        $sql = "SELECT per_proyectos.id, per_proyectos.proyecto_id, per_proyectos.perfil_id, per_proyectos.created, per_proyectos.participacion, per_perfiles_expertos.nombre, per_perfiles_expertos.apellido_paterno, per_perfiles_expertos.apellido_materno FROM per_proyectos
            inner join per_perfiles_expertos on per_perfiles_expertos.id = per_proyectos.perfil_id and proyecto_id = $proyecto_id
            ORDER BY per_proyectos.perfil_id";
        $this->content['proyectos_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $proyectos = ProyectosPerfiles::findFirst(
                [
                    'proyecto_id = :proyecto_id: AND (perfil_id = :perfil_id:)',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'perfil_id' => intval($request['perfil_id'])
                    ]
                ]
            );
            if ($proyectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este perfil ya está involucrado en el project'];
            } else {
                $proyectos = new ProyectosPerfiles();
                $proyectos->setTransaction($tx);
                $proyectos->perfil_id = intval($request['perfil_id']);
                $proyectos->proyecto_id = intval($request['proyecto_id']);
                $proyectos->participacion = trim(strtoupper($request['participacion']));
                if ($proyectos->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha agregado el perfil al project.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($proyectos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el perfil al project.'];
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

            $proyectos = ProyectosPerfiles::findFirst(
                [
                    'id != :id: AND (proyecto_id = :proyecto_id:) AND (perfil_id = :perfil_id:)',
                    'bind' => [
                        'proyecto_id' => intval($request['proyecto_id']),
                        'perfil_id' => intval($request['perfil_id']),
                        'id' => intval($request['id'])
                    ]
                ]
            );
            if ($proyectos) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe ese perfil involucrado en este proyecto'];
            } else {
                $proyectos = ProyectosPerfiles::findFirst($request['id']);
                if ($proyectos) {
                    $proyectos->setTransaction($tx);
                    $proyectos->perfil_id = intval($request['perfil_id']);
                    $proyectos->proyecto_id = intval($request['proyecto_id']);
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
                    $proyectos = ProyectosPerfiles::findFirst($request['id']);
                    $proyectos->setTransaction($tx);

                    if ($proyectos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($proyectos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado el perfil de este project.'];
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