<?php

use Phalcon\Mvc\Controller;

class MenusController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => '¡Error!', 'content' => 'Error del servidor.']];

    public function get ($id) {
        $this->content['menu'] = Menus::findFirst($id);
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAll ()
    {
        $sql = "SELECT *
            FROM sys_menus
            ORDER BY id DESC";
        $this->content['menus'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $menu = Menus::findFirst(
                [
                    'label = :label:',
                    'bind' => [
                        'label' => $request['label']
                    ]
                ]
            );

            if ($menu) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un menú con ese nombre.'];
            } else {
                $menu = new Menus();
                $menu->setTransaction($tx);
                $menu->label = $request['label'];
                $menu->ord = $request['ord'];
                $menu->descripcion = $request['descripcion'];

                if ($menu->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el menú.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($menu);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el menú.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $menu = Menus::findFirst(
                [
                    'id != :id: AND label = :label:',
                    'bind' => [
                        'id' => $request['id'],
                        'label' => $request['label']
                    ]
                ]
            );

            if ($menu) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un menú con ese nombre.'];
            } else {
                $menu = Menus::findFirst($request['id']);
                $menu->setTransaction($tx);
                $menu->label = $request['label'];
                $menu->ord = $request['ord'];
                $menu->descripcion = $request['descripcion'];

                if ($menu->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el menú.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($menu);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el menú.'];
                    $tx->rollback();
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
        
            if ($request['ids']) {
                foreach ($request['ids'] as $id) {
                    $menu = Menus::findFirst($id);
                    $menu->setTransaction($tx);

                    if ($menu->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($menu);
                        $msgTitle = '¡Error!';
                        $msgContent = count($request['ids']) > 1 ? 'No se pudo borrar los menús.' : 'No se pudo borrar el menú.';
                        
                        if ($this->content['error'][1] !== null) {
                            $msgTitle = '¡Advertencia!';
                            $msgContent = $this->content['error'][1];
                        }
                        $this->content['message'] = ['title' => $msgTitle, 'content' => $msgContent];
                        $tx->rollback();
                    }
                }

                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => count($request['ids']) > 1 ? 'Se han borrado los menús.' : 'Se ha borrado el menú.'];
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function export ()
    {
        $content = $this->content;
        
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');
        fputcsv($fp, ['#', 'Nombre', 'Padre', 'Privilegios'], ',');

        $roles = SysRoles::find(['order' => 'id']);
        
        if ($roles->count() > 0) {
            foreach ($roles as $r) {
                $privileges = [];
                $privilegesText = '';
                if ($r->SysRolesHasPrivileges->count() > 0) {
                    foreach ($r->SysRolesHasPrivileges as $srhp) {
                        $privileges[] = $srhp->SysPrivileges->name;
                    }
                    $privilegesText = implode(', ', $privileges);
                } else {
                    $privilegesText = 'Ninguno';
                }

                fputcsv($fp, [
                    $r->id, 
                    utf8_decode($r->name),
                    utf8_decode($r->parentRole ? $r->parentRole->name : 'Sin padre'),
                    utf8_decode($privilegesText),
                ], ',');
            }
            $content['result'] = 'success';
        }  
        rewind($fp);
        $output = stream_get_contents($fp);
        mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
        fclose($fp);

        $this->response->resetHeaders();
        $this->response->setHeader('Content-Type', 'application/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Roles-' . date('Y-m-d') . '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }
}