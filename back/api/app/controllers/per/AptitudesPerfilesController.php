<?php

use Phalcon\Mvc\Controller;

class AptitudesPerfilesController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT * 
            FROM per_aptitudes_perfiles
            ORDER BY nombre";
        $this->content['aptitudes_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByPerfil ($perfil_id)
    {
        $sql = "SELECT * 
            FROM per_aptitudes_perfiles
            where perfil_id = $perfil_id order by aptitud";
        $this->content['aptitudes_perfiles'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $aptitudes = Aptitudes::findFirst(
                [
                    'nombre = :nombre:',
                    'bind' => [
                        'nombre' => strtoupper($request['aptitud'])
                    ]
                ]
            );
            if ($aptitudes) {
                $aptitudes_perfiles = AptitudesPerfiles::findFirst(
                    [
                        'aptitud = :aptitud: AND (perfil_id = :perfil_id:)',
                        'bind' => [
                            'aptitud' => strtoupper($request['aptitud']),
                            'perfil_id' => intval($request['perfil_id'])
                        ]
                    ]
                );
                if ($aptitudes_perfiles) {
                    $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Este perfil ya tiene registrada la aptitud'];
                } else {
                    $aptitudes_perfiles = new AptitudesPerfiles();
                    $aptitudes_perfiles->setTransaction($tx);
                    $aptitudes_perfiles->perfil_id = intval($request['perfil_id']);
                    $aptitudes_perfiles->aptitud = trim(strtoupper($request['aptitud']));
                    if ($aptitudes_perfiles->create()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido la aptitud al perfil.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($aptitudes_perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir la aptitud al perfil.'];
                        $tx->rollback();
                    }
                }
            } else {
                $aptitudes = new Aptitudes();
                $aptitudes->setTransaction($tx);
                $aptitudes->nombre = trim(strtoupper($request['aptitud']));
                if ($aptitudes->create()) {
                    $aptitudes_perfiles = new AptitudesPerfiles();
                    $aptitudes_perfiles->setTransaction($tx);
                    $aptitudes_perfiles->perfil_id = intval($request['perfil_id']);
                    $aptitudes_perfiles->aptitud = trim(strtoupper($request['aptitud']));
                    if ($aptitudes_perfiles->create()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha añadido la aptitud al perfil.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($aptitudes_perfiles);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir la aptitud al perfil.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($aptitudes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo añadir la aptitud al perfil.'];
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

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $aptitudes = AptitudesPerfiles::findFirst($request['id']);
                    $aptitudes->setTransaction($tx);

                    if ($aptitudes->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($aptitudes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha eliminado la aptitud de este perfil.'];
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