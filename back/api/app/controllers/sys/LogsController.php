<?php

use Phalcon\Mvc\Controller;

class LogsController extends Controller
{

	public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT sys_logs.log, sys_users.nickname, sys_log_levels.name, sys_logs.fecha
                from sys_logs
                join sys_users on sys_logs.account_id = sys_users.id
                join sys_log_levels on sys_logs.level_id = sys_log_levels.id
                order by sys_logs.id desc limit 1000";

        $logs = $this->content['info_logs'] = $this->db->query($sql)->fetchAll();

        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

                $logs = new Logs();
                $logs->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $logs->account_id = $validUser->user_id;
                $logs->level_id = $request['nivel'];
                if($request['nivel'] == '4')
                $logs->log = 'AUTORIZÓ GASTO CON NÚMERO DE SOLICITUD ' . $request['numero'];
                $logs->fecha = date("Y-m-d H:i:s");
                
                if ($logs->create()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado el log.'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($logs);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el log.'];
                    $tx->rollback();
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}