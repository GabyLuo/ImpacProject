<?php

use Phalcon\Mvc\Controller;

class MenuItemsControllers extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => '¡Error!', 'content' => 'Error del servidor.']];

    public function get ($id) {
        $this->content['menu'] = Menus::findFirst($id);
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    /* public function getAll ()
    {
        $sql = "SELECT *
            FROM sys_menus_
            ORDER BY id DESC";
        $this->content['menus'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    } */ 

    public function getAll ()
    {
        $sql = "SELECT *
            FROM sys_menu_items
            ORDER BY label";
        $this->content['menuItems'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByMenu ($menu_id)
    {
        $sql = "SELECT *
        	FROM sys_menu_items
        	WHERE menu_id = $menu_id
            ORDER BY id DESC";
        $this->content['menuItems'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }


    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $menuItem = MenuItems::findFirst(
                [
                    'menu_id = :menu_id: AND label = :label:',
                    'bind' => [
                        'menu_id' => $request['menu_id'],
                        'label' => $request['label']
                    ]
                ]
            );

            if ($menuItem) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un sub-menú con ese nombre.'];
            } else {
                $menuItem = new MenuItems();
                $menuItem->setTransaction($tx);
                $menuItem->menu_id = $request['menu_id'];
                $menuItem->label = $request['label'];
                $menuItem->route = $request['route'];
                $menuItem->icon = $request['icon'];
                $menuItem->ord = $request['ord'];

                if ($menuItem->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado el sub-menú.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($menuItem);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el sub-menú.'];
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

            $menuItem = MenuItems::findFirst(
                [
                    'id != :id: AND (menu_id = :menu_id: AND label = :label:)',
                    'bind' => [
                        'id' => $request['id'],
                        'menu_id' => $request['menu_id'],
                        'label' => $request['label']
                    ]
                ]
            );

            if ($menuItem) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe un sub-menú con ese nombre.'];
            } else {
                $menuItem = MenuItems::findFirst($request['id']);
                $menuItem->setTransaction($tx);
                $menuItem->menu_id = $request['menu_id'];
                $menuItem->label = $request['label'];
                $menuItem->route = $request['route'];
                $menuItem->icon = $request['icon'];
                $menuItem->ord = $request['ord'];

                if ($menuItem->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el sub-menú.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($menuItem);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el sub-menú.'];
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
                    $menuItem = MenuItems::findFirst($id);
                    $menuItem->setTransaction($tx);

                    if ($menuItem->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($menuItem);
                        $msgTitle = '¡Error!';
                        $msgContent = count($request['ids']) > 1 ? 'No se pudo borrar los sub-menús.' : 'No se pudo borrar el sub-menú.';
                        
                        if ($this->content['error'][1] !== null) {
                            $msgTitle = '¡Advertencia!';
                            $msgContent = $this->content['error'][1];
                        }
                        $this->content['message'] = ['title' => $msgTitle, 'content' => $msgContent];
                        $tx->rollback();
                    }
                }

                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => count($request['ids']) > 1 ? 'Se han borrado los sub-menús.' : 'Se ha borrado el sub-menú.'];
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