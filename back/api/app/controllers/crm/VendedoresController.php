<?php

use Phalcon\Mvc\Controller;

class VendedoresController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT sys_users.id, sys_users.account_id, sys_users.email, sys_users.nickname, 
        sys_users.created, sys_users.created_by, sys_users.modified, sys_users.modified_by,
        sys_roles.name, sys_grants.role_id as roles, sys_users.status, sys_users.meta_anual, sys_users.gerente_id, sys_users.tipo
         FROM sys_users
        inner join sys_grants on sys_users.id = sys_grants.user_id
        inner join sys_roles on sys_roles.id = sys_grants.role_id and (sys_roles.name='Ventas' or sys_roles.name = 'Coordinador GDP' or sys_roles.name = 'Director')
        order by sys_users.nickname";
        $this->content['vendedores'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getGerentes ()
    {
        $sql = "SELECT sys_users.id, sys_users.account_id, sys_users.email, sys_users.nickname, 
        sys_users.created, sys_users.created_by, sys_users.modified, sys_users.modified_by,
        sys_roles.name, sys_grants.role_id as roles, sys_users.status, sys_users.meta_anual, sys_users.gerente_id, sys_users.tipo
         FROM sys_users
        inner join sys_grants on sys_users.id = sys_grants.user_id
        inner join sys_roles on sys_roles.id = sys_grants.role_id and sys_roles.name='Gerente de ventas'
        order by sys_users.nickname";
        $this->content['gerentes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $vendedor = SysUsers::findFirst($request['id']);
            if ($vendedor) {
                $vendedor->setTransaction($tx);
                // var_dump($request);
                $vendedor->meta_anual = floatval($request['meta_anual']);
                $vendedor->tipo = $request['tipo'];
                if (intval($request['gerente_id']) > 0) {
                    $vendedor->gerente_id = $request['gerente_id'];
                }
                if ($vendedor->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado el vendedor.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($vendedor);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el vendedor'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}