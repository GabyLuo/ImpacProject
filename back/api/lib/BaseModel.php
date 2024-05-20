<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 30/01/2019
 * Time: 05:58 PM
 */

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    protected static $is_top=false;
    public $db;
    public static function getInstance()
    {
        $class_name = static::class;
        return new $class_name();
    }
    public static function findAll($params)
    {
        $di = \Phalcon\DI::getDefault();

        //Logger::log(print_r($params,1));
        static::viewAll($params);

        if(isset($params['conditions'])){

        }
        // getModelsManager  Logger::log()
        return parent::find($params);
    }
    public static function viewAll($params)
    {
        $di = \Phalcon\DI::getDefault();

        $instance = static::getInstance();
        $view_name = "v_" . $instance->getSource();
        $account_id = $di->get('auth_token')->data->account_id;
        //$result = $instance->db->query();
        //$result_set  = $query->execute();
        $sql = "SELECT * FROM {$view_name}";
        $where = " account_id = :account_id: ";
        $bind = ["account_id" => $account_id];
        if(isset($params["conditions"])){
            $where .= " and ( " . $params["conditions"] . " ) ";
            $bind = array_merge($bind, $params["bind"]);
        }
        $sql .= " where {$where} ";

        Logger::log($sql);
        Logger::log($bind);

        $result = $instance->db->query($sql, $bind);
        Logger::log($instance->db->getSQLStatement());

        $rows = $result->fetchAll();

        foreach ($rows as $row) {

            //Logger::log($row);
            //Logger::log($params);
        }

    }
    public function initialize()
    {
        $this->db=$this->getDi()->getShared('db');
    }

}