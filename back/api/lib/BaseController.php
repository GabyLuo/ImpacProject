<?php
/**
 * Created by PhpStorm.
 * User: lnunez
 * Date: 23/10/17
 * Time: 10:08 AM
 */

use Phalcon\Mvc\Controller;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class BaseController extends Controller
{
    protected $content = ["code"=>0, "result"=>"error"];

    protected function sendResponse()
    {
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getTableData ($rows_per_page, $page, $sort_by, $descending, $filter, $columns)
    {
        $content = $this->content;
        $params = [];
        if ($sort_by != "null") {
            $params['order'] = $sort_by . ($descending == 'true' ?  ' DESC' : '');
        }
        if ($filter != '_none_') {
            $filter_columns = explode(',', $columns);
            $conditions = [];
            $binds = [];
            foreach ($filter_columns as $column){
                $conditions[] = "{$column} ILIKE :{$column}:";
                $bind[$column] = "%{$filter}%";
            }
            $params['conditions'] = implode(' OR ', $conditions);
            $params['bind'] = $bind;
        }
        $data = $this->find($params);
        $paginator = new PaginatorModel(
            [
                'data'  => $data,
                'limit' => $rows_per_page,
                'page'  => $page,
            ]
        );
        $content['page'] = $paginator->getPaginate();
        $content['result'] = 'success';
        $this->response->setJsonContent($content);
        $this->response->send();
    }


}