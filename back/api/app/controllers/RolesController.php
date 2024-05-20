<?php

use Phalcon\Mvc\Controller;

class RolesController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => 'Error!', 'content' => 'Error del servidor.']];

    public function get ($id) {
        $content = $this->content;
        $content['role'] = SysRoles::findFirst($id);
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function getAll ()
    {
        $sql = "SELECT sys_roles.*, sr.name AS parent 
            FROM sys_roles
            LEFT JOIN sys_roles sr ON sr.id = sys_roles.parent_id 
            ORDER BY sys_roles.id DESC";
        $roles = $this->db->query($sql)->fetchAll();

        $this->content['roles'] = array_map(
            function ($r) {
                $r['menuItems'] = [];
                $grants = MenuItemGrants::find('role_id = ' . $r['id']);

                if ($grants->count() > 0) {
                    foreach ($grants as $g) {
                        $r['menuItems'][] = $g->item_id;
                    }
                }
                return $r;
            }, $roles);
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $role = SysRoles::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => $request['name']
                    ]
                ]
            );

            if ($role) {
                $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'Ya existe un rol con ese nombre.'];
            } else {
                $role = new SysRoles();
                $role->setTransaction($tx);
                if (intval($request['parent_id']) > 0) {
                    $role->parent_id = $request['parent_id'];
                }
                $role->name = $request['name'];

                if ($role->create()) {

                    if (isset($request['menuItems'])) {
                        
                        if (count($request['menuItems']) > 0) {
                            foreach ($request['menuItems'] as $menuId) {
                                $this->content['result'] = 'error';
                                $grant = new MenuItemGrants();
                                $grant->setTransaction($tx);
                                $grant->item_id = $menuId;
                                $grant->role_id = $role->id;

                                if ($grant->create()) {
                                } else {
                                    $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo agregar los accesos a este rol.'];
                                    $tx->rollack();
                                }
                            }
                        }
                    }

                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha creado el rol.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($role);
                    $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo crear el rol.'];
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

            $role = SysRoles::findFirst(
                [
                    'id != :id: AND name = :name:',
                    'bind' => [
                        'id' => $request['id'],
                        'name' => $request['name']
                    ]
                ]
            );

            if ($role) {
                $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'Ya existe un rol con ese nombre.'];
            } else {
                $role = SysRoles::findFirst($request['id']);
                $role->setTransaction($tx);

                if (intval($request['parent_id']) > 0) {
                    $role->parent_id = $request['parent_id'];
                } else {
                    $role->parent_id = null;
                }
                $role->name = $request['name'];

                if ($role->update()) {
                    $roleGrants = MenuItemGrants::find('role_id='.$role->id);
                    //$roleGrants = $role->MenuItemGrants;
                    if ($roleGrants->count() > 0) {
                        foreach ($roleGrants as $rg) {
                            $this->content['result'] = 'error';
                            $rg->setTransaction($tx);
                            
                            if ($rg->delete()) {
                            } else {
                                $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo remover los menús de del rol.'];
                                $tx->rollack();
                            }
                        }
                    }

                    if (isset($request['menuItems'])) {
                        if (count($request['menuItems']) > 0) {
                            foreach ($request['menuItems'] as $menuId) {
                                $this->content['result'] = 'error';
                                $grant = new MenuItemGrants();
                                $grant->setTransaction($tx);
                                $grant->item_id = $menuId;
                                $grant->role_id = $role->id;

                                if ($grant->create()) {
                                } else {
                                    $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo agregar los menús al rol.'];
                                    $tx->rollack();
                                }
                            }
                        }
                    }

                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha actualizado el rol.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($role);
                    $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo actualizar el rol.'];
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
                    $role = SysRoles::findFirst($id);
                    $role->setTransaction($tx);

                    if ($role->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($role);
                        $msgTitle = 'Error!';
                        $msgContent = count($request['ids']) > 1 ? 'No se pudo borrar los roles.' : 'No se pudo borrar el rol.';
                        
                        if ($this->content['error'][1] !== null) {
                            $msgTitle = 'Advertencia!';
                            $msgContent = $this->content['error'][1];
                        }
                        $this->content['message'] = ['title' => $msgTitle, 'content' => $msgContent];
                        $tx->rollback();
                    }
                }

                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => 'Exito!', 'content' => count($request['ids']) > 1 ? 'Se han borrado los roles.' : 'Se ha borrado el rol.'];
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