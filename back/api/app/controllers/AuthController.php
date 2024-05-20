<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 17/09/17
 * Time: 11:42 AM
 */

use Phalcon\Mvc\Controller;
use \Firebase\JWT\JWT;


class AuthController extends Controller
{

    public function login ()
    {
        $content = ['result' => 'error', 'message' => 'error'];
        
        try {
            $request = $this->request->getPost();
            if (isset($request['username']) && isset($request['password'])) {
                $user = Auth::login(strtolower($request['username']), $request['password']);
                $user_id = intval($user->id);
                $status_user = 'INACTIVO';
                $status_user = $user->status;
                if ($user_id > -1) {
                    if ($status_user == 'ACTIVO') {
                        $data = array("user_id" => $user_id, "user_name" => $request['username'], "account_id" => $user->account_id);
                        $token = Auth::createToken($this->request->getHttpHost(), $data, $this->config->jwtkey);
                        $content['result'] = 'success';
                        $content['message'] = 'Logged in';
                        $content['token'] = $token;
                    } else {
                        $content['message'] = 'Usuario inactivo, contacte al Admin del sistema';
                    }
                } else {
                    $content['message'] = 'Usuario '.$request['username'].' o contraseña '.$request['password'].' inválida :'.sha1($request['password']);
                }
            } else {
                $content['message'] = 'Por favor revise los campos.';
            }
        } catch (Exception $e) {
            $content['result'] = 'success';
            $content['message'] = $e->getMessage();
        }
        
        $this->response->setJsonContent($content);
        $this->response->send();
    }

    public function logout()
    {
        $this->session->destroy();
        $this->response->redirect('/');
        $this->response->sendHeaders();
    }
}
