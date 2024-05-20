<?php

use Phalcon\Mvc\Controller;

class UsersController extends Controller
{
    public $content = ['result' => 'error', 'message' => ['title' => 'Error!', 'content' => 'Error del servidor.']];

    public function getProfile ()
    {
        $content = $this->content;
        $validUser = Auth::getUserData($this->config);
        $request = $this->request->getPost();

        if ($validUser !== null) {
            $user = SysUsers::findFirst($validUser->user_id)->toArray();
            $id_user = intval($user['id']);
            unset($user['password'], $user['password_code']);

            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/uploads/profile/';
            if (!file_exists($upload_dir . $user['id'])) {
                $user['image'] = 'default-user.png';
            }

            $content['user'] = $user;
            $content['menuData'] = [];
            
            # NO BORRAR
            # $content['rolesIds'] = [];
            # $grants = SysGrants::find('user_id = ' . $user['id']);

            # if ($grants->count() > 0) {
            #     foreach ($grants as $g) {
            #         $content['rolesIds'][] = $g->role_id;
            #         $smg = $g->SysRoles->MenuGrants;

            #         if ($smg->count() > 0) {
            #             foreach ($smg as $mg) {
            #                $content['menuData'][$mg->Menus->id] = [
            #                     'ord' => $mg->Menus->ord, 
            #                     'name' => $mg->Menus->label, 
            #                     'items' => MenuItems::find(['menu_id = ' . $mg->Menus->id, 'order' => 'ord ASC'])
            #                 ];
            #             }
            #         }
            #     }
            # }

            /*    
            $content['role'] = [];
            $grants = SysGrants::findFirst('user_id = ' . $user['id']);
            $content['role']=$grants->SysRoles->name;
            */

            $content['role'] = [];
            $grants = SysGrants::find('user_id = ' . $user['id']);
            if ($grants->count() > 0) {
                foreach ($grants as $g) {
                    $content['role'][] = $g->SysRoles->name;
                }
            }

            $sql="SELECT sys_menus.id as menu_id,sys_menus.label as menu, sys_menus.ord 
            from sys_menuitem_grants 
            JOIN sys_menu_items ON sys_menu_items.id = sys_menuitem_grants.item_id
            join sys_menus on sys_menus.id=sys_menu_items.menu_id
            WHERE role_id IN (select role_id from sys_grants where user_id = $user[id]) 
            group by menu,sys_menus.ord,sys_menus.id
            order by sys_menus.ord";

            $menus = $this->db->query($sql)->fetchAll();


            /*$sql = "SELECT sys_menu_grants.*, sys_menus.label as menu, sys_menus.ord from sys_menu_grants 
                JOIN sys_menus ON sys_menus.id = sys_menu_grants.menu_id
                WHERE role_id IN (select role_id from sys_grants where user_id = $user[id]) 
                ORDER BY sys_menus.ord ASC";
            $menus = $this->db->query($sql)->fetchAll();*/


            if (count($menus) > 0) {
                foreach ($menus as $m) {
                    $menu_id=$m['menu_id'];
                    $sqlItems="SELECT icon,sys_menuitem_grants.item_id as id,sys_menu_items.label as label,
                                sys_menus.id as menu_id,sys_menu_items.ord as ord,route
                                from sys_menuitem_grants join sys_menu_items on sys_menuitem_grants.item_id=sys_menu_items.id
                                join sys_menus on sys_menus.id=sys_menu_items.menu_id
                                where sys_menus.id=$menu_id and role_id IN (select role_id from sys_grants where user_id = $user[id])
                                order by sys_menu_items.ord";
                                $items = $this->db->query($sqlItems)->fetchAll();
                    $content['menuData']['_'. $m['menu_id']] = [
                         'ord' => $m['ord'], 
                         'name' => $m['menu'], 
                         'items' => $items
                     ];
                }
            }
            $content['result'] = 'success';
        } else {
            $content['message'] = 'No existe el usuario.';
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
    
    public function getAll ()
    {
        $sql = "SELECT sys_users.id, sys_users.account_id, sys_users.email, sys_users.nickname, 
        sys_users.created, sys_users.created_by, sys_users.modified, sys_users.modified_by,
        sys_roles.name, sys_grants.role_id as roles, sys_users.status
         FROM sys_users
        inner join sys_grants on sys_users.id = sys_grants.user_id
        inner join sys_roles on sys_roles.id = sys_grants.role_id
        order by sys_users.nickname";
        $this->content['users'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getClientes ()
    {
        $users = "SELECT sys_users.id,email,nickname,role_id,name from sys_users
        join sys_grants on sys_users.id = sys_grants.user_id
        join sys_roles on sys_roles.id = sys_grants.role_id
        where name ilike 'cliente'
        order by email";
        $this->content['users'] = $this->db->query($users)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getUser ()
    {
        $content = $this->content;
        $validUser = Auth::getUserData($this->config);
        $request = $this->request->getPost();

        if ($validUser !== null) {
            $user = SysUsers::findFirst($validUser->userId)->toArray();
            unset($user['password'], $user['password_code']);

            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/assets/uploads/profile/';
            if (!file_exists($upload_dir . $user['image'])) {
                $user['image'] = 'default-user.png';
            }

            $content['user'] = $user;
            $content['result'] = 'success';
        } else {
            $content['message'] = 'No existe el usuario.';
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function updateStatus () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $validUser = Auth::getUserData($this->config);
            $user = SysUsers::findFirst($request['id']);

            if ($user) {
                    $user->setTransaction($tx);
                    $user->status = $request['status'];

                    if ($user->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha actualizado el usuario.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($user);
                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo actualizar el usuario.'];
                        $tx->rollback();
                    }
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se ha podido crear su usuario'];

        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $email=strtolower($request['email']);

            $user = SysUsers::findFirst(
                [
                    'email = :email: OR nickname = :nickname:',
                    'bind' => [
                        'email' => $email,
                        'nickname' => strtoupper($request['nickname'])
                    ]
                ]
            );

            if ($user) {
                $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'Ya existe un usuario con esos datos.'];
            } else {
                $validUser = Auth::getUserData($this->config);

                if ($validUser !== null) {
                    $user = new SysUsers();
                    $user->setTransaction($tx);
                    $user->account_id = $validUser->account_id;
                    $user->email = $email;
                    $user->nickname = strtoupper($request['nickname']);
                    $user->password = sha1($request['password']);
                    $user->status = 'ACTIVO';

                    if ($user->create()) {

                        if (isset($request['roles'])) {
                            
                            if (count($request['roles']) > 0) {
                                foreach ($request['roles'] as $roleId) {
                                    $this->content['result'] = 'error';
                                    $grant = new SysGrants();
                                    $grant->setTransaction($tx);
                                    $grant->user_id = $user->id;
                                    $grant->role_id = $roleId;

                                    if ($grant->create()) {
                                    } else {
                                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo agregar los roles al usuario.'];
                                        $tx->rollback();
                                    }
                                }
                            }
                        }
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha creado el usuario.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($user);
                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo crear el usuario.'];
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

    public function updateProfile () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $validUser = Auth::getUserData($this->config);

            $email=strtolower($request['email']);

            $user = SysUsers::findFirst(
                [
                    'id != :id: AND (email = :email: OR nickname = :nickname:)',
                    'bind' => [
                        'email' => $email,
                        'nickname' => strtoupper($request['nickname']),
                        'id' => $validUser->user_id
                    ]
                ]
            );

            if ($user) {
                $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'Ya existe un usuario con esos datos.'];
            } else {
                $user = SysUsers::findFirst($validUser->user_id);
                if ($user) {
                    $user->setTransaction($tx);
                    $user->email = $email;
                    $user->nickname = strtoupper($request['nickname']);

                    if ($user->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha actualizado el usuario.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($user);
                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo actualizar el usuario.'];
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

    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $email=strtolower($request['email']);

            $user = SysUsers::findFirst(
                [
                    'id != :id: AND (email = :email: OR nickname = :nickname:)',
                    'bind' => [
                        'id' => intval($request['id']),
                        'email' => $email,
                        'nickname' => strtoupper($request['nickname'])
                    ]
                ]
            );

            if ($user) {
                $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'Ya existe un usuario con esos datos.'];
            } else {
                $user = SysUsers::findFirst($request['id']);
                if ($user) {
                    $user->setTransaction($tx);
                    $user->email = $email;
                    $user->nickname = strtoupper($request['nickname']);
                    if($request['password'] !== '***'){
                        $user->password = sha1($request['password']);
                    }
                    // $user->status = $request['status'];

                    if ($user->update()) {
                        $userGrants = $user->SysGrants;
                        if ($userGrants->count() > 0) {
                            foreach ($userGrants as $ug) {
                                $this->content['result'] = 'error';
                                $ug->setTransaction($tx);
                                
                                if ($ug->delete()) {
                                } else {
                                    $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo remover los roles del usuario.'];
                                    $tx->rollback();
                                }
                            }
                        }

                        if (isset($request['roles'])) {
                            if (count($request['roles']) > 0) {
                                foreach ($request['roles'] as $roleId) {
                                    $this->content['result'] = 'error';
                                    $grant = new SysGrants();
                                    $grant->setTransaction($tx);
                                    $grant->user_id = $user->id;
                                    $grant->role_id = $roleId;

                                    if ($grant->create()) {
                                    } else {
                                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo agregar los roles al usuario.'];
                                        $tx->rollback();
                                    }
                                }
                            }
                        }
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => 'Exito!', 'content' => 'Se ha actualizado el usuario.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($user);
                        $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo actualizar el usuario.'];
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
        
            if ($request['ids']) {
                foreach ($request['ids'] as $id) {

                    if (intval($id) === 1) {
                        $this->content['message'] = ['title' => 'Advertencia!', 'content' => 'No se puede borrar el Super Admin.']; 
                        $tx->rollback();
                    }

                    $user = SysUsers::findFirst($id);
                    $userGrants = $user->SysGrants;
                    if ($userGrants->count() > 0) {
                        foreach ($userGrants as $ug) {
                            $this->content['result'] = 'error';
                            $ug->setTransaction($tx);
                            
                            if ($ug->delete()) {
                                $user->setTransaction($tx);

                                if ($user->delete()) {
                                    $this->content['result'] = 'success';
                                } else {
                                    $this->content['error'] = Helpers::getErrors($user);
                                    $msgTitle = 'Error!';
                                    $msgContent = count($request['ids']) > 1 ? 'No se pudo borrar los usuarios.' : 'No se pudo borrar el usuario.';
                                    
                                    if ($this->content['error'][1] !== null) {
                                        $msgTitle = 'Advertencia!';
                                        $msgContent = $this->content['error'][1];
                                    }

                                    $this->content['message'] = ['title' => $msgTitle, 'content' => $msgContent];
                                    $tx->rollback();
                                }
                            } else {
                                $this->content['message'] = ['title' => 'Error!', 'content' => 'No se pudo remover los roles del usuario.'];
                                $tx->rollback();
                            }
                        }
                    }

                    /*$sql="SELECT id
                        from sys_grants
                        where user_id =$id";

                    $sysGrants = $this->db->query($sql)->fetchAll();
                    $grants = SysGrants::findFirst($sysGrants[0]['id']);
                    $grants->setTransaction($tx);

                    if($grants->delete()) {
                        $user = SysUsers::findFirst($id);
                        $user->setTransaction($tx);

                        if ($user->delete()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($user);
                            $msgTitle = 'Error!';
                            $msgContent = count($request['ids']) > 1 ? 'No se pudo borrar los usuarios.' : 'No se pudo borrar el usuario.';
                            
                            if ($this->content['error'][1] !== null) {
                                $msgTitle = 'Advertencia!';
                                $msgContent = $this->content['error'][1];
                            }

                            $this->content['message'] = ['title' => $msgTitle, 'content' => $msgContent];
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($grants);
                        $tx->rollback();
                    }*/
                    
                }

                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => 'Exito!', 'content' => count($request['ids']) > 1 ? 'Se han borrado los usuarios.' : 'Se ha borrado el usuario.'];
                    $tx->commit();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updatePassword()
    {
        $content = $this->content;
        $validUser = Auth::getUserData($this->config);
        $request = $this->request->getPut();
        if ($validUser !== null) {
            $user = SysUsers::findFirst($validUser->user_id);
            if (sha1($request['password']) === $user->password) {
                $user->password = sha1($request['newPassword']);
                if ($user->update() !== false) {
                    $content['result'] = 'success';
                    $content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la contraseña.'];
                }
            } else {
                $content['message'] = ['title' => '¡Error!', 'content' => 'La CONTRASEÑA que ingresó como ACTUAL no coincide con la base de datos.'];
            }
        }
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function uploadImage()
    {
        $content = $this->content;
        $validUser = Auth::getUserData($this->config);

        if ($this->request->hasFiles() && $validUser !== null) {
            $user = SysUsers::findFirst($validUser->userId);

            $upload_dir = __DIR__ . '../../../public//assets//uploads//profile/';

            foreach ($this->request->getUploadedFiles() as $file) {
                $img = uniqid('image-') . '.' . $file->getExtension();
                if ($user->image) {
                    $img = $user->image;
                }
                $file->moveTo($upload_dir . $img);
                if (file_exists($upload_dir . $img)) {
                    chmod($upload_dir . $img, 0777);
                    // $content['existe'] = TRUE;
                }
                $user->image = $img;
            }
            if ($user->update()) {
                $content['result'] = 'success';
            }

        } else {
            $content['message'] = 'No hay archivos.';
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function recoverPassword()
    {
        $content = $this->content;

        $request = $this->request->getPost();
        
        if (!empty($request['email'])) {

            $email=strtolower($request['email']);

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user = SysUsers::findFirst([
                    'email = :email:',
                    'bind' => [
                        "email" => $email,
                    ]
                ]);

                if ($user) {
                    $newPass = bin2hex(random_bytes(8));
                    $user->password = sha1($newPass);

                    $mailer = new Mailer();
                    $mailer->from = 'donotreply@ant.com.mx';
                    //$mailer->from = 'dragonballz2.h1@gmail.com';
                    $mailer->to = $user->email;
                    //$mailer->tousers = ['aleirbagmv@gmail.com'/*,'gmendoza@ant.com.mx'*/];
                    $mailer->subject = 'Recuperacion de contraseña';
                    $mailer->template = 'recover.xml';
                    $mailer->misc = [
                        //'name' => $user->first_name . ' ' . $user->last_name,
                        'name' => $user->nickname,
                        'password' => $newPass,
                        // 'logo' => 'api.lubrifiltros.beta.antfarm.mx/public/assets/images/logo.png'
                        'logo' => 'http://impact.localhost/public/assets/images/logo.png'
                    ];

                    $result_message = null;
                    if ($user->update() !== false) {
                        try {
                            $result_message = $mailer->sendEmail();
                            $content['result'] = 'success';
                        } catch (Exception $e) {
                            $result_message = (object)array(
                                'status' => false,
                                'message' => $e->getMessage()
                            );
                        }
                    }

                    $content['mail_message'] = $result_message;
                } else {
                    $content['message'] = 'Por favor revise el email ingresado.';
                }
            } else {
                $content['message'] = 'Por favor revise el email ingresado.';
            }
        } else {
            $content['message'] = 'Por favor no deje campos vacios.';
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }
}