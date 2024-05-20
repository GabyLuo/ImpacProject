<?php

use Phalcon\Mvc\Controller;

class SolicitudesOController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $sql = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, 
                fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado, fin_solicitudes.proyecto_id, 
                fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.descripcion, fin_solicitudes.total, fin_solicitudes.total as totalcopia, fin_solicitudes.iva,
                (select pmo_proyecto.nombre 
                from pmo_proyecto 
                where pmo_proyecto.id = fin_solicitudes.proyecto_id) as nombre_proyecto,
                (select sys_users.nickname from sys_users where sys_users.id = fin_solicitudes.creador) 
                as responsable_solicitud
                FROM fin_solicitudes";

        $solicitudes = $this->db->query($sql)->fetchAll();
        $nuevo = [];
            foreach ($solicitudes as $elemento){
                $s=(object)array();
                $s->id = $elemento['id'];
                $s->proyecto_id = $elemento['proyecto_id'];
                $s->status = $elemento['status'];
                $s->fecha_solicitado = $elemento['fecha_solicitado'];
                $s->autorizacion_id = $elemento['autorizacion_id'];
                $s->fecha_creado = $elemento['fecha_creado'];
                $s->creador = $elemento['creador'];
                $s->descripcion = $elemento['descripcion'];
                $s->nombre_proyecto = $elemento['nombre_proyecto'];
                $s->responsable_solicitud = $elemento['responsable_solicitud'];
                $s->total = $elemento['total'];
                $s->totalcopia = $elemento['totalcopia'];
                if(intval($elemento['iva']) === 0){
                    $s->iva = 'NO';
                } else {
                    $s->iva ='SI';
                }           
                array_push($nuevo,$s);
            }

        $this->content['solicitudes'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getById ($id) {

      $sql = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, 
                fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado, fin_solicitudes.proyecto_id, 
                fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.descripcion, fin_solicitudes.total, fin_solicitudes.total as totalcopia, fin_solicitudes.iva,
                (select pmo_proyecto.nombre 
                from pmo_proyecto 
                where pmo_proyecto.id = fin_solicitudes.proyecto_id) as nombre_proyecto,
                (select sys_users.nickname from sys_users where sys_users.id = fin_solicitudes.creador) 
                as responsable_solicitud
                FROM fin_solicitudes where fin_solicitudes.id = $id";

      $solicitud = $this->db->query($sql)->fetchAll();
      $nuevo = [];
        foreach ($solicitud as $elemento){
            $s=(object)array();
            $s->id = $elemento['id'];
            $s->proyecto_id = $elemento['proyecto_id'];
            $s->status = $elemento['status'];
            $s->fecha_solicitado = $elemento['fecha_solicitado'];
            $s->autorizacion_id = $elemento['autorizacion_id'];
            $s->fecha_creado = $elemento['fecha_creado'];
            $s->creador = $elemento['creador'];
            $s->descripcion = $elemento['descripcion'];
            $s->nombre_proyecto = $elemento['nombre_proyecto'];
            $s->responsable_solicitud = $elemento['responsable_solicitud'];
            $s->total = $elemento['total'];
            $s->totalcopia = $elemento['totalcopia'];
            if(intval($elemento['iva']) === 0){
                $s->iva = 'NO';
            } else {
                $s->iva ='SI';
            }           
            array_push($nuevo,$s);
        }

        $this->content['solicitud'] = $nuevo;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id)
    {
        $sql = "SELECT fin_solicitudes.id,fin_solicitudes.status,fin_solicitudes.fecha_solicitado, fin_solicitudes.autorizacion_id,fin_solicitudes.fecha_ejercido,fin_solicitudes.proyecto_id,fin_solicitudes.responsable,
        (select sys_users.nickname from sys_users where fin_solicitudes.responsable = sys_users.id) as nombre
                FROM fin_solicitudes
                where fin_solicitudes.proyecto_id = $proyecto_id";

        $this->content['solicitudes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function validateDate($date, $format = 'Y-m-d') {
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }

    public function filtrar () 
    {
      $tx = $this->transactions->get();
      $request = $this->request->getPost();
      $pagination = $request['pagination'];
      $filter = trim($request['filter']);
      $year = $request['year'];
      $year_anterior = intval($year) - 1;
      $where = "";
      $clasificado_filter = $request['clasificado'];

      if (!empty($filter)) {
        if (is_numeric($filter)) {
          if ($this->validateDate($filter)) {
            $where = " WHERE (fin_solicitudes.fecha_creado = '".$filter."' OR fin_solicitudes.fecha_solicitado = '".$filter."' OR fin_solicitudes.fecha_ejercido = '".$filter."')";
          } else {
            $where = " WHERE fin_solicitudes.id =".intval($filter);
          }
        } else {
          $where = " WHERE (pmo_proyecto.nombre ILIKE '%".$filter."%' OR sys_users.nickname ILIKE '%".$filter."%' or fin_solicitudes.status ILIKE '%".$filter."%')";
        }
      }
      $offset = " OFFSET " . (($pagination['page'] - 1) * $pagination['rowsPerPage']);
      $limit = " LIMIT " . $pagination['rowsPerPage'];

      $proyecto_id = intval($request['proyecto_id']);
      $responsable_id = intval($request['responsable_id']);
      $comprobado = $request['comprobado'];
      $status = $request['status'];
      $proveedor_id = intval($request['proveedor_id']);
      $actividad_id = intval($request['actividad_id']);
      $creador_id = intval($request['creador_id']);
      $factura = $request['factura'];
      $empresa_id = intval($request['empresa_id']);
      if($request['fecha_inicio'] !== '') {
        $fecha_inicio = date('Y-m-d',strtotime($request['fecha_inicio']));
      } else {
        $fecha_inicio = null;
      }
      if($request['fecha_fin'] !== '') {
        $fecha_fin = date('Y-m-d',strtotime($request['fecha_fin']));
      } else {
        $fecha_fin = null;
      }
      if($request['fecha_programada'] !== '') {
        $fecha_programada = date('Y-m-d',strtotime($request['fecha_programada']));
      } else {
        $fecha_programada = date('Y-m-d');
      }
      $fecha_programada = date('Y-m-d');

      $distinct = "";
      if (intval($proveedor_id) !== 0 || intval($actividad_id) !== 0) {
        $distinct = "distinct";
      }

      $validUser = Auth::getUserData($this->config);
      $id = $validUser->user_id;
      $cuenta = $validUser->account_id;
      $correo = $validUser->user_name;
      $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        
      if(strtoupper($role)===strtoupper('root') || $correo === 'andresgonzalez@impactasesores.com' || strtoupper($role)===strtoupper('admin') || strtoupper($role)===strtoupper('finanzas') || strtoupper($role)===strtoupper('finanzas/comisiones') || $correo == 'jmedina@impactasesores.com' || $correo === 'alberto.becerril@impactasesores.com' || $correo === 'kmeza@impactasesores.com') {


        $sql1 = "SELECT {$distinct} fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, TO_CHAR(fin_solicitudes.fecha_solicitado,'DD/MM/YYYY HH24:MI') as f_solicitado,TO_CHAR(fin_solicitudes.fecha_creado,'DD/MM/YYYY HH24:MI') as f_creado,
      fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado,fin_solicitudes.fecha_creado_validacion, fin_solicitudes.fecha_solicitado_validacion, fin_solicitudes.proyecto_id, 
      fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, fin_solicitudes.proveedor_id, fin_solicitudes.banco1, fin_solicitudes.banco2, fin_solicitudes.banco3, fin_solicitudes.banco4, fin_solicitudes.toka, fin_solicitudes.total as totalcopia, fin_solicitudes.iva, fin_solicitudes.fecha_ejercido, fin_solicitudes.empresa_id, fin_solicitudes.subempresa_id,fin_solicitudes.sobrepasa_presupuesto, pmo_proyecto.nombre as nombre_proyecto,sys_users.nickname as responsable_solicitud, fin_solicitudes.concepto_id, fin_solicitudes.subconcepto_id, fin_solicitudes.factura,
      case when exists(select id from fin_gastos where fin_gastos.solicitud_id = fin_solicitudes.id and (fin_gastos.concepto_id is null or fin_gastos.subconcepto_id is null or fin_gastos.concepto_id = 0 or fin_gastos.subconcepto_id = 0)) then 'NO' else 'SI' end as clasificado, (select cmp_compras.folio from cmp_compras where cmp_compras.id = fin_solicitudes.compra_id) as compra_folio, (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = fin_solicitudes.empresa_id), proyecto_comision_id, fin_solicitudes.fecha_programada, TO_CHAR(fin_solicitudes.fecha_programada,'DD-MM-YYYY') as f_programada
      FROM fin_solicitudes 
      inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id and ((pmo_proyecto.year = '$year' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2020' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
      inner join sys_users on sys_users.id = fin_solicitudes.creador and fin_solicitudes.activo = true";

        $sql_colaborador = "SELECT pmo_proyecto.id
                          FROM pmo_proyecto
                          inner join pmo_colaboradores
                          on pmo_proyecto.id = pmo_colaboradores.proyecto_id
                          and pmo_colaboradores.usuario_id = $id";

      if (intval($proveedor_id) !== 0 || intval($actividad_id) !== 0) {
        if(intval($actividad_id) !== 0 && intval($proveedor_id) === 0){
          $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id";
        }
        if(intval($actividad_id) === 0 && intval($proveedor_id) !== 0){
          $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.proveedor_id = $proveedor_id";
        }
        if(intval($actividad_id) !== 0 && intval($proveedor_id) !== 0){
          $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id and fin_gastos.proveedor_id = $proveedor_id";
        }
      } else {
        // $sql1 = $sql1 . " where fin_solicitudes.id > 0 ";
      }
      if(intval($proyecto_id) !== 0){
        $sql1 = $sql1 . " and fin_solicitudes.proyecto_id = $proyecto_id";
      }
      if(intval($creador_id) !== 0){
        $sql1 = $sql1 . " and fin_solicitudes.creador = $creador_id";
      }
      if(intval($responsable_id) !== 0 && intval($proyecto_id) !== 0){
        // $sql = $sql . " and responsable = $responsable_id";
      }
      if(intval($empresa_id) !== 0){
        $sql1 = $sql1 . " and fin_solicitudes.empresa_id = $empresa_id";
      }
      if($status !== 'all'){
        $sql1 = $sql1 . " and fin_solicitudes.status = '$status'";
      }
      if($comprobado === 'SI'){
        $sql1 = $sql1 . " and fin_solicitudes.comprobado = true";
      }
      if($comprobado === 'NO'){
        $sql1 = $sql1 . " and fin_solicitudes.comprobado = false";
      }
      if($factura === 'SI'){
        $sql1 = $sql1 . " and fin_solicitudes.factura = true";
      }
      if($factura === 'NO'){
        $sql1 = $sql1 . " and fin_solicitudes.factura = false";
      }
      if($fecha_inicio !== null && $fecha_fin !== null) {
        $sql1 = $sql1 . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
      }
      if($fecha_programada !== null && ($id ==1 || $id == 39 || strtoupper($role)===strtoupper('finanzas') || strtoupper($role)===strtoupper('finanzas/comisiones'))) {
        // $fecha_siguiente = strtotime ( '-1 days' , strtotime ($fecha_programada));
        $fecha_siguiente = $fecha_programada;

        if ($status == 'all') {
          $sql1 = $sql1 . " and (((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_siguiente' or fin_solicitudes.fecha_programada is null or fin_solicitudes.fecha_programada < '$fecha_siguiente') and fin_solicitudes.status = 'POR AUTORIZAR') or fin_solicitudes.status in ('CREADO', 'CANCELADO', 'RECHAZADO', 'POR PAGAR', 'PAGADO', 'PAGO PARCIAL'))";
        }
        if ($status == 'POR AUTORIZAR') {
          $sql1 = $sql1 . " and ((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_siguiente' or fin_solicitudes.fecha_programada is null) and fin_solicitudes.status = 'POR AUTORIZAR')";
        }
      }

      if ($clasificado_filter != 'all') {
        $sql = "SELECT * FROM (" . $sql1 . $where . " order by id desc" . $offset . $limit . ") as w where clasificado = '$clasificado_filter'";
        $sql_count = "SELECT * FROM (" . $sql1 . $where . " order by id desc" . ") as w where clasificado = '$clasificado_filter'";
      } else {
        $sql = $sql1 . $where . " order by id desc" . $offset . $limit;
        $sql_count = $sql1 . $where . " order by id desc";
      }
      } else {
          $sql1 = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, TO_CHAR(fin_solicitudes.fecha_solicitado,'DD-MM-YYYY HH24:MI') as f_solicitado,
            fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado,TO_CHAR(fin_solicitudes.fecha_creado,'DD-MM-YYYY HH24:MI') as f_creado,fin_solicitudes.fecha_creado_validacion, fin_solicitudes.fecha_solicitado_validacion, fin_solicitudes.proyecto_id, 
            fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, fin_solicitudes.proveedor_id, fin_solicitudes.banco1, fin_solicitudes.banco2, fin_solicitudes.banco3, fin_solicitudes.banco4, fin_solicitudes.toka,
            fin_solicitudes.total as totalcopia, fin_solicitudes.iva, fin_solicitudes.fecha_ejercido, fin_solicitudes.empresa_id,fin_solicitudes.subempresa_id,fin_solicitudes.sobrepasa_presupuesto, pmo_proyecto.nombre as nombre_proyecto, sys_users.nickname as responsable_solicitud, fin_solicitudes.concepto_id, fin_solicitudes.subconcepto_id, fin_solicitudes.factura,
            case when exists(select id from fin_gastos where fin_gastos.solicitud_id = fin_solicitudes.id and (fin_gastos.concepto_id is null or fin_gastos.subconcepto_id is null or fin_gastos.concepto_id = 0 or fin_gastos.subconcepto_id = 0)) then 'NO' else 'SI' end as clasificado,  (select cmp_compras.folio from cmp_compras where cmp_compras.id = fin_solicitudes.compra_id) as compra_folio, (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = fin_solicitudes.empresa_id), proyecto_comision_id, fin_solicitudes.fecha_programada,TO_CHAR(fin_solicitudes.fecha_programada,'DD-MM-YYYY') as f_programada
            FROM fin_solicitudes 
            inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id and ((pmo_proyecto.year = '$year' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2020' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
            inner join sys_users on sys_users.id = fin_solicitudes.creador

            inner join pmo_solicitantes on fin_solicitudes.proyecto_id = pmo_solicitantes.proyecto_id
            and pmo_solicitantes.usuario_id = $id and fin_solicitudes.activo = true ";
      
        $union = " union ";
      
        $sql2 = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, TO_CHAR(fin_solicitudes.fecha_solicitado,'DD-MM-YYYY HH24:MI') as f_solicitado,
          fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado,TO_CHAR(fin_solicitudes.fecha_creado,'DD-MM-YYYY HH24:MI') as f_creado,fin_solicitudes.fecha_creado_validacion, fin_solicitudes.fecha_solicitado_validacion, fin_solicitudes.proyecto_id, 
          fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, fin_solicitudes.proveedor_id, fin_solicitudes.banco1, fin_solicitudes.banco2, fin_solicitudes.banco3, fin_solicitudes.banco4, fin_solicitudes.toka,
          fin_solicitudes.total as totalcopia, fin_solicitudes.iva, fin_solicitudes.fecha_ejercido, fin_solicitudes.empresa_id,fin_solicitudes.subempresa_id,fin_solicitudes.sobrepasa_presupuesto,
          pmo_proyecto.nombre as nombre_proyecto,sys_users.nickname as responsable_solicitud, fin_solicitudes.concepto_id, fin_solicitudes.subconcepto_id, fin_solicitudes.factura,
          case when exists(select id from fin_gastos where fin_gastos.solicitud_id = fin_solicitudes.id and (fin_gastos.concepto_id is null or fin_gastos.subconcepto_id is null or fin_gastos.concepto_id = 0 or fin_gastos.subconcepto_id = 0)) then 'NO' else 'SI' end as clasificado,  (select cmp_compras.folio from cmp_compras where cmp_compras.id = fin_solicitudes.compra_id) as compra_folio, (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = fin_solicitudes.empresa_id), proyecto_comision_id, fin_solicitudes.fecha_programada, TO_CHAR(fin_solicitudes.fecha_programada,'DD-MM-YYYY') as f_programada
          FROM fin_solicitudes
          inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id and ((pmo_proyecto.year = '$year' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2020' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
          inner join sys_users on sys_users.id = fin_solicitudes.creador
          inner join pmo_colaboradores on fin_solicitudes.proyecto_id = pmo_colaboradores.proyecto_id
          and pmo_colaboradores.usuario_id = $id and fin_solicitudes.activo = true ";

        $sql3 = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, TO_CHAR(fin_solicitudes.fecha_solicitado,'DD-MM-YYYY HH24:MI') as f_solicitado,
          fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado,TO_CHAR(fin_solicitudes.fecha_creado,'DD-MM-YYYY HH24:MI') as f_creado,fin_solicitudes.fecha_creado_validacion, fin_solicitudes.fecha_solicitado_validacion, fin_solicitudes.proyecto_id, 
          fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, fin_solicitudes.proveedor_id, fin_solicitudes.banco1, fin_solicitudes.banco2, fin_solicitudes.banco3, fin_solicitudes.banco4, fin_solicitudes.toka,
          fin_solicitudes.total as totalcopia, fin_solicitudes.iva, fin_solicitudes.fecha_ejercido, fin_solicitudes.empresa_id,fin_solicitudes.subempresa_id,fin_solicitudes.sobrepasa_presupuesto, pmo_proyecto.nombre as nombre_proyecto,sys_users.nickname as responsable_solicitud, fin_solicitudes.concepto_id, fin_solicitudes.subconcepto_id, fin_solicitudes.factura,
          case when exists(select id from fin_gastos where fin_gastos.solicitud_id = fin_solicitudes.id and (fin_gastos.concepto_id is null or fin_gastos.subconcepto_id is null or fin_gastos.concepto_id = 0 or fin_gastos.subconcepto_id = 0)) then 'NO' else 'SI' end as clasificado,  (select cmp_compras.folio from cmp_compras where cmp_compras.id = fin_solicitudes.compra_id) as compra_folio, (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = fin_solicitudes.empresa_id), proyecto_comision_id, fin_solicitudes.fecha_programada,TO_CHAR(fin_solicitudes.fecha_programada,'DD-MM-YYYY') as f_programada
          FROM fin_solicitudes
          inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id and ((pmo_proyecto.year = '$year' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2020' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
          inner join sys_users on sys_users.id = fin_solicitudes.creador 
          inner join pmo_autorizadores on fin_solicitudes.proyecto_id = pmo_autorizadores.proyecto_id
          and pmo_autorizadores.usuario_id = $id and fin_solicitudes.activo = true ";

        $sql4 = "SELECT fin_solicitudes.id, fin_solicitudes.status, fin_solicitudes.fecha_solicitado, TO_CHAR(fin_solicitudes.fecha_solicitado,'DD-MM-YYYY HH24:MI') as f_solicitado,
          fin_solicitudes.autorizacion_id, fin_solicitudes.fecha_creado,TO_CHAR(fin_solicitudes.fecha_creado,'DD-MM-YYYY HH24:MI') as f_creado,fin_solicitudes.fecha_creado_validacion, fin_solicitudes.fecha_solicitado_validacion, fin_solicitudes.proyecto_id, 
          fin_solicitudes.creador, fin_solicitudes.comprobado, fin_solicitudes.total, fin_solicitudes.proveedor_id, fin_solicitudes.banco1, fin_solicitudes.banco2, fin_solicitudes.banco3, fin_solicitudes.banco4, fin_solicitudes.toka,
          fin_solicitudes.total as totalcopia, fin_solicitudes.iva, fin_solicitudes.fecha_ejercido, fin_solicitudes.empresa_id,fin_solicitudes.subempresa_id,fin_solicitudes.sobrepasa_presupuesto,
          pmo_proyecto.nombre as nombre_proyecto,sys_users.nickname as responsable_solicitud, fin_solicitudes.concepto_id, fin_solicitudes.subconcepto_id, fin_solicitudes.factura,
          case when exists(select id from fin_gastos where fin_gastos.solicitud_id = fin_solicitudes.id and (fin_gastos.concepto_id is null or fin_gastos.subconcepto_id is null or fin_gastos.concepto_id = 0 or fin_gastos.subconcepto_id = 0)) then 'NO' else 'SI' end as clasificado, (select cmp_compras.folio from cmp_compras where cmp_compras.id = fin_solicitudes.compra_id) as compra_folio, (select vnt_empresa.razon_social as empresa from vnt_empresa where vnt_empresa.id = fin_solicitudes.empresa_id), proyecto_comision_id, fin_solicitudes.fecha_programada,TO_CHAR(fin_solicitudes.fecha_programada,'DD-MM-YYYY') as f_programada
          FROM fin_solicitudes 
          inner join pmo_proyecto on pmo_proyecto.id = fin_solicitudes.proyecto_id and ((pmo_proyecto.year = '$year' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '$year_anterior' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2020' and date_part('year', fin_solicitudes.fecha_creado) = '$year') or (pmo_proyecto.year = '2021' and date_part('year', fin_solicitudes.fecha_creado) = '$year'))
          inner join sys_users on sys_users.id = fin_solicitudes.creador
          inner join pmo_responsable_pagos on fin_solicitudes.proyecto_id = pmo_responsable_pagos.proyecto_id
          and pmo_responsable_pagos.usuario_id = $id and fin_solicitudes.activo = true ";

        if (intval($proveedor_id) !== 0 || intval($actividad_id) !== 0) {
          if(intval($actividad_id) !== 0 && intval($proveedor_id) === 0){
            $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id";
            $sql2 = $sql2 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id";
            $sql3 = $sql3 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id";
            $sql4 = $sql4 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id";
          }
          if(intval($actividad_id) === 0 && intval($proveedor_id) !== 0){
            $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql2 = $sql2 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql3 = $sql3 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql4 = $sql4 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.proveedor_id = $proveedor_id";
          }
          if(intval($actividad_id) !== 0 && intval($proveedor_id) !== 0){
            $sql1 = $sql1 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql2 = $sql2 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql3 = $sql3 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id and fin_gastos.proveedor_id = $proveedor_id";
            $sql4 = $sql4 . " inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $actividad_id and fin_gastos.proveedor_id = $proveedor_id";
          }
        } else {
          if(intval($actividad_id) !== 0){
            $sql1 = $sql1 . " and fin_gastos.actividad_id = $actividad_id";
            $sql2 = $sql2 . " and fin_gastos.actividad_id = $actividad_id";
            $sql3 = $sql3 . " and fin_gastos.actividad_id = $actividad_id";
            $sql4 = $sql4 . " and fin_gastos.actividad_id = $actividad_id";
          }
          if(intval($proyecto_id) !== 0){
            $sql1 = $sql1 . " and fin_solicitudes.proyecto_id = $proyecto_id";
            $sql2 = $sql2 . " and fin_solicitudes.proyecto_id = $proyecto_id";
            $sql3 = $sql3 . " and fin_solicitudes.proyecto_id = $proyecto_id";
            $sql4 = $sql4 . " and fin_solicitudes.proyecto_id = $proyecto_id";
          }
          if(intval($proveedor_id) !== 0){
            $sql1 = $sql1 . " and fin_gastos.proveedor_id = $proveedor_id";
            $sql2 = $sql2 . " and fin_gastos.proveedor_id = $proveedor_id";
            $sql3 = $sql3 . " and fin_gastos.proveedor_id = $proveedor_id";
            $sql4 = $sql4 . " and fin_gastos.proveedor_id = $proveedor_id";
          }
          if(intval($creador_id) !== 0){
            $sql1 = $sql1 . " and fin_solicitudes.creador = $creador_id";
            $sql2 = $sql2 . " and fin_solicitudes.creador = $creador_id";
            $sql3 = $sql3 . " and fin_solicitudes.creador = $creador_id";
            $sql4 = $sql4 . " and fin_solicitudes.creador = $creador_id";
          }
          if(intval($responsable_id) !== 0 && intval($proyecto_id) !== 0){
            // $sql = $sql . " and responsable = $responsable_id";
          }
          if($status !== 'all'){
            $sql1 = $sql1 . " and fin_solicitudes.status = '$status'";
            $sql2 = $sql2 . " and fin_solicitudes.status = '$status'";
            $sql3 = $sql3 . " and fin_solicitudes.status = '$status'";
            $sql4 = $sql4 . " and fin_solicitudes.status = '$status'";
          }
          if($comprobado === 'SI'){
            $sql1 = $sql1 . " and fin_solicitudes.comprobado = true";
            $sql2 = $sql2 . " and fin_solicitudes.comprobado = true";
            $sql3 = $sql3 . " and fin_solicitudes.comprobado = true";
            $sql4 = $sql4 . " and fin_solicitudes.comprobado = true";
          }
          if($comprobado === 'NO'){
            $sql1 = $sql1 . " and fin_solicitudes.comprobado = false";
            $sql2 = $sql2 . " and fin_solicitudes.comprobado = false";
            $sql3 = $sql3 . " and fin_solicitudes.comprobado = false";
            $sql4 = $sql4 . " and fin_solicitudes.comprobado = false";
          }
          if($factura === 'SI'){
            $sql1 = $sql1 . " and fin_solicitudes.factura = true";
            $sql2 = $sql2 . " and fin_solicitudes.factura = true";
            $sql3 = $sql3 . " and fin_solicitudes.factura = true";
            $sql4 = $sql4 . " and fin_solicitudes.factura = true";
          }
          if($factura === 'NO'){
            $sql1 = $sql1 . " and fin_solicitudes.factura = false";
            $sql2 = $sql2 . " and fin_solicitudes.factura = false";
            $sql3 = $sql3 . " and fin_solicitudes.factura = false";
            $sql4 = $sql4 . " and fin_solicitudes.factura = false";
          }
          if(intval($empresa_id) !== 0) {
            $sql1 = $sql1 . " and fin_solicitudes.empresa_id = $empresa_id";
            $sql2 = $sql2 . " and fin_solicitudes.empresa_id = $empresa_id";
            $sql3 = $sql3 . " and fin_solicitudes.empresa_id = $empresa_id";
            $sql4 = $sql4 . " and fin_solicitudes.empresa_id = $empresa_id";
          }
          if($fecha_inicio !== null && $fecha_fin !== null) {
            $sql1 = $sql1 . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
            $sql2 = $sql2 . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
            $sql3 = $sql3 . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
            $sql4 = $sql4 . " and fin_solicitudes.fecha_creado BETWEEN '$fecha_inicio' and '$fecha_fin 23:59:59'";
          }
          if($fecha_programada !== null && ($id == 1)) {
            if ($status == 'all') {
              $sql1 = $sql1 . " and ((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR') or fin_solicitudes.status in ('CREADO', 'CANCELADO', 'RECHAZADO', 'POR PAGAR', 'PAGADO', 'PAGO PARCIAL'))";
              $sql2 = $sql2 . " and ((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR') or fin_solicitudes.status in ('CREADO', 'CANCELADO', 'RECHAZADO', 'POR PAGAR', 'PAGADO', 'PAGO PARCIAL'))";
              $sql3 = $sql3 . " and ((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR') or fin_solicitudes.status in ('CREADO', 'CANCELADO', 'RECHAZADO', 'POR PAGAR', 'PAGADO', 'PAGO PARCIAL'))";
              $sql4 = $sql4 . " and ((TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR') or fin_solicitudes.status in ('CREADO', 'CANCELADO', 'RECHAZADO', 'POR PAGAR', 'PAGADO', 'PAGO PARCIAL'))";
            }
            if ($status == 'POR AUTORIZAR') {
              $sql1 = $sql1 . " and (TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR')";
              $sql2 = $sql2 . " and (TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR')";
              $sql3 = $sql3 . " and (TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR')";
              $sql4 = $sql4 . " and (TO_CHAR(fin_solicitudes.fecha_programada,'YYYY-MM-DD') = '$fecha_programada' and fin_solicitudes.status = 'POR AUTORIZAR')";
            }
          }
        }

        /* if ($clasificado_filter != 'all') {
          $sql = "SELECT * FROM (" . $sql1 . $where . $union . $sql2 . $where . $union . $sql3 . $where . $union . $sql4 . $where . " order by id desc" . $offset . $limit . ") as w where clasificado = '$clasificado_filter'";
          $sql_count = "SELECT * FROM (" . $sql1 . $where . $union . $sql2 . $where . $union . $sql3 . $where . $union . $sql4 . $where . " order by id desc" . ") as w where clasificado = '$clasificado_filter'";
        } else { */ 
          $sql = $sql1 . $where . $union . $sql2 . $where . " order by id desc" . $offset . $limit;
          $sql_count = $sql1 . $where . $union . $sql2 . $where . " order by id desc";
        // }
      }

        $sql_colaborador = "SELECT pmo_proyecto.id
                            FROM pmo_proyecto
                            inner join pmo_colaboradores
                            on pmo_proyecto.id = pmo_colaboradores.proyecto_id
                            and pmo_colaboradores.usuario_id = $id";

        $sql_solicitante = "SELECT pmo_proyecto.id
                            FROM pmo_proyecto
                            inner join pmo_solicitantes 
                            on pmo_proyecto.id = pmo_solicitantes.proyecto_id
                            and pmo_solicitantes.usuario_id = $id";

        $sql_autorizador = "SELECT pmo_proyecto.id
                            FROM pmo_proyecto
                            inner join pmo_autorizadores
                            on pmo_proyecto.id = pmo_autorizadores.proyecto_id
                            and pmo_autorizadores.usuario_id = $id";

        $sql_responsable = "SELECT pmo_proyecto.id
                            FROM pmo_proyecto
                            inner join pmo_responsable_pagos
                            on pmo_proyecto.id = pmo_responsable_pagos.proyecto_id
                            and pmo_responsable_pagos.usuario_id = $id";

        $solicitudes = $this->db->query($sql)->fetchAll();
        $solicitudes_count = count($this->db->query($sql_count)->fetchAll());
        $colaboradores = $this->db->query($sql_colaborador)->fetchAll();
        $solicitantes = $this->db->query($sql_solicitante)->fetchAll();
        $autorizadores = $this->db->query($sql_autorizador)->fetchAll();
        $responsables = $this->db->query($sql_responsable)->fetchAll();
        $nuevo = [];
          foreach ($solicitudes as $elemento){
              $proyecto = intval($elemento['proyecto_id']);
              $tam_colaborador = sizeof($colaboradores);
              $tam_solicitante = sizeof($solicitantes);
              $tam_autorizador = sizeof($autorizadores);
              $tam_responsables = sizeof($responsables);

              $s=(object)array();
              $s->id = $elemento['id'];
              if ($this->perfil($proyecto,$tam_colaborador,$colaboradores)) {
                  $s->colaborador = true;
              } else {
                  $s->colaborador = false;
              }
              if ($this->perfil($proyecto,$tam_solicitante,$solicitantes)) {
                  $s->solicitante = true;
              } else {
                  $s->solicitante = false;
              }
              if ($this->perfil($proyecto,$tam_autorizador,$autorizadores)) {
                  $s->autorizador = true;
                  $sql_orden = "SELECT pmo_proyecto.id, pmo_autorizadores.orden, pmo_autorizadores.id as autorizador_id, pmo_autorizadores.alteracion
                                FROM pmo_proyecto
                                inner join pmo_autorizadores
                                on pmo_proyecto.id = pmo_autorizadores.proyecto_id
                                and pmo_autorizadores.usuario_id = $id
                                and pmo_proyecto.id = $proyecto";
                  $resultado = $this->db->query($sql_orden)->fetchAll();
                  if(sizeof($resultado) > 0) {
                      $s->orden = $resultado[0]['orden'];
                      $s->autorizador_id = $resultado[0]['autorizador_id'];
                      if(intval($resultado[0]['alteracion']) === 0){
                        $s->alteracion = 'NO';
                      } else {
                        $s->alteracion ='SI';
                      } 
                  }
              } else {
                  $s->autorizador = false;
              }
              if ($this->perfil($proyecto,$tam_responsables,$responsables)) {
                  $s->pagos = true;
              } else {
                  if ($validUser->user_id == 29 || $validUser->user_id == 39 || $validUser->user_id == 108 || $validUser->user_id == 119) {
                    $s->pagos = true;
                  } else {
                    $s->pagos = false;
                  }
                  if (strtoupper($role)===strtoupper('finanzas') || strtoupper($role)===strtoupper('finanzas/comisiones')) {
                    $s->pagos = true;
                    // $s->colaborador = true;
                    // $s->solicitante = true;
                  }
              }
              $s->proyecto_id = $elemento['proyecto_id'];
              $s->status = $elemento['status'];
              $s->fecha_solicitado = $elemento['fecha_solicitado'];
               $s->f_solicitado = $elemento['f_solicitado'];
              $s->autorizacion_id = $elemento['autorizacion_id'];
              $s->fecha_creado = $elemento['fecha_creado'];
               $s->f_creado = $elemento['f_creado'];
              $s->fecha_creado_validacion = $elemento['fecha_creado_validacion'];
              $s->fecha_solicitado_validacion = $elemento['fecha_solicitado_validacion'];
              $s->creador = $elemento['creador'];
              $s->creador_nombre = $elemento['responsable_solicitud'];
              $s->nombre_proyecto = $elemento['nombre_proyecto'];
              $s->empresa_id = $elemento['empresa_id'];
              $s->subempresa_id = $elemento['subempresa_id'];
              $s->proveedor_id = $elemento['proveedor_id'];
              $s->proyecto_comision_id = $elemento['proyecto_comision_id'];
              $s->banco1 = $elemento['banco1'];
              $s->banco2 = $elemento['banco2'];
              $s->banco3 = $elemento['banco3'];
              $s->banco4 = $elemento['banco4'];
              $s->toka = $elemento['toka'];
              if ($s->status === 'CREADO'){
                $s->responsable_solicitud = 'LÍDER';
              }
              if ($s->status === 'RECHAZADO'){
                $s->responsable_solicitud = 'LÍDER';
              }
              if ($s->status === 'POR AUTORIZAR'){
                if (intval($s->autorizacion_id) === 0) {
                  $s->responsable_solicitud = '';
                }
                if (intval($s->autorizacion_id) > 0) {
                  $p = $s->proyecto_id;
                  $a = $s->autorizacion_id;
                  $sql_responsable = "SELECT sys_users.nickname from sys_users
                  inner join pmo_autorizadores on pmo_autorizadores.usuario_id = sys_users.id
                  and pmo_autorizadores.proyecto_id = $p and pmo_autorizadores.orden = $a";
                  $resp = $this->db->query($sql_responsable)->fetchAll();
                  if (sizeof($resp)>0) {
                    $s->responsable_solicitud = $resp[0]['nickname'];
                  } else {
                    $s->responsable_solicitud = 'SIN AUTORIZADOR';
                  }
                }
              }
              $s->total = $elemento['total'];
              $s->totalcopia = $elemento['totalcopia'];
              $s->fecha_ejercido = $elemento['fecha_ejercido'];
              if(intval($elemento['iva']) === 0){
                  $s->iva = 'NO';
                  $s->columna_total = number_format(floatval($elemento['total']),2,'.',',');
              } else {
                  $s->iva ='SI';
                  $s->columna_total = number_format((floatval($elemento['total']) + (floatval($elemento['total']) * 0.16)),2,'.',',');
              }
              if(intval($elemento['comprobado']) === 0){
                  $s->comprobado = 'NO';
              } else {
                  $s->comprobado ='SI';
              }
              $s->concepto_id = $elemento['concepto_id'];
              $s->subconcepto_id = $elemento['subconcepto_id'];
              if(intval($elemento['factura']) === 0) {
                  $s->factura = 'NO';
              } else {
                  $s->factura = 'SI';
              }
              
              $s->sobrepasa_presupuesto = $elemento['sobrepasa_presupuesto'];
              if ($s->status === 'CREADO') {
                $clasificado = 'NO';
              } else {
                $clasificado = $elemento['clasificado'];
              }
              if ($clasificado == 'NO') {
                $s->color_clasificado = 'red-10';
                $s->icon_clasificado = 'fas fa-times';
              } else {
                $s->color_clasificado = 'green-9';
                $s->icon_clasificado = 'fas fa-check';
              }
              $s->compra_folio = $elemento['compra_folio'];
              $s->empresa = $elemento['empresa'];
              $s->fecha_programada = $elemento['fecha_programada'];
              $s->f_programada = $elemento['f_programada'];
              array_push($nuevo,$s);
          }

          if (sizeof($colaboradores) > 0) {
              $this->content['perfil_colaborador'] = 'si';
          } else {
              $this->content['perfil_colaborador'] = 'no';
          }

      $this->content['solicitudes'] = $nuevo;
      $this->content['solicitudes_count'] = $solicitudes_count;
      $this->content['result'] = 'success';
      $this->response->setJsonContent($this->content);
      $this->response->send();
    }

    private function perfil ($numero, $longitud, $arreglo) {
        for ($i=0; $i<$longitud; $i++) {
            if ($numero === $arreglo[$i]['id']){
                return true;
            }
        }
        return false;
    }

    private function el_siguiente ($numero, $longitud, $arreglo) 
    {
        for ($i=0; $i<$longitud; $i++) {
            if ($numero == $arreglo[$i]['orden']) {
                if ($longitud-1 == $i) {
                    return 0;
                }
                if ($i < $longitud-2) {
                    return $arreglo[$i+1]['orden'];
                    //return $arreglo[$i+1];
                }
            }
        }
    }

    public function obtenerSigSolicitud () {
        $sql = "SELECT max(id) FROM fin_solicitudes";
        
        $this->content['solicitudes'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $id = 0;

                $solicitudes = new SolicitudesO();
                $solicitudes->setTransaction($tx);
                $solicitudes->proyecto_id = intval($request['proyecto_id']);
                $solicitudes->status = 'CREADO';
                $solicitudes->fecha_creado = date("Y-m-d H:i:s");
                $solicitudes->fecha_creado_validacion = date("Y-m-d H:i:s");
                $validUser = Auth::getUserData($this->config);
                $user_id = $validUser->user_id;
                $solicitudes->creador = $validUser->user_id;
                $solicitudes->autorizacion_id = null;
                $solicitudes->total = floatval($request['total']);
                $solicitudes->empresa_id = intval($request['empresa_id']);
                $solicitudes->compra_id = intval($request['compra_id']) > 0 ? intval($request['compra_id']) : null;
                $solicitudes->fecha_programada = null;
                // if($request['iva'] === 'SI'){
                    $solicitudes->iva = true;
                    $solicitudes->factura = true;
                /* } else {
                    $solicitudes->iva = false;
                    $solicitudes->factura = false;
                } */
                $solicitudes->proveedor_id = intval($request['proveedor_id']);
                if($request['banco1'] === 'SI'){
                    $solicitudes->banco1 = 'SI';
                }
                if($request['banco2'] === 'SI'){
                    $solicitudes->banco2 = 'SI';
                }
                if($request['banco3'] === 'SI'){
                    $solicitudes->banco3 = 'SI';
                }
                if($request['banco4'] === 'SI'){
                    $solicitudes->banco4 = 'SI';
                }
                if($request['tarjeta1'] === 'SI'){
                    $solicitudes->tarjeta1 = 'SI';
                }
                if($request['tarjeta2'] === 'SI'){
                    $solicitudes->tarjeta2 = 'SI';
                }
                if($request['tarjeta3'] === 'SI'){
                    $solicitudes->tarjeta3 = 'SI';
                }
                if($request['tarjeta4'] === 'SI'){
                    $solicitudes->tarjeta4 = 'SI';
                }
                if($request['toka'] === 'SI'){
                    $solicitudes->toka = 'SI';
                }

                if ($solicitudes->create()) {
                    $id = $solicitudes->id;
                    $proyecto_id = $solicitudes->proyecto_id;
                    $sql_solicitante = "SELECT pmo_proyecto.id,pmo_proyecto.nombre from pmo_proyecto inner join pmo_solicitantes on pmo_proyecto.id = pmo_solicitantes.proyecto_id and pmo_solicitantes.usuario_id = $user_id and pmo_proyecto.id = $proyecto_id";
                    $es_solicitante = $this->db->query($sql_solicitante)->fetchAll();
                    if (count($es_solicitante) > 0) {
                        $this->content['solicitante'] = true;
                    } else {
                        $this->content['solicitante'] = false;
                    }
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha creado la solicitud'];
                    $this->content['id'] = $id;
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitudes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la solicitud.'];
                    $tx->rollback();
                }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function rechazar () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
            if ($solicitud) {
                $mensajes= new Mensajes();
                $mensajes->setTransaction($tx);
                $validUser = Auth::getUserData($this->config);
                $mensajes->autorizador_id = $validUser->user_id;
                $mensajes->solicitud_id=$request['solicitud_id'];
                $mensajes->mensaje=trim($request['mensaje']);
                $mensajes->user_destinatario=0;
                $mensajes->fecha_mensaje = date("Y-m-d H:i:s");
                
                if ($mensajes->create()) {
                    $solicitud->setTransaction($tx);
                    $solicitud->status = 'RECHAZADO';
                    $solicitud->autorizacion_id = null;
                    if($solicitud->update()){
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha rechazado la solicitud.'];
                        $tx->commit();
                    } else {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha guardado el mensaje pero no se ha podido rechazar la solicitud.'];
                    $tx->commit();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($mensajes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el mensaje.'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La solicitud ya no está disponible.'];
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function cancelar () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            $solicitud = SolicitudesO::findFirst($request['solicitud_id']);
            if ($solicitud) {
                    $solicitud->setTransaction($tx);
                    $solicitud->status = 'CANCELADO';
                    $solicitud->autorizacion_id = -2;
                    if($solicitud->update()){
                        $logs = new Logs();
                        $logs->setTransaction($tx);
                        $validUser = Auth::getUserData($this->config);
                        $logs->account_id = $validUser->user_id;
                        $logs->level_id = 6;
                        $logs->log = 'CANCELÓ SOLICITUD #' . $request['solicitud_id'];
                        $logs->fecha = date("Y-m-d H:i:s");
                        if($logs->create()){
                          $this->content['result'] = 'success';
                          $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha cancelado la solicitud.'];
                          $tx->commit();
                        } else {
                          $this->content['result'] = 'success';
                          $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha cancelado la solicitud.'];
                          $tx->commit();
                        }
                    } else {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo cancelar la solicitud.'];
                    $tx->commit();
                    }
            } else {
                $this->content['result'] = 'success';
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La solicitud ya no está disponible.'];
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

                $validUser = Auth::getUserData($this->config);
                $id = $validUser->user_id;
                $solicitudes = SolicitudesO::findFirst($request['id']);
                if ($solicitudes) {
                // var_dump($request);
                $solicitudes->setTransaction($tx);
                // este campo -> autorizacion_id cambiará pero no sé como xD
                // 
                $status_anterior = $solicitudes->status;
                $solicitudes->status = $request['status'];

                $solicitudes->proyecto_id = intval($request['proyecto_id']);
                $solicitudes->proyecto_comision_id = intval($request['proyecto_comision_id']);

                if(($request['status'] === 'POR AUTORIZAR' && $status_anterior === 'CREADO') || ($request['status'] === 'POR AUTORIZAR' && $status_anterior === 'RECHAZADO')) {
                    $solicitudes->fecha_solicitado = date("Y-m-d H:i:s");
                    $solicitudes->fecha_solicitado_validacion = date("Y-m-d H:i:s");
                    $solicitudes->autorizacion_id = 1;
                } 
                if($request['status'] === 'CANCELADO' || $request['status'] === 'RECHAZADO') {
                    $solicitudes->fecha_solicitado = null;
                }
                if($request['iva'] === 'SI'){
                    $solicitudes->iva = true;
                    $solicitudes->factura = true;
                } else {
                    $solicitudes->iva = false;
                    $solicitudes->factura = false;
                }
                if($request['comprobado'] === 'SI'){
                    $solicitudes->comprobado = true;
                } else {
                    $solicitudes->comprobado = false;
                }
                if($request['fecha_ejercido'] !== ''){
                  $solicitudes->fecha_ejercido = date("Y/m/d", strtotime($request['fecha_ejercido']));
                  $solicitudes->status = 'PAGADO';
                }
                if($request['fecha_programada'] !== ''){
                  $solicitudes->fecha_programada = date("Y/m/d", strtotime($request['fecha_programada']));
                }

                if ($solicitudes->update()) {
                  $proyecto_id = $solicitudes->proyecto_id;
                  //
                  if($request['status'] === 'POR AUTORIZAR' && $status_anterior === 'CREADO') {
                    $logs = new Logs();
                    $logs->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $logs->account_id = $validUser->user_id;
                    $logs->level_id = 5;
                    $logs->log = 'CAMBIÓ SOLICITUD #' . $request['id'].' DE ESTATUS CREADO A POR AUTORIZAR';
                    $logs->fecha = date("Y-m-d H:i:s");
                    if($logs->create()){
                      if($request['sobrepasa_presupuesto'] === 'SI') {
                        $notas= new Notas();
                        $notas->setTransaction($tx);
                        $notas->usuario_id = $validUser->user_id;
                        $notas->solicitud_id=$solicitudes->id;
                        $notas->nota=trim($request['nota']);
                        $notas->fecha = date("Y-m-d H:i:s");
                        $notas->perfil = 'SOLICITANTE';
                        if($notas->create()) {
                          //
                          $sql_autorizador = "SELECT pmo_proyecto.id
                            FROM pmo_proyecto
                            inner join pmo_autorizadores
                            on pmo_proyecto.id = pmo_autorizadores.proyecto_id
                            and pmo_autorizadores.usuario_id = $id";
                          $autorizadores = $this->db->query($sql_autorizador)->fetchAll();
                          $proyecto = $proyecto_id;
                          $tam_autorizador = sizeof($autorizadores);
                          if ($this->perfil($proyecto,$tam_autorizador,$autorizadores)) {
                            $autorizador = true;
                            $sql_orden = "SELECT pmo_proyecto.id, pmo_autorizadores.orden, pmo_autorizadores.id as autorizador_id, pmo_autorizadores.alteracion FROM pmo_proyecto inner join pmo_autorizadores
                              on pmo_proyecto.id = pmo_autorizadores.proyecto_id and pmo_autorizadores.usuario_id = $id
                              and pmo_proyecto.id = $proyecto";
                            $resultado = $this->db->query($sql_orden)->fetchAll();
                            if(sizeof($resultado) > 0) {
                              $orden = $resultado[0]['orden'];
                              $autorizador_id = $resultado[0]['autorizador_id'];
                              if(intval($resultado[0]['alteracion']) === 0){
                                $alteracion = 'NO';
                              } else {
                                $alteracion ='SI';
                              } 
                            }
                            ///
                            $this->content['autorizador'] = $autorizador;
                            $this->content['orden'] = $orden;
                            $this->content['autorizador_id'] = $autorizador_id;
                            $this->content['alteracion'] = $alteracion;
                            ///
                          } else {
                            $autorizador = false;
                            $this->content['autorizador'] = $autorizador;
                          }
                          //
                          $this->content['result'] = 'success';
                          $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la solicitud'];
                          $this->content['status'] = $solicitudes->status;
                          $tx->commit();
                        } else {
                          $this->content['error'] = Helpers::getErrors($notas);
                          $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                          $tx->rollback();
                        }
                      } else {
                        //
                        $sql_autorizador = "SELECT pmo_proyecto.id FROM pmo_proyecto inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id and pmo_autorizadores.usuario_id = $id";
                        $autorizadores = $this->db->query($sql_autorizador)->fetchAll();
                        $proyecto = $proyecto_id;
                        $tam_autorizador = sizeof($autorizadores);
                        if ($this->perfil($proyecto,$tam_autorizador,$autorizadores)) {
                          $autorizador = true;
                          $sql_orden = "SELECT pmo_proyecto.id, pmo_autorizadores.orden, pmo_autorizadores.id as autorizador_id, pmo_autorizadores.alteracion FROM pmo_proyecto inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id and pmo_autorizadores.usuario_id = $id and pmo_proyecto.id = $proyecto";
                          $resultado = $this->db->query($sql_orden)->fetchAll();
                          if(sizeof($resultado) > 0) {
                            $orden = $resultado[0]['orden'];
                            $autorizador_id = $resultado[0]['autorizador_id'];
                            if(intval($resultado[0]['alteracion']) === 0){
                              $alteracion = 'NO';
                            } else {
                              $alteracion ='SI';
                            } 
                          }
                          ///
                          $this->content['autorizador'] = $autorizador;
                          $this->content['orden'] = $orden;
                          $this->content['autorizador_id'] = $autorizador_id;
                          $this->content['alteracion'] = $alteracion;
                          ///
                        } else {
                          $autorizador = false;
                          $this->content['autorizador'] = $autorizador;
                        }
                        //
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la solicitud'];
                        $this->content['status'] = $solicitudes->status;
                        $tx->commit();
                      }
                    } else {
                      $this->content['result'] = 'success';
                      $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la solicitud pero sin guardar el registro en los logs'];
                      $this->content['status'] = $solicitudes->status;
                      $tx->commit();
                    }
                  } else {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la solicitud'];
                    $this->content['status'] = $solicitudes->status;
                    $tx->commit();
                  }
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitudes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                    $this->content['status'] = $solicitudes->status;
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update_fechas () {
      try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
            $id = 162;

            $solicitudes = SolicitudesO::find([
                'id != :id: ',
                'bind' => [
                    'id' => 0
                ]
            ]);
            if ($id > 0) {
              foreach ($solicitudes as $solicitud) {
                $fecha = "SELECT fecha from sys_logs where log like '%#". $solicitud->id." %' and level_id = 5";
                $resultado = $this->db->query($fecha)->fetchAll();
                if (count($resultado) > 0) {
                  $solicitud->setTransaction($tx);
                  $solicitud->fecha_solicitado_validacion = $resultado[0]['fecha'];
                  $solicitud->fecha_creado_validacion = $resultado[0]['fecha'];
                  if ($solicitud->update()) {
                    $this->content['result'] = 'success';
                  } else {
                    $this->content['error'] = Helpers::getErrors($solicitud);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                    $tx->rollback();
                  }
                } else {
                  $solicitud->setTransaction($tx);
                  $solicitud->fecha_solicitado_validacion = $solicitud->fecha_solicitado;
                  $solicitud->fecha_creado_validacion = $solicitud->fecha_creado;
                  if ($solicitud->update()) {
                    $this->content['result'] = 'success';
                  } else {
                    $this->content['error'] = Helpers::getErrors($solicitud);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                    $tx->rollback();
                  }
                }
                # code...
              }
            }
            //
            if ($this->content['result'] === 'success') {
              $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se han actualizado las fechas'];
              $tx->commit();
            }
      } catch (Throwable $e) {
        $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
      }
      $this->response->setJsonContent($this->content);
      $this->response->send();
    }

    public function updateEmpresa ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $solicitudes = SolicitudesO::findFirst($request['id']);
            if ($solicitudes) {
                $solicitudes->setTransaction($tx);
                
                if (intval($request['empresa_id']) == 0) {
                  $solicitudes->empresa_id = null;
                } else {
                  $solicitudes->empresa_id = intval($request['empresa_id']);
                }
                if (intval($request['subempresa_id']) == 0) {
                  $solicitudes->subempresa_id = null;
                } else {
                  $solicitudes->subempresa_id = intval($request['subempresa_id']);
                }

                if ($solicitudes->update()) {
                    $this->content['result'] = 'success';
                    $this->content['empresa_id'] = intval($request['empresa_id']);
                    $this->content['subempresa_id'] = intval($request['subempresa_id']);
                    $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha asociado la empresa a la solicitud'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($solicitudes);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                    $tx->rollback();
                }
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateStatus ()
    {
      try {
        $tx = $this->transactions->get();
        $request = $this->request->getPut();

        $solicitudes = SolicitudesO::findFirst($request['solicitud_id']);
        if ($solicitudes) {
          $solicitudes->setTransaction($tx);
          $solicitudes->autorizacion_id = 1;
          $solicitudes->status = 'POR AUTORIZAR';
          if ($solicitudes->update()) {
            $gastos = GastosSolicitudes::find([
                'solicitud_id != :solicitud_id: ',
                'bind' => [
                    'solicitud_id' => $request['solicitud_id']
                ]
            ]);
              foreach ($gastos as $g) {
                $g->setTransaction($tx);
                $g->fecha_ejercido = null;
                if ($g->update()) {
                  $this->content['result'] = 'success';
                } else {
                  $this->content['error'] = Helpers::getErrors($g);
                  $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
                  $tx->rollback();
                }
              }
            $this->content['result'] = 'success';
            $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la solicitud'];
            $tx->commit();
          } else {
            $this->content['error'] = Helpers::getErrors($solicitudes);
            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
            $tx->rollback();
          }
        }
      } catch (Throwable $e) {
        $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
      }

      $this->response->setJsonContent($this->content);
      $this->response->send();
    }

    public function updateClasificacion ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();
                $solicitud = SolicitudesO::findFirst($request['id']);
                if ($solicitud) {
                    $solicitud->setTransaction($tx);
                    if(intval($request['subconcepto_id']) === 0) {
                      $solicitud_id->concepto_id = null;
                    } else {
                      $solicitud->concepto_id = intval($request['concepto_id']);
                    }
                    if(intval($request['subconcepto_id']) === 0) {
                      $solicitud_id->subconcepto_id = null;
                    } else {
                      $solicitud->subconcepto_id = intval($request['subconcepto_id']);
                    }
                    if ($solicitud->update()) {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la solicitud.'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = Helpers::getErrors($solicitud);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la solicitud.'];
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
            
            $request = $this->request->getPut();
            $validUser = Auth::getUserData($this->config);

            if ($request['solicitud_id']) {
                    $solicitudes = SolicitudesO::findFirst($request['solicitud_id']);
                    $solicitudes->setTransaction($tx);
                    $solicitudes->activo = false; 

                    if ($solicitudes->delete()) {
                      $logs = new Logs();
                      $logs->setTransaction($tx);
                      $validUser = Auth::getUserData($this->config);
                      $logs->account_id = $validUser->user_id;
                      $logs->level_id = 13;
                      $logs->log ='ELIMINÓ LA SOLICITUD #'.$solicitudes->id;
                      $logs->fecha = date("Y-m-d H:i:s");
                      if($logs->create()) {
                        $this->content['result'] = 'success';
                      } else {
                        $this->content['error'] = Helpers::getErrors($logs);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                      }
                    } else {
                        $this->content['error'] = Helpers::getErrors($solicitudes);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la solicitud.'];
                    $tx->commit();
                }
                
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }
}