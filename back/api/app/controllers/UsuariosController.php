<?php

use Phalcon\Mvc\Controller;

class UsuariosController extends Controller
{

    /*
    public $content = ["code"=>0, "result"=>"error"];

    // TODO - Definitivamente corregir la comprobacion de roles
    public function getAll ()
    {
        $content = $this->content;

        $users = SysUsers::find(["order" => "nickname"]);
        
        // $validUser = Auth::getUserData($this->config);
        // $userRol = Auth::getUserRol($validUser->userId);

        $content['usuarios'] = [];

        if ($users->count() > 0) {
            $auxUsuarios = [];
            foreach ($users as $user) {
                $u = $user->toArray();
                unset($u['password'], $u['password_code']);
                
                $roles = '';
                foreach ($user->SysGrants as $grant) {
                    $roles = $grant->SysRoles->name;
                }

                // if ($userRol === 'Super Almacén' || $userRol === 'Almacén') {
                //     if ($roles === 'Super Almacén' || $roles === 'Almacén') {
                //         $auxUsuarios[] = array_merge($u, ['rol' => $roles]);
                //     }
                // } else {
                    $auxUsuarios[] = array_merge($u, ['rol' => $roles]);
                // }
            }
            $content['result'] = 'success';
            $content['usuarios'] = $auxUsuarios;
        }
        
        $this->response->setJsonContent($content);

        $this->response->send();
    }
    */

    /*public function getAll ()
    {
        $content = $this->content;

        $sql = "SELECT u.id,
                       u.account_id,
                       u.nickname,
                       u.email,
                       u.first_name,
                       u.last_name,
                       u.image,
                       (SELECT r.name
                        FROM sys_grants AS g
                        JOIN sys_roles AS r ON g.role_id = r.id
                        WHERE user_id = u.id) AS rol
                FROM sys_users as u
                ORDER BY nickname;";

        $data = $this->db->query($sql);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $usuarios = $data->fetchAll();
        $content['result'] = 'success';
        $content['usuarios'] = $usuarios;
        
        $this->response->setJsonContent($content);
        $this->response->send();
    }*/
    
    /*
    public function save()
    {
        $content = $this->content;

        $request = $this->request->getPost();

        $usuario = SysUsers::findFirst([
            "nickname = :nickname:",
            "bind" => [
                "nickname" => $request['nickname']
            ]
        ]);

        if ($usuario !== false) {
            $content['message'] = 'Ya existe ese usuario.';
        } else {
            $usuario = new SysUsers();
            $usuario->nickname = $request['nickname'];
            $usuario->email = $request['email'];
            $usuario->password = sha1($request['password']);
            $usuario->numerotel = $request['numerotel'];
            $usuario->first_name = $request['first_name'];
            $usuario->last_name = $request['last_name'];
            
            if ($usuario->create() !== false) {
                if ($request['user_role'] != null || $request['user_role'] != 'null') {
                    $grant = new SysGrants();
                    $grant->user_id = $usuario->id;
                    $grant->role_id = $request['user_role'];

                    if ($grant->create() !== false) {
                        $content['result'] = 'success';
                    }                    
                }
                $content['result'] = 'success';
            }
        }        

        $this->response->setJsonContent($content);

        $this->response->send();
    }

    public function update ()
    {
        $content = $this->content;

        $request = $this->request->getPut();

        $usuario = SysUsers::findFirst([
            "id != :id: AND nickname = :nickname:",
            "bind" => [
                "id" => $request['id'],
                "nickname" => $request['nickname']
            ]
        ]);

        if ($usuario !== false) {
            $content['message'] = 'El nombre de usuario ingresado ya se encuentra en uso.';
        } else {
            $usuario = SysUsers::findFirst($request['id']);
            if ($request['sa'] == 1) {
                if ($request['password'] !== '' && $request['password'] !== null) {
                  $usuario->password = sha1($request['password']);
                }
                else {
                    $content['result'] = 'error';
                    $this->response->setJsonContent($content);
                    $this->response->send();
                }
            }
            else {
                $usuario->nickname = $request['nickname'];
                $usuario->email = $request['email'];
                $usuario->numerotel = $request['numerotel'];
                $usuario->first_name = $request['first_name'];
                $usuario->last_name = $request['last_name'];
            }
            
            if ($usuario->update() !== false) {
                $grant = SysGrants::findFirst([
                    "user_id = :user_id:",
                    "bind" => [
                        "user_id" => $usuario->id
                    ]
                ]);
                if ($request['sa'] == 0) {
                    if ($grant != false) {
                        $grant->delete();
                    }
                    if ($request['user_role'] != null || $request['user_role'] != 'null') {
                        $grant = new SysGrants();
                        $grant->user_id = $usuario->id;
                        $grant->role_id = $request['user_role'];

                        if ($grant->create() !== false) {
                            $content['result'] = 'success';
                        }                    
                    }
                }
                $content['result'] = 'success';
            }
        }        

        $this->response->setJsonContent($content);

        $this->response->send();
    }

    public function delete ()
    {   
        $content = $this->content;

        $request = $this->request->getPost();
        
        if ($request['ids']) {
        	foreach ($request['ids'] as $id) {
        		$usuario = SysUsers::findFirst($id);

        		if ($usuario->delete() !== false) {
        			$content['result'] = 'success';
        		} else {
        			$content['errors'] = [];
        			foreach ($usuario->getMessages() as $message) {
        				foreach ((array) $message as $m) {
        					$content['errors'][] = $m;
        				}
        				break;
        			}
        		}
        	}
        }

        $this->response->setJsonContent($content);

        $this->response->send();
    }

    public function export ()
    {
        $content = $this->content;
        
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');
        fputcsv($fp, ['Usuario', 'E-mail', 'Nombre', 'Apellido', 'Rol'], ',');

        $sql = "SELECT
            sys_users.nickname,
            sys_users.email,
            sys_users.first_name,
            sys_users.last_name,
            sys_roles.name as role 
            FROM sys_users
            JOIN sys_grants ON sys_grants.user_id = sys_users.id
            JOIN sys_roles ON sys_roles.id = sys_grants.role_id";
        $data = $this->db->query($sql);
        $data->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        $users = $data->fetchAll();
        
        if (count($users) > 0) {
            foreach ($users as $u) {
                fputcsv($fp, [
                    $u['nickname'],
                    $u['email'], 
                    utf8_decode($u['first_name']),
                    utf8_decode($u['last_name']),
                    utf8_decode($u['role'])
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
        $this->response->setHeader('Content-Disposition', 'attachment; filename=Usuarios-' . date('Y-m-d') . '.csv');
        $this->response->setContent($output);
        $this->response->send();
    }

    //get iat y exp
    public function getIatExp ()
    {
        $content = $this->content;
        $token = Auth::getTokenData($this->config);

        $content['result'] = 'success';
        $content['iat'] = $token->iat;
        $content['exp'] = $token->exp;
        
        $this->response->setJsonContent($content);
        $this->response->send();
    }
    */
}