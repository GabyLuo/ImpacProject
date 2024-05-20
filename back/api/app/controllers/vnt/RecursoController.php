<?php

use Phalcon\Mvc\Controller;

class RecursoController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];

    public function getAll ()
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        if (strtoupper($role) === strtoupper('Lider')) {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,case when vnt_recurso.nombre is null then ' ' else vnt_recurso.nombre end as nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, adjudicacion, vnt_recurso.sucursal_id
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            union
            SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'YYYY-MM-DD') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3,
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, monto_sin_iva
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            order by codigo";
        } else {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,case when vnt_recurso.nombre is null then ' ' else vnt_recurso.nombre end as nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3,
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, monto_sin_iva
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            order by vnt_recurso.codigo";
        }
        $this->content['recursos'] = $this->db->query($sql)->fetchAll();

        foreach ($this->content['recursos'] as $key => $recurso) {
            $recurso_id = $recurso['id'];
            $totalRecursos = "SELECT coalesce(
                (SELECT count(pmo_proyecto.id) as proyecto_total
                    from pmo_proyecto
                    where recurso_id = $recurso_id) +

                (SELECT count(vnt_contrato.id) as contrato_total
                    from vnt_contrato
                    where recurso_id = $recurso_id) +

                (SELECT count(lic_licitacion.id) as licitacion_total
                    from lic_licitacion
                    where recurso_id = $recurso_id),0) as total_recursos ";
            $this->content['recursos'][$key]['total_recursos'] = $this->db->query($totalRecursos)->fetchAll()[0]['total_recursos'];
            $this->content['recursos'][$key]['suma_contratos'] = number_format(floatval($recurso['monto_adjudicado_contrato']),2,'.',',');
        }
        
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByYear ($year, $presupuesto, $sucursal)
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        $year_anterior = intval($year) - 1;
        $where = "";
        if (intval($sucursal) > 0) {
            $where = " and vnt_recurso.sucursal_id = " . $sucursal;
        }
        if ($presupuesto !== 'all') {
            $sql_presupuesto = "and (EXISTS (SELECT pmo_proyecto.id from pmo_proyecto where pmo_proyecto.nombre like '%$presupuesto%' and pmo_proyecto.recurso_id = vnt_recurso.id))";
        } else {
            $sql_presupuesto = "";
        }
        if (strtoupper($role) === strtoupper('Lider')) {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,case when vnt_recurso.nombre is null then ' ' else vnt_recurso.nombre end as nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, vnt_recurso.year, adjudicacion, vnt_recurso.sucursal_id, pmo_sucursales.nombre as sucursal
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id 
            where vnt_recurso.year = '$year' $where
            union
            SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, vnt_recurso.year
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, pmo_sucursales.nombre as sucursal
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id
            where vnt_recurso.year = '$year' $where
            union
            SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, vnt_recurso.year
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, vnt_recurso.tipo, monto_sin_iva, , pmo_sucursales.nombre as sucursal
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id 
            where vnt_recurso.year = '$year_anterior' $where and pmo_proyecto.finalizado = false
            order by codigo";
        } else {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,case when vnt_recurso.nombre is null then ' ' else vnt_recurso.nombre end as nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, vnt_recurso.year,
            (select coalesce(sum(vnt_contrato.monto_total), 0) from vnt_contrato where vnt_contrato.recurso_id = vnt_recurso.id) as monto_adjudicado_contrato, adjudicacion, vnt_recurso.sucursal_id, vnt_recurso.tipo, monto_sin_iva, pmo_sucursales.nombre as sucursal
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            left join pmo_sucursales on pmo_sucursales.id = vnt_recurso.sucursal_id 
            where (vnt_recurso.year = '$year' or (EXISTS (SELECT pmo_proyecto.finalizado FROM pmo_proyecto WHERE pmo_proyecto.recurso_id = vnt_recurso.id AND pmo_proyecto.finalizado = false))) {$sql_presupuesto} $where
            order by vnt_recurso.codigo";
        }
        $this->content['recursos'] = $this->db->query($sql)->fetchAll();

        foreach ($this->content['recursos'] as $key => $recurso) {
            $recurso_id = $recurso['id'];
            $totalRecursos = "SELECT coalesce(
                (SELECT count(pmo_proyecto.id) as proyecto_total
                    from pmo_proyecto
                    where recurso_id = $recurso_id) +

                (SELECT count(vnt_contrato.id) as contrato_total
                    from vnt_contrato
                    where recurso_id = $recurso_id) +

                (SELECT count(lic_licitacion.id) as licitacion_total
                    from lic_licitacion
                    where recurso_id = $recurso_id),0) as total_recursos ";
            $this->content['recursos'][$key]['total_recursos'] = $this->db->query($totalRecursos)->fetchAll()[0]['total_recursos'];
            $this->content['recursos'][$key]['suma_contratos'] = number_format(floatval($recurso['monto_adjudicado_contrato']),2,'.',',');
        }
        
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getBy ($id_recurso)
    {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;
        if (strtoupper($role) === strtoupper('Lider')) {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, adjudicacion, vnt_recurso.sucursal_id, monto_sin_iva
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id
            union
            SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, adjudicacion, vnt_recurso.sucursal_id, vnt_recurso.tipo
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id
            join pmo_proyecto on vnt_recurso.id = pmo_proyecto.recurso_id and pmo_proyecto.lider_proyecto = $id and vnt_recurso.id = $id_recurso
            order by codigo";
        } else {
            $sql = "SELECT vnt_recurso.id,vnt_recurso.codigo,cliente_id,vnt_clientes.razon_social,subprograma_id,
            vnt_subprograma.nombre as subprograma_nombre,monto,vnt_recurso.rubro,vnt_recurso.nombre,
            vnt_subprograma.programa_id,
            vnt_programa.nombre as programa_nombre, sys_users.nickname as creador, to_char(vnt_recurso.created, 'DD-MM-YYYY') as fecha_creacion,
            vnt_recurso.fuente_financiamiento, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id) as monto_licitado, (select coalesce(sum(lic_licitacion.monto_adjudicado), 0) from lic_licitacion
            where lic_licitacion.recurso_id = vnt_recurso.id and lic_licitacion.status = 'ADJUDICADA') as monto_adjudicado, aliado, aliado1, aliado2, aliado3, adjudicacion, vnt_recurso.sucursal_id, vnt_recurso.tipo, monto_sin_iva
            from vnt_recurso join vnt_clientes on vnt_recurso.cliente_id=vnt_clientes.id
            join vnt_subprograma on vnt_recurso.subprograma_id=vnt_subprograma.id
            join vnt_programa on vnt_subprograma.programa_id = vnt_programa.id
            join sys_users on vnt_recurso.created_by = sys_users.id and vnt_recurso.id = $id_recurso
            order by vnt_recurso.codigo";
        }
        $this->content['recursos'] = $this->db->query($sql)->fetchAll();

        foreach ($this->content['recursos'] as $key => $recurso) {
            $recurso_id = $recurso['id'];
            $totalRecursos = "SELECT coalesce(
                (SELECT count(pmo_proyecto.id) as proyecto_total
                    from pmo_proyecto
                    where recurso_id = $recurso_id) +

                (SELECT count(vnt_contrato.id) as contrato_total
                    from vnt_contrato
                    where recurso_id = $recurso_id) +

                (SELECT count(lic_licitacion.id) as licitacion_total
                    from lic_licitacion
                    where recurso_id = $recurso_id),0) as total_recursos ";
            $this->content['recursos'][$key]['total_recursos'] = $this->db->query($totalRecursos)->fetchAll()[0]['total_recursos'];
        }
        
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getMontoComision ($tipo, $porcentaje, $aplica, $contrato, $base, $proyecto) {
        // var_dump($aplica);
        if ($aplica == 'AL CONTRATO') {
            $sql="SELECT monto_total as monto from vnt_contrato where id = ".intval($contrato);
        } else {
            $sql="SELECT (SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades 
            inner join pmo_proyecto on pmo_proyecto.id = pmo_actividades.proyecto_id
            and pmo_proyecto.recurso_id = vnt_recurso.id and pmo_actividades.indice = '1'), vnt_recurso.monto from vnt_recurso where id = ".intval($proyecto);
        }
        $monto = $this->db->query($sql)->fetchAll();
        $monto_subtotal_comision = 0;
        $monto_bolsa = $monto[0]['monto'];
        if ($base === 'SUBTOTAL') {
            $monto_base = $monto_bolsa / 1.16;
        } else {
            $monto_base = $monto_bolsa;
        }
        if ($aplica == 'AL CONTRATO') {
            if ($tipo == 'PORCENTAJE') {
                if ($monto_bolsa > 0) {
                    $monto_subtotal_comision = ($monto_base * (floatval($porcentaje) / 100));
                } else {
                    $monto_subtotal_comision = 0;
                }
            }
        }
        $utility = 0;
        if ($aplica == 'A LA UTILIDAD') {
            $utility = $monto_base - $monto[0]['presupuesto_actividad_principal'];
            if ($tipo == 'PORCENTAJE') {
                if ($utility > 0) {
                    $monto_subtotal_comision = ($utility * (floatval($porcentaje) / 100));
                } else {
                    $monto_subtotal_comision = 0;
                }
            }
        }
        $monto_total_comision = round($monto_subtotal_comision,2);
        $this->content['utility'] = number_format(floatval($utility),2,'.',',');
        $this->content['monto_base'] = number_format(floatval($monto_base),2,'.',',');
        $this->content['monto_bolsa'] = number_format(floatval($monto_bolsa),2,'.',',');
        $this->content['monto_comision'] = number_format(floatval($monto_total_comision),2,'.',',');
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function recursosAll ($recursos,$anio) {

        $consecutivos = array();

        foreach ($recursos as $recurso) {
            $codigo=explode("-",$recurso->codigo);
            if(intval($codigo[1])===intval($anio)){
                array_push($consecutivos, intval($codigo[2]));
            }
        } // aqui van los cambios
        if (sizeof($consecutivos) > 0) {
            return max($consecutivos) + 1; // este ya estaba
        } else {
            return 1;
        }
    }

    private function generarCodigo2($cliente,$subprograma,$recurso_id,$year){
        $anio = date('y',strtotime($year.'-01-01'));
        $cliente_id = $cliente->id;

        $recursos = Recurso::find(
            [
                'id != :id: AND cliente_id = :cliente_id: ',
                'bind' => [
                    'cliente_id' => intval($cliente_id),
                    'id' => intval($recurso_id)
                ]
            ]
        );
        
        if(count($recursos)>0){
            $consecutivo = $this->recursosAll($recursos,$anio);
        } else {
            $consecutivo = 1;
        }

        if($consecutivo<10){
            $codigo = $cliente->codigo . '-' . $anio . '-0' . $consecutivo . '-'.$subprograma->codigo;
        } else {
            $codigo = $cliente->codigo . '-' . $anio . '-' . $consecutivo . '-' .$subprograma->codigo;
        }

        return $codigo;
    }

    private function generarCodigo($cliente,$subprograma,$year){
        $anio = date('y',strtotime($year.'-01-01'));
        $cliente_id = $cliente->id;

        $recursos = Recurso::find(
            [
                'cliente_id = :cliente_id: ',
                'bind' => [
                    'cliente_id' => intval($cliente_id)
                ]
            ]
        );
        
        if(count($recursos)>0){
            $consecutivo = $this->recursosAll($recursos,$anio);
        } else {
            $consecutivo = 1;
        }

        if($consecutivo<10){
            $codigo = $cliente->codigo . '-' . $anio . '-0' . $consecutivo . '-'.$subprograma->codigo;
        } else {
            $codigo = $cliente->codigo . '-' . $anio . '-' . $consecutivo . '-' .$subprograma->codigo;
        }

        return $codigo;
    }

    public function getRecursosPerfiles () {
        $validUser = Auth::getUserData($this->config);
        $id = $validUser->user_id;
        $cuenta = $validUser->account_id;
        $role=SysGrants::findFirst("user_id=$validUser->user_id")->SysRoles->name;

        if(strtoupper($role)===strtoupper('admin') || strtoupper($role)===strtoupper('root') || strtoupper($role)===strtoupper('gerente')) {
            $sql = "SELECT vnt_recurso.id, vnt_recurso.codigo from vnt_recurso";
        } else {
            $sql = "SELECT vnt_recurso.id, vnt_recurso.codigo from vnt_recurso
            inner join pmo_proyecto on pmo_proyecto.recurso_id = vnt_recurso.id
            inner join pmo_colaboradores on pmo_proyecto.id = pmo_colaboradores.proyecto_id
            and usuario_id = $id

            union

            SELECT vnt_recurso.id, vnt_recurso.codigo from vnt_recurso
            inner join pmo_proyecto on pmo_proyecto.recurso_id = vnt_recurso.id
            inner join pmo_solicitantes on pmo_proyecto.id = pmo_solicitantes.proyecto_id
            and usuario_id = $id

            union

            SELECT vnt_recurso.id, vnt_recurso.codigo from vnt_recurso
            inner join pmo_proyecto on pmo_proyecto.recurso_id = vnt_recurso.id
            inner join pmo_autorizadores on pmo_proyecto.id = pmo_autorizadores.proyecto_id
            and usuario_id = $id

            union

            SELECT vnt_recurso.id, vnt_recurso.codigo from vnt_recurso
            inner join pmo_proyecto on pmo_proyecto.recurso_id = vnt_recurso.id
            inner join pmo_responsable_pagos on pmo_proyecto.id = pmo_responsable_pagos.proyecto_id
            and usuario_id = $id";
        }

        $proyectos = $this->db->query($sql)->fetchAll();
        $niveles = [];
        foreach ($proyectos as $proyecto) {
            $array['label'] = '' . $proyecto['codigo'];
            $array['value'] = $proyecto['id'];
            array_push($niveles, $array);
        }
        $array['label'] = 'Seleccione el project';
        $array['value'] = 0;
        array_push($niveles, $array);

        $this->content['recursos'] = $niveles;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save ()
    {   
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $cliente = VntClientes::findFirst($request['cliente_id']);
            $subprograma = Subprograma::findFirst($request['subprograma_id']);

            if($cliente->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El cliente no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($subprograma->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El subprograma no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($cliente->codigo !== "" && $subprograma->codigo !== ""){
                $year = $request['year'];
                $codigo = $this->generarCodigo($cliente,$subprograma,$year);

                $recursos = new Recurso();
                $recursos->setTransaction($tx);
                //campo para separa por anio
                $recursos->year = $request['year'];
                $recursos->codigo = strtoupper($codigo);
                $recursos->cliente_id = intval($request['cliente_id']);
                $recursos->subprograma_id = intval($request['subprograma_id']);
                $recursos->fuente_financiamiento = strtoupper($request['fuente_financiamiento']);
                $recursos->monto = $request['monto'];
                $recursos->monto_sin_iva = $request['monto_sin_iva'];
                $recursos->tipo = $request['tipo'];
                if (trim($request['rubro']) === '') {
                    $recursos->rubro = null;
                } else {
                    $recursos->rubro = trim($request['rubro']);
                }
                if (trim($request['nombre']) === '') {
                    $recursos->nombre = null;
                } else {
                    $recursos->nombre = trim($request['nombre']);
                }
                if (trim($request['aliado']) === '') {
                    $recursos->aliado = null;
                } else {
                    $recursos->aliado = trim($request['aliado']);
                }
                if (trim($request['aliado1']) === '') {
                    $recursos->aliado1 = null;
                } else {
                    $recursos->aliado1 = trim($request['aliado1']);
                }
                if (trim($request['aliado2']) === '') {
                    $recursos->aliado2 = null;
                } else {
                    $recursos->aliado2 = trim($request['aliado2']);
                }
                if (trim($request['aliado3']) === '') {
                    $recursos->aliado3 = null;
                } else {
                    $recursos->aliado3 = trim($request['aliado3']);
                }
                if (trim($request['adjudicacion']) === '') {
                    $recursos->adjudicacion = null;
                } else {
                    $recursos->adjudicacion = trim($request['adjudicacion']);
                }
                $recursos->sucursal_id = null;
                if ($recursos->create()) {
                    $logs = new Logs();
                            $logs->setTransaction($tx);
                            $validUser = Auth::getUserData($this->config);
                            $logs->account_id = $validUser->user_id;
                            $logs->level_id = 10;
                            $logs->log = 'CREÓ EL PROYECT0: ' . $codigo;
                            $logs->fecha = date("Y-m-d H:i:s");
                            if($logs->create()){
                              $this->content['result']='success';
                              $this->content['codigo'] = $codigo;
                              $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha creado el proyecto.'];
                              $tx->commit();
                            } else {
                              $this->content['result']='error';
                              $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha autorizado la solicitud pero sin guardar el registro en logs.']; 
                              $tx->rollback();
                            }
                } else {
                    $this->content['error'] = Helpers::getErrors($recursos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el recurso.'];
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
    
    public function update ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $cliente = VntClientes::findFirst($request['cliente_id']);
            $subprograma = Subprograma::findFirst($request['subprograma_id']);
            $year = $request['year'];

            if($cliente->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El cliente no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($subprograma->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El subprograma no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($cliente->codigo !== "" && $subprograma->codigo !== ""){
                $recurso = Recurso::findFirst($request['id']);

                if(intval($recurso->cliente_id) === intval($request['cliente_id']) && intval($recurso->subprograma_id) === intval($request['subprograma_id'])) {
                    $array_codigo = explode("-",$recurso->codigo);
                    if(count($array_codigo) === 4) {
                        if($array_codigo[0] === $cliente->codigo && $array_codigo[3] === $subprograma->codigo){
                            $recurso->setTransaction($tx);
                            $recurso->fuente_financiamiento = strtoupper($request['fuente_financiamiento']);
                            $recurso->monto = $request['monto'];
                            if (trim($request['rubro']) === '') {
                                $recurso->rubro = null;
                            } else {
                                $recurso->rubro = trim($request['rubro']);
                            }
                            if (trim($request['nombre']) === '') {
                                $recurso->nombre = null;
                            } else {
                                $recurso->nombre = trim($request['nombre']);
                            }          
                            if (trim($request['aliado']) === '') {
                                $recurso->aliado = null;
                            } else {
                                $recurso->aliado = trim($request['aliado']);
                            }
                            if (trim($request['aliado1']) === '') {
                                $recurso->aliado1 = null;
                            } else {
                                $recurso->aliado1 = trim($request['aliado1']);
                            }
                            if (trim($request['aliado2']) === '') {
                                $recurso->aliado2 = null;
                            } else {
                                $recurso->aliado2 = trim($request['aliado2']);
                            }
                            if (trim($request['aliado3']) === '') {
                                $recurso->aliado3 = null;
                            } else {
                                $recurso->aliado3 = trim($request['aliado3']);
                            }
                            if (trim($request['adjudicacion']) === '') {
                                $recurso->adjudicacion = null;
                            } else {
                                $recurso->adjudicacion = trim($request['adjudicacion']);
                            }
                            if (intval($request['sucursal_id']) > 0) {
                                $recurso->sucursal_id = $request['sucursal_id'];
                            } else {
                                $recurso->sucursal_id = null;
                            }
                            $recursos->monto_sin_iva = $request['monto_sin_iva'];
                            $recurso->tipo = $request['tipo'];
                            if ($recurso->update()) {
                                $this->content['result'] = 'success';
                                $this->content['cambio_codigo'] = 'false';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el recurso.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($recurso);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el recurso'];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                        }
                    } else {
                        $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El código del recurso no es válido, revise en el apartado de Recursos.'];
                    }
                } else {
                    $codigo = $this->generarCodigo2($cliente,$subprograma,$request['id'],$year);
                    $recurso->setTransaction($tx);
                    $recurso->codigo = strtoupper($codigo);
                    $cliente_anterior = $recurso->cliente_id;
                    $cliente_anterior_rs = VntClientes::findFirst($cliente_anterior)->razon_social;
                    $recurso->cliente_id = intval($request['cliente_id']);
                    $recurso->subprograma_id = intval($request['subprograma_id']);
                    $recurso->fuente_financiamiento = strtoupper($request['fuente_financiamiento']);
                    $recurso->monto = $request['monto'];
                    if (trim($request['rubro']) === '') {
                        $recurso->rubro = null;
                    } else {
                        $recurso->rubro = trim($request['rubro']);
                    }
                    if (trim($request['nombre']) === '') {
                        $recurso->nombre = null;
                    } else {
                        $recurso->nombre = trim($request['nombre']);
                    }

                    if (trim($request['aliado']) === '') {
                        $recurso->aliado = null;
                    } else {
                        $recurso->aliado = trim($request['aliado']);
                    }
                    if (trim($request['aliado1']) === '') {
                        $recurso->aliado1 = null;
                    } else {
                        $recurso->aliado1 = trim($request['aliado1']);
                    }
                    if (trim($request['aliado2']) === '') {
                        $recurso->aliado2 = null;
                    } else {
                        $recurso->aliado2 = trim($request['aliado2']);
                    }
                    if (trim($request['aliado3']) === '') {
                        $recurso->aliado3 = null;
                    } else {
                        $recurso->aliado3 = trim($request['aliado3']);
                    }
                    if (trim($request['adjudicacion']) === '') {
                        $recurso->adjudicacion = null;
                    } else {
                        $recurso->adjudicacion = trim($request['adjudicacion']);
                    }
                    if (intval($request['sucursal_id']) > 0) {
                        $recurso->sucursal_id = $request['sucursal_id'];
                    } else {
                        $recurso->sucursal_id = null;
                    }
                    $recursos->monto_sin_iva = $request['monto_sin_iva'];
                    $recurso->tipo = $request['tipo'];
                    if ($recurso->update()) {
                        $logs = new Logs();
                        $logs->setTransaction($tx);
                        $validUser = Auth::getUserData($this->config);
                        $logs->account_id = $validUser->user_id;
                        $logs->level_id = 10;
                        $logs->log = 'CAMBIÓ DE CLIENTE EL PROYECTO CON ID #' . $recurso->id . ' Y CÓDIGO: ' . $recurso->codigo . ' DEL CLIENTE #' . $cliente_anterior . ' CON RAZÓN SOCIAL ' . $cliente_anterior_rs . ' AL CLIENTE ' . $recurso->cliente_id . ' CON RAZÓN SOCIAL ' . $cliente->razon_social;
                        $logs->fecha = date("Y-m-d H:i:s");
                        if($logs->create()) {
                            $this->content['result'] = 'success';
                            $this->content['cambio_codigo'] = 'true';
                            $this->content['codigo'] = $codigo;
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el proyecto.'];
                            $tx->commit();
                        } else {
                            $this->content['result']='error';
                            $this->content['message']=['title' => '¡Éxito!', 'content' => 'No se pudo actualizar el proyecto.']; 
                            $tx->rollback();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($recurso);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el recurso'];
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

    public function create_from_licitacion () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            if ($request['recurso_id'] !== '') {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'La licitación ya tiene un proyecto vinculado'];
            } else {

            $cliente = VntClientes::findFirst("razon_social = 'DEFAULT'");
            $subprograma = Subprograma::findFirst("nombre = 'DEFAULT'");

            if($cliente->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El cliente no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($subprograma->codigo === ""){
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'El subprograma no tiene un código, por lo que no podemos crear el código del recurso'];
            } else if($cliente->codigo !== "" && $subprograma->codigo !== ""){
                $year = $request['year'];
                $codigo = $this->generarCodigo($cliente,$subprograma,$year);

                $recursos = new Recurso();
                $recursos->setTransaction($tx);
                //campo para separa por anio
                $recursos->year = $request['year'];
                $recursos->codigo = strtoupper($codigo);
                $recursos->cliente_id = $cliente->id;
                $recursos->subprograma_id = $subprograma->id;
                $recursos->fuente_financiamiento = 'DEFAULT';
                $recursos->monto = 0;
                $recursos->licitacion_id = intval($request['licitacion_id']);
                $recursos->nombre = $request['descripcion'];
                if ($recursos->create()) {
                    $logs = new Logs();
                    $logs->setTransaction($tx);
                    $validUser = Auth::getUserData($this->config);
                    $logs->account_id = $validUser->user_id;
                    $logs->level_id = 10;
                    $logs->log = 'CREÓ EL PROYECT0 MEDIANTE UNA LICITACIÓN: ' . $codigo;
                    $logs->fecha = date("Y-m-d H:i:s");
                    if ($logs->create()){
                        $licitacion = Licitacion::findFirst($request['licitacion_id']);
                        if ($licitacion) {
                            $licitacion->setTransaction($tx);
                            $licitacion->recurso_id = $recursos->id;
                            if ($licitacion->update()) {
                                $this->content['result']='success';
                                $this->content['id'] = $recursos->id;
                                $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha creado el proyecto.'];
                                $tx->commit();
                            } else {
                                $this->content['result']='error';
                                $this->content['message']=['title' => '¡Éxito!', 'content' => 'No se ha podido actualizar la licitación.']; 
                                $tx->rollback();
                            }
                        }    
                    } else {
                      $this->content['result']='error';
                      $this->content['message']=['title' => '¡Éxito!', 'content' => 'No se ha podido crear el proyecto.']; 
                      $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($recursos);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear el recurso.'];
                    $tx->rollback();
                }
            }
        }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
            $this->content['message'] = ['title' => '¡Error!', 'content' => $e->getMessage()];
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();
            if ($request['id']) {
                    $recursos = Recurso::findFirst($request['id']);
                    $recursos->setTransaction($tx);

                    if ($recursos->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($recursos);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el recurso.'];
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