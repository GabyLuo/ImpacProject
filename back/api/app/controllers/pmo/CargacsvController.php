<?php

use Phalcon\Mvc\Controller;


class CargacsvController extends Controller
{

    public $content = ['result' => 'error', 'message' => ''];
    
    public $colores = ['indigo-5','teal','amber-6', 'orange-5','yellow-12', 'cyan-4','blue-grey-4'];

    public function getAll ()
    {
        $sql = "SELECT *
                from pmo_carga
                order by id";
        $this->content['cargas'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAllActividades()
    {
        $sql = "SELECT * from pmo_actividades 
                where padre_id is not null and nombre <> ': --' 
                and nombre <> '--' and nombre <> ':' 
                and nivel <> 1 and nivel <> 2
                order by indice";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getActividadesOpt($proyecto_id)
    {
        $sql = "SELECT id, nombre, (select count(*) from pmo_actividades as x where padre_id = pmo_actividades.id) as hijos, (select nombre from pmo_actividades as x where id = pmo_actividades.padre_id) as padre from pmo_actividades 
                where padre_id is not null and nombre <> ': --' 
                and nombre <> '--' and nombre <> ':' 
                and nivel <> 1 -- and nivel <> 2
                and proyecto_id = $proyecto_id and pmo_actividades.finalizado = false and pmo_actividades.visible = false
                order by indice";
        $actividades = $this->db->query($sql)->fetchAll();
        $act = [];
        foreach ($actividades as $actividad) {
            if(intval($actividad['hijos']) == 0) {
                $array['label'] = $actividad['padre'].' -- ' . $actividad['nombre'];
                $array['value'] = $actividad['id'];
                array_push($act, $array);
            }
        }
        $this->content['actividades'] = $act;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getPresupuestoActividad($id)
    {
        $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, 
        round( (pmo_actividades.costo - (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id and fin_gastos.actividad_id = $id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)) as cambios)) ,2) as cantidad_disponible, (SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'RECHAZADO')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'RECHAZADO')), 0) as total2) as cantidad_real
            from pmo_actividades where id = $id";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getPresupuestoActividadReal($id)
    {
        $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
        FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total2))) as cantidad_disponible
            from pmo_actividades where id = $id";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function getPresupuestoActividadReal_disponible($id)
    {
        $actividad_padre = Actividades::findFirst($id);
        $proyecto_id = $actividad_padre->proyecto_id;
        $nivel = $actividad_padre->nivel;
        $cantidad_disponible = 0;
        $cantidad_ejercida = 0;
        $cantidad_comprometida = 0;

        // $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - (select coalesce(sum(cambios.suma), 0) as recurso_ejercido from (select case when fin_solicitudes.iva = true then (fin_gastos.monto + (fin_gastos.monto * .16)) else fin_gastos.monto end as suma from fin_solicitudes inner join fin_gastos on fin_solicitudes.id = fin_gastos.solicitud_id inner join pmo_actividades on pmo_actividades.id = fin_gastos.actividad_id and fin_gastos.actividad_id = $id and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)) as cambios)) as cantidad_disponible, (SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_gastos.fecha_ejercido is not null)), 0) as total2) as cantidad_ejercida, (SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'RECHAZADO')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status != 'CANCELADO' and fin_solicitudes.status != 'RECHAZADO')), 0) as total2) as cantidad_comprometida from pmo_actividades where id = $id";
        $sql = "SELECT * from get_presupuesto_by_actividad($id)";

        $sql_padre = "SELECT count(*), sum(costo) as costo from pmo_actividades where padre_id = $id and proyecto_id = $proyecto_id";
        $data_padre = $this->db->query($sql_padre)->fetchAll();

        $data = $this->db->query($sql)->fetchAll();
        if ($data_padre[0]['count'] > 0) {
            if ($data[0]['costo'] == ($data_padre[0]['costo'] + round($data[0]['cantidad_ejercida']))) {
                $cantidad_disponible = 0;
            } else {
                $cantidad_disponible = 0 - $data[0]['cantidad_ejercida'];
            }
            // $cantidad_disponible = $data[0]['cantidad_disponible'];
            /* if ($data[0]['costo'] == $data[0]['cantidad_ejercida']) {
                $cantidad_disponible = $data[0]['costo'] - $data[0]['cantidad_ejercida'];
            } else { */
                
            // }
        } else {
            $cantidad_disponible = 0;
            $cantidad_disponible = $cantidad_disponible + $data[0]['cantidad_disponible'];
        }
        $cantidad_ejercida = $cantidad_ejercida + $data[0]['cantidad_ejercida'];
        $cantidad_comprometida = $cantidad_comprometida + $data[0]['cantidad_comprometida'];
        $actividad_padre = Actividades::findFirst($id);
        $id = $actividad_padre->padre_id;

        $dato=(object)array();
        $dato->cantidad_disponible = $cantidad_disponible;
        $dato->cantidad_ejercida = $cantidad_ejercida;
        $dato->cantidad_comprometida = $cantidad_comprometida;
        
        return $dato;
    }

    private function getPresupuestoActividadReal_disponible_recursivo($id, $padre)
    {
        $cantidad_ejercida = 0;
        $actividades = Actividades::find(
            [
                'padre_id = :padre_id:',
                'order' => 'indice',
                'bind' => [
                    'padre_id' => $id
                ]
            ]
        );

        if (sizeof($actividades) > 0) {
            // $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida from pmo_actividades where padre_id = $id";

            // $data = $this->db->query($sql)->fetchAll();
            // foreach ($data as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['cantidad_ejercida'];
            // }
            foreach ($actividades as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['id'];
                return $this->getPresupuestoActividadReal_disponible_recursivo($elemento->id, $cantidad_ejercida, $elemento->padre_id);
            }
        } else {
            $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida
                from pmo_actividades where id = $id";
            $data = $this->db->query($sql)->fetchAll();
            // foreach ($data as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['id'];
                $cantidad_ejercida = $cantidad_ejercida + $data[0]['cantidad_ejercida'];
            // }
            
        }
        return $cantidad_ejercida;
    }

    private function getPresupuestoActividadReal_disponible_recursivo3($id, $padre, $cantidad_ejercida)
    {
        $actividades = Actividades::find(
            [
                'padre_id = :padre_id:',
                'order' => 'indice',
                'bind' => [
                    'padre_id' => $id
                ]
            ]
        );

        if (sizeof($actividades) > 0) {
            // $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida from pmo_actividades where padre_id = $id";

            // $data = $this->db->query($sql)->fetchAll();
            // foreach ($data as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['cantidad_ejercida'];
            // }
            foreach ($actividades as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['id'];
                return $this->getPresupuestoActividadReal_disponible_recursivo($elemento->id, $cantidad_ejercida, $elemento->padre_id);
            }
        } else {
            $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida
                from pmo_actividades where id = $id";
            $data = $this->db->query($sql)->fetchAll();
            // foreach ($data as $elemento) {
                // $cantidad_ejercida = $cantidad_ejercida + $elemento['id'];
                $cantidad_ejercida = $cantidad_ejercida + $data[0]['cantidad_ejercida'];
            // }
            
        }
        return $cantidad_ejercida;
    }

    private function getPresupuestoActividadReal_disponible_recursivo2($id, $valor, $cantidad_ejercida)
    {
        if ($valor > 0) {
            $actividades = Actividades::find(
            [
                'padre_id = :padre_id:',
                'order' => 'indice',
                'bind' => [
                    'padre_id' => $id
                ]
            ]
            );
            foreach ($actividades as $actividad) {
                $id_actividad = $actividad->id;
                $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id_actividad and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida from pmo_actividades where id = $id_actividad";

                $data = $this->db->query($sql)->fetchAll();
                foreach ($data as $elemento) {
                    $cantidad_ejercida = $cantidad_ejercida + $elemento['cantidad_ejercida'];
                }
            }
            
            return $cantidad_ejercida;
        } else {
            $sql = "SELECT pmo_actividades.costo, pmo_actividades.nombre, (pmo_actividades.costo - ((SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2))) as cantidad_disponible,(SELECT coalesce ((SELECT sum(fin_gastos.monto + fin_gastos.monto * 0.16) as suma
                FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = true and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total1) + (SELECT coalesce ((SELECT sum(fin_gastos.monto) as suma FROM fin_gastos inner join fin_solicitudes on fin_solicitudes.id = fin_gastos.solicitud_id and fin_gastos.actividad_id = $id and fin_solicitudes.iva = false and (fin_solicitudes.status = 'PAGADO' or fin_solicitudes.status = 'PAGO PARCIAL')), 0) as total2) as cantidad_ejercida
                from pmo_actividades where id = $id";
            $data = $this->db->query($sql)->fetchAll();
            $cantidad_ejercida = $cantidad_ejercida + $data[0]['cantidad_ejercida'];
            return $cantidad_ejercida;
        }
    }


    public function getByActividad($id)
    {
        $sql = "SELECT nombre
                from pmo_actividades
                where id = $id";
        $this->content['actividades'] = $this->db->query($sql)->fetchAll();
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getAllActividadesProject() {
        $sql = "SELECT *
                from pmo_proyecto";
        $projects = $this->db->query($sql)->fetchAll();

        $projects_actividades = [];
        foreach ($projects as $project) {
            $nodo_proyectos = (object)array();
            $nodo_proyectos->id = null;
            $nodo_proyectos->proyecto_id = null;
            $nodo_proyectos->nombre = null;
            $nodo_proyectos->avance = null;
            $nodo_proyectos->inicio = null;
            $nodo_proyectos->fin = null;
            $nodo_proyectos->costo = null;
            $nodo_proyectos->costoCopia = null;
            $nodo_proyectos->presupuesto_inicial = null;
            $nodo_proyectos->presupuesto_inicial_copia = null;
            $nodo_proyectos->fin_real = null;
            $nodo_proyectos->indice = null;
            $nodo_proyectos->nivel = null;
            $nodo_proyectos->resumen = null;
                
            $nodo_proyectos->fin_real = null;
            $nodo_proyectos->duracion = null;
            $nodo_proyectos->duracion_restante = null;
            $nodo_proyectos->padre_id = null;
            $nodo_proyectos->expend = true;
            $nodo_proyectos->color = null;
            $nodo_proyectos->orden = 0;

            $actividades = Actividades::find(
                [
                    'proyecto_id = :proyecto_id: AND padre_id IS NULL',
                    'order' => 'indice',
                    'bind' => [
                        'proyecto_id' => $project['id']
                    ]
                ]
            );

            $padres = [];
            foreach($actividades as $padre){
                $nodo_padres=(object)array();
                $nodo_padres->id = $padre->id;
                $nodo_padres->proyecto_id =$padre->proyecto_id;
                $nodo_padres->nombre =$padre->nombre;
                $nodo_padres->avance =$padre->avance;
                $nodo_padres->inicio =$padre->inicio;
                $nodo_padres->fin =$padre->fin;
                $nodo_padres->costo =$padre->costo;
                $nodo_padres->costoCopia =number_format(floatval($padre->costo),2,'.',',');
                $nodo_padres->presupuesto_inicial =$padre->presupuesto_inicial;
                $nodo_padres->presupuesto_inicial_copia =number_format(floatval($padre->presupuesto_inicial),2,'.',',');
                $nodo_padres->fin_real =$padre->fin_real;
                $nodo_padres->indice =$padre->indice;
                $nodo_padres->nivel =$padre->nivel;
                if(intval($padre->resumen) === 0){
                    $nodo_padres->resumen = 'SI';
                } else {
                    $nodo_padres->resumen ='NO';
                }
                $nodo_padres->fin_real =$padre->fin_real;
                $nodo_padres->duracion =$padre->duracion;
                $nodo_padres->duracion_restante =$padre->duracion_restante;
                $nodo_padres->padre_id =$padre->padre_id;

                $nodo_padres->expend = true;
                $nodo_padres->color = $this->colores[0];

                $nodo_padres->orden = intval($padre->indice);

                array_push($padres,$nodo_padres);
            }
            $nodo_proyectos->actividades =  $this->encuentraHijos($padres,$project['id']);
            array_push($projects_actividades, $nodo_proyectos);
        }

        $this->content['actividades'] = $projects_actividades;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getByProyecto($proyecto_id){

        $actividades = Actividades::find(
            [
                'proyecto_id = :proyecto_id: AND padre_id IS NULL',
                // 'order' => 'indice',
                'bind' => [
                    'proyecto_id' => $proyecto_id
                ]
            ]
        );

        $padres = [];
        foreach($actividades as $padre){
            $data = $this->getPresupuestoActividadReal_disponible($padre->id);
            $nodo_padres=(object)array();
            $nodo_padres->id = $padre->id;
            $nodo_padres->proyecto_id =$padre->proyecto_id;
            $nodo_padres->nombre =$padre->nombre;
            $nodo_padres->avance =$padre->avance;
            $nodo_padres->inicio =$padre->inicio;
            $nodo_padres->fin =$padre->fin;
            $nodo_padres->costo =$padre->costo;
            $nodo_padres->costoCopia =number_format(floatval($padre->costo),2,'.',',');
            $nodo_padres->presupuesto_inicial =$padre->presupuesto_inicial;
            $nodo_padres->presupuesto_inicial_copia =number_format(floatval($padre->presupuesto_inicial),2,'.',',');
            $nodo_padres->fin_real =$padre->fin_real;
            $nodo_padres->indice =$padre->indice;
            $nodo_padres->nivel =$padre->nivel;
            if(intval($padre->resumen) === 0){
                $nodo_padres->resumen = 'SI';
            } else {
                $nodo_padres->resumen ='NO';
            }
            $nodo_padres->fin_real =$padre->fin_real;
            $nodo_padres->duracion =$padre->duracion;
            $nodo_padres->duracion_restante =$padre->duracion_restante;
            $nodo_padres->padre_id =$padre->padre_id;

            $nodo_padres->expend = true;
            $nodo_padres->color = $this->colores[0];

            $nodo_padres->orden = intval($padre->indice);
            // $nodo_padres->cantidad_ejercida = $this->getPresupuestoActividadReal_disponible_recursivo($padre->id, 0, $padre->id);
            $nodo_padres->cantidad_ejercida = number_format(floatval($data->cantidad_ejercida),2,'.',',');
            if ($nodo_padres->cantidad_ejercida == 0) {
                $nodo_padres->avance_monetario = 0;
            } else {
                $multi = $nodo_padres->cantidad_ejercida * 100;
                $nodo_padres->avance_monetario = number_format(floatval($multi / $padre->costo),2,'.',',');
            }
            if ($padre->finalizado == true) {
                $nodo_padres->avance_monetario = 100;
            }
            // number_format(floatval($data->cantidad_ejercida),2,'.',',');
            $nodo_padres->cantidad_disponible = number_format(floatval($data->cantidad_disponible),2,'.',',');
            $nodo_padres->cantidad_comprometida = number_format(floatval($data->cantidad_comprometida),2,'.',',');
            $nodo_padres->finalizado = $padre->finalizado;
            $nodo_padres->visible = $padre->visible;

            array_push($padres,$nodo_padres);
        }

        $actividades_nodos = $this->encuentraHijos($padres,$proyecto_id);
        // $actividades_nodos_2 = $this->actualizaCantidades($actividades_nodos,$proyecto_id);
        $this->content['actividades'] = $actividades_nodos;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function save() {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $id_act = intval($request['padre_id']);
            $sql = "SELECT indice from pmo_actividades where padre_id =$id_act order by id desc limit 1";
            $valor_indice = $this->db->query($sql)->fetchAll();
            if (count($valor_indice) > 0) {
                $numeros = explode(".", $valor_indice[0]['indice']);
                $indice_num = "";
                // var_dump($numeros);
                // var_dump(count($numeros));
                for ($i=0; $i<count($numeros); $i++) {
                    if ($i == (count($numeros)-1)) {
                        $indice_num = $indice_num . (intval($numeros[$i]) + 1);
                    } else {
                        $indice_num = $indice_num . $numeros[$i] . '.';
                    }
                }
            } else {
                $indice_num = Actividades::findFirst($id_act)->indice.'.1';
            }
            // var_dump($indice_num);
            // $numeros = explode(".", $valor_indice[0]['indice']);
            
            // $indice_num = $numeros[0].'.'.$numeros[1].'.'.intval($numeros[2] + 1);

            $actividades = Actividades::findFirst(
                [
                    'nombre = :nombre: AND (padre_id = :padre_id:)',
                    'bind' => [
                        'nombre' => $request['nombre'],
                        'padre_id' => $id_act
                    ]
                ]
            );
            if ($actividades) {
                $this->content['message'] = ['title' => '¡Advertencia!', 'content' => 'Ya existe esa actividad, ingrese un nombre diferente.'];
            } else {
                //
                // print_r($request['costo_actividad']);
                $actividades =  new Actividades();
                $actividades->setTransaction($tx);

                $actividades->proyecto_id  =  $request['proyecto_id'];
                $actividades->nombre  =  trim($request['nombre']);
                $actividades->avance  =  $request['avance'];
                $actividades->inicio =  $request['inicio'];
                $actividades->fin =  $request['fin'];
                $actividades->costo =  floatval($request['costo_actividad']);
                $actividades->presupuesto_inicial =  floatval($request['costo_actividad']);
                $actividades->fin_real  =  null;
                $actividades->indice  =  $indice_num;
                $actividades->nivel  =  Actividades::findFirst($id_act)->nivel + 1;
                $actividades->resumen =  1;
                $actividades->duracion =  $request['duracion'];
                $actividades->duracion_restante =  $request['duracion_restante'];
                $actividades->padre_id = intval($request['padre_id']);
                if  ($actividades->create())  {
                    $nivel = $actividades->nivel;
                    //
                    for ($i=$nivel; $i>1; $i--) {
                        $actividad_padre = Actividades::findFirst($id_act);
                        if ($actividad_padre !== false) {
                            $actividad_padre->setTransaction($tx);
                            $actividad_padre->costo = $actividad_padre->costo + $actividades->costo;
                            if ($actividad_padre->update()) {
                                $this->content['result'] = 'success';
                                $id_act = $actividad_padre->padre_id;
                            } else {
                                $this->content['error'] = Helpers::getErrors($actividad_padre);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la actividad'];
                                $tx->rollback();
                            }
                        }
                    }
                    $monto_actual = 0;
                    $projectAModificar = Proyecto::findFirst($request['proyecto_id']);
                    if ($projectAModificar !== false) {
                        $projectAModificar->setTransaction($tx);
                        $monto_actual = $projectAModificar->presupuesto_actual + $actividades->costo;
                        $projectAModificar->presupuesto_actual = $monto_actual;
                        if ($projectAModificar->update()) {
                            $this->content['result'] = 'success';
                        } else {
                            $this->content['error'] = Helpers::getErrors($projectAModificar);
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el proyecto'];
                            $tx->rollback();
                        }
                    }
                    if ($this->content['result'] == 'success') {
                        $this->content['monto_actual'] = $monto_actual;
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha creado la actividad.'];
                        $this->content['result']  =  'success';
                        $tx->commit();
                    } else {
                        $this->content['result'] = 'Error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo modificar la actividad principal.'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['result'] = 'Error';
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la actividad.'];
                    $tx->rollback();
                } 
                //
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

            $id_proyecto = intval($request['id']);
            $sql_solicitudes = "SELECT id from fin_solicitudes where proyecto_id = $id_proyecto";
            $solicitudes=$this->db->query($sql_solicitudes)->fetchAll();
        
            if (sizeof($solicitudes)>0) {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No es posible eliminar las actividades, existen solicitudes vinculadas.'];
            } else {

                $this->content['result']='success';
                $sql_carga = "SELECT id from pmo_carga where proyecto_id = $id_proyecto";
                $cargasArchivo=$this->db->query($sql_carga)->fetchAll();
                $id_carga = intval($cargasArchivo[0]['id']);
                
                $cargas = CargaDetalles::find(
                    [
                        'carga_id = :carga_id:',
                        'bind' => [
                            'carga_id' => $id_carga
                        ]
                    ]
                );
                foreach($cargas as $acti){
                    $acti->setTransaction($tx);
                    if($acti->delete()){
                    } else {
                        $this->content['error'] = Helpers::getErrors($acti);
                        $this->content['message']  =  'Error al borrar actividades';
                        $tx->rollback();
                    }
                }
                $actividades = Actividades::find(
                    [
                        'proyecto_id = :proyecto_id:',
                        'bind' => [
                            'proyecto_id' => $id_proyecto
                        ]
                    ]
                );
                foreach($actividades as $acti){
                    $acti->setTransaction($tx);
                    if($acti->delete()){
                    } else {
                        $this->content['error'] = Helpers::getErrors($acti);
                        $this->content['message']  =  'Error al borrar actividades';
                        $tx->rollback();
                    }
                }

                if ($this->content['result']==='success') {
                    $tx->commit();
                    $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se han eliminado las actividades de este project.'];
                }
            }
                
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete_single ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();

            $id = intval($request['id']);
            $sql_solicitudes = "SELECT id from fin_gastos where actividad_id = $id";
            $solicitudes=$this->db->query($sql_solicitudes)->fetchAll();
        
            if (sizeof($solicitudes)>0) {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No es posible eliminar la actividad, existen solicitudes vinculadas.'];
            } else {
                $actividades = Actividades::findFirst($id);
                // foreach($actividades as $acti) {
                    $id_act = $actividades->padre_id;
                    $nivel = $actividades->nivel;
                    $proyecto_id = $actividades->proyecto_id;
                    $actividades->setTransaction($tx);
                    if($actividades->delete()){
                        $costo = $actividades->costo;
                        // $this->content['result']='success';
                        //
                        for ($i=$nivel; $i>1; $i--) {
                            $actividad_padre = Actividades::findFirst($id_act);
                            if ($actividad_padre !== false) {
                                $actividad_padre->setTransaction($tx);
                                $actividad_padre->costo = $actividad_padre->costo -  $costo;
                                if ($actividad_padre->update()) {
                                    $this->content['result'] = 'success';
                                    $id_act = $actividad_padre->padre_id;
                                } else {
                                    $this->content['error'] = Helpers::getErrors($actividad_padre);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo crear la actividad'];
                                    $tx->rollback();
                                }
                            }
                        }
                        $monto_actual = 0;
                        $projectAModificar = Proyecto::findFirst($proyecto_id);
                        if ($projectAModificar !== false) {
                            $projectAModificar->setTransaction($tx);
                            $monto_actual = $projectAModificar->presupuesto_actual - $costo;
                            $projectAModificar->presupuesto_actual = $monto_actual;
                            if ($projectAModificar->update()) {
                                $this->content['result'] = 'success';
                            } else {
                                $this->content['error'] = Helpers::getErrors($projectAModificar);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el proyecto'];
                                $tx->rollback();
                            }
                        }
                        //
                    } else {
                        $this->content['error'] = Helpers::getErrors($acti);
                        $this->content['message']  =  'Error al borrar actividad';
                        $tx->rollback();
                    }
                // }

                if ($this->content['result']==='success') {
                    $this->content['monto_actual'] = $monto_actual;
                    $this->content['message']=['title' => '¡Éxito!', 'content' => 'Se ha eliminado la actividad de este project.'];
                    $tx->commit();
                }
            }
                
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function encuentraHijos($actividades,$proyecto_id) {
        foreach($actividades as $actividad) {
            $children = $this->buscarHijos($actividad->id,$proyecto_id);
            $actividad->children = $children;
            

            if(count($children)>0){
                if(intval($actividad->cantidad_ejercida) === 0) { 
                    $actividad->cantidad_ejercida = '-------';
                    $actividad->cantidad_disponible = '-------';
                    $actividad->avance_monetario = '----';
                }
                $this->encuentraHijos($children,$proyecto_id);
            }
        }
        return $actividades;
    }

    public function exportCSV_actividades($project) {
        $content = $this->content;
        $fp = fopen('php://temp/maxmemory:' . (12 * 1024 * 1024), 'r+');

        fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

        fputcsv($fp, array('Nombre','Avance','Fecha inicio','Fecha fin','Costo','Costo inicial','Ejercido', 'Disponible','Solicitado', 'Fin real', 'EDT','Resumen','Duración', 'Duración restante'), ',');
        $project_nombre = Proyecto::findFirst($project)->nombre;

        $actividades = Actividades::find(
            [
                'proyecto_id = :proyecto_id: AND padre_id IS NULL',
                // 'order' => 'indice',
                'bind' => [
                    'proyecto_id' => $project
                ]
            ]
        );

        $padres = [];
        foreach($actividades as $padre){
            $data = $this->getPresupuestoActividadReal_disponible($padre->id);
            $nodo_padres=(object)array();
            $nodo_padres->id = $padre->id;
            $nodo_padres->proyecto_id =$padre->proyecto_id;
            $nodo_padres->nombre =$padre->nombre;
            $nodo_padres->avance =$padre->avance;
            $nodo_padres->inicio =$padre->inicio;
            $nodo_padres->fin =$padre->fin;
            $nodo_padres->costo =$padre->costo;
            $nodo_padres->costoCopia =number_format(floatval($padre->costo),2,'.',',');
            $nodo_padres->presupuesto_inicial =$padre->presupuesto_inicial;
            $nodo_padres->presupuesto_inicial_copia =number_format(floatval($padre->presupuesto_inicial),2,'.',',');
            $nodo_padres->fin_real =$padre->fin_real;
            $nodo_padres->indice =$padre->indice;
            $nodo_padres->nivel =$padre->nivel;
            if(intval($padre->resumen) === 0){
                $nodo_padres->resumen = 'SI';
            } else {
                $nodo_padres->resumen ='NO';
            }
            $nodo_padres->fin_real =$padre->fin_real;
            $nodo_padres->duracion =$padre->duracion;
            $nodo_padres->duracion_restante =$padre->duracion_restante;
            $nodo_padres->padre_id =$padre->padre_id;

            $nodo_padres->expend = true;
            $nodo_padres->color = $this->colores[0];

            $nodo_padres->orden = intval($padre->indice);
            // $nodo_padres->cantidad_ejercida = $this->getPresupuestoActividadReal_disponible_recursivo($padre->id, 0, $padre->id);
            $nodo_padres->cantidad_ejercida = number_format(floatval($data->cantidad_ejercida),2,'.',',');
            // number_format(floatval($data->cantidad_ejercida),2,'.',',');
            $nodo_padres->cantidad_comprometida = number_format(floatval($data->cantidad_comprometida),2,'.',',');
            $nodo_padres->cantidad_disponible = number_format(floatval($data->cantidad_disponible),2,'.',',');

            array_push($padres,$nodo_padres);
        }

        $actividades_nodos = $this->encuentraHijos($padres,$project);

            foreach($actividades_nodos as $elemento){
                fputcsv($fp, [
                    $elemento->nombre,
                    $elemento->avance,
                    $elemento->inicio,
                    $elemento->fin,
                    $elemento->costoCopia,
                    $elemento->presupuesto_inicial_copia,
                    $elemento->cantidad_ejercida,
                    $elemento->cantidad_disponible,
                    $elemento->cantidad_comprometida,
                    $elemento->fin_real,
                    $elemento->indice,
                    $elemento->resumen,
                    $elemento->duracion,
                    $elemento->duracion_restante,
                    ], ',');
                // var_dump($elemento);
                $this->toCSV($elemento->children,$fp);
            }
            rewind($fp);
            $output = stream_get_contents($fp);
            mb_convert_encoding($output, 'UCS-2LE', 'UTF-8');
            fclose($fp);

            $this->response->resetHeaders();
            $this->response->setHeader('Content-Type', 'application/csv; charset=utf-8');
            $this->response->setHeader('Content-Disposition', 'attachment; filename=Actividades-'."\"".$project_nombre."\"". '.csv');
            $this->response->setContent($output);
            $this->response->send();
    }

    private function toCSV ($hijos, $fp) {
        foreach($hijos as $elemento) {
            fputcsv($fp, [
                $elemento->nombre,
                $elemento->avance,
                $elemento->inicio,
                $elemento->fin,
                $elemento->costoCopia,
                $elemento->presupuesto_inicial_copia,
                $elemento->cantidad_ejercida,
                $elemento->cantidad_disponible,
                $elemento->cantidad_comprometida,
                $elemento->fin_real,
                $elemento->indice,
                $elemento->resumen,
                $elemento->duracion,
                $elemento->duracion_restante,
            ], ',');
            

            if(count($elemento->children)>0){
                // $actividad->cantidad_ejercida = '-------';
                // $actividad->cantidad_disponible = '-------';
                // $actividad->cantidad_ejercida = $this->getPresupuestoActividadReal_disponible_recursivo2($actividad->id, 1, 0);
                $this->toCSV($elemento->children,$fp);
            } else {
                // $actividad->cantidad_ejercida = $this->getPresupuestoActividadReal_disponible_recursivo2($actividad->id, 0, 0);
            }
        }
        
    }

    private function actualizaCantidades($actividades,$proyecto_id) {
        
        foreach($actividades as $actividad) {
            if(count($actividad->children) > 0){
                $this->actualizaCantidades($actividad->children,$proyecto_id);
            } else {
                if (count($actividad->children) > 0) {
                    $suma = 0;
                    foreach ($actividad->children as $hijo) {
                        $suma = $actividad->cantidad_ejercida + $suma;
                    }
                    $actividad->cantidad_ejercida = $suma;
                }
            }
        }
        return $actividades;
    }

    private function buscarHijos($padre_id,$proyecto_id) {
        $actividades_hijos = Actividades::find(
            [
                'proyecto_id = :proyecto_id: AND padre_id = :padre_id:',
                // 'order' => 'indice',
                'bind' => [
                    'proyecto_id' => $proyecto_id,
                    'padre_id' => $padre_id
                ]
            ]
        );

        $hijos = [];

        foreach($actividades_hijos as $hijo) {
            $data = $this->getPresupuestoActividadReal_disponible($hijo->id);
            $nodo_hijos=(object)array();
            $nodo_hijos->id = $hijo->id;
            $nodo_hijos->proyecto_id =$hijo->proyecto_id;
            $nodo_hijos->nombre =$hijo->nombre;
            $nodo_hijos->avance =$hijo->avance;
            $nodo_hijos->inicio =$hijo->inicio;
            $nodo_hijos->fin =$hijo->fin;
            $nodo_hijos->costo =$hijo->costo;
            $nodo_hijos->costoCopia =number_format(floatval($hijo->costo),2,'.',',');
            $nodo_hijos->presupuesto_inicial =$hijo->presupuesto_inicial;
            $nodo_hijos->presupuesto_inicial_copia =number_format(floatval($hijo->presupuesto_inicial),2,'.',',');
            $nodo_hijos->fin_real =$hijo->fin_real;
            $nodo_hijos->indice =$hijo->indice;
            $nodo_hijos->nivel =$hijo->nivel;
            if(intval($hijo->resumen) === 0){
                $nodo_hijos->resumen = 'SI';
            } else {
                $nodo_hijos->resumen ='NO';
            }
            $nodo_hijos->fin_real =$hijo->fin_real;
            $nodo_hijos->duracion =$hijo->duracion;
            $nodo_hijos->duracion_restante =$hijo->duracion_restante;
            $nodo_hijos->padre_id =$hijo->padre_id;

            $nodo_hijos->expend = true;
            $nodo_hijos->color = $this->colores[intval($hijo->nivel)-1];

            $numero_indice = intval(explode(".",$hijo->indice)[intval($hijo->nivel)-1]);
            $nodo_hijos->orden = $numero_indice;
            $nodo_hijos->cantidad_ejercida = number_format(floatval($data->cantidad_ejercida),2,'.',',');
            $nodo_hijos->cantidad_disponible = number_format(floatval($data->cantidad_disponible),2,'.',',');
            $nodo_hijos->cantidad_comprometida = number_format(floatval($data->cantidad_comprometida),2,'.',',');
            $nodo_hijos->finalizado = $hijo->finalizado;
            $nodo_hijos->visible = $hijo->visible;
            if ($data->cantidad_ejercida == 0) {
                $nodo_hijos->avance_monetario = 0;
            } else {
                $multi = $data->cantidad_ejercida * 100;
                if ($hijo->costo > 0) {
                    $nodo_hijos->avance_monetario = number_format(floatval($multi / $hijo->costo),2,'.',',');
                } else {
                    $nodo_hijos->avance_monetario = 0;
                }
                
            }
            if ($hijo->finalizado == true) {
                $nodo_hijos->avance_monetario = 100;
            }

            array_push($hijos,$nodo_hijos);
        }

        usort($hijos, function($a, $b) {
            return $a->orden > $b->orden ? 1 : -1;
        });

        return $hijos;

    }

    private function detect_sjis_win($filename){
        $file = null;
        if  (($file = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                foreach ($row as $r)  {
                    if(strtoupper(mb_detect_encoding($r, "ASCII, UTF-8, UNICODE, sjis-win, ISO-8859-1")) === strtoupper('SJIS-win')){
                        fclose($file);
                        return true;
                    }
                }
            }
            fclose($file);
        }
        return false;
    }

    private function detect_utf_8_bom($filename) {
        $str = file_get_contents($filename);
        $bom = pack("CCC", 0xef, 0xbb, 0xbf);
        if (0 === strncmp($str, $bom, 3)) {
            return true;
        }
        return false;
    }

    private function moverArchivo($nombre_archivo) {
        $upload_dir_origen = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/proyectoscarga_csv/';

        $upload_dir_destino = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/proyectos_csv/';

        if (!is_dir($upload_dir_destino))  {
            mkdir($upload_dir_destino, 0777);
        }

        copy($upload_dir_origen.$nombre_archivo.'.csv', $upload_dir_destino.$nombre_archivo.'.csv');
    }

    private function getNombreArchivo($carga,$proyecto_id){
        $numeros=[];
        if(count($carga)>0){
            foreach ($carga as $archivos){
                $numeros[]=intval(explode("_",$archivos->archivo)[2]);
            }
            $numero_archivo = max($numeros) + 1;
            if($numero_archivo<=9 && $numero_archivo>0){
                return 'archivocarga_'.$proyecto_id.'_0'.$numero_archivo;
            } else if($numero_archivo>9){
                return 'archivocarga_'.$proyecto_id.'_'.$numero_archivo;
            }
        } else {
            return 'archivocarga_'.$proyecto_id.'_01';
        }
    }

    public function uploadCsv ()
    {
        try {
            $content = $this->content;
            $isCsv = true;
            
            $request = $this->request->getPost();

            $cargas = Carga::find(
                [
                    'proyecto_id = :proyecto_id:',
                    'bind' => [
                        'proyecto_id' => $request['proyecto_id']
                    ]
                ]
            );
            $nombre_archivo = $this->getNombreArchivo($cargas,$request['proyecto_id']);

            if ($this->request->hasFiles())  {
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/proyectoscarga_csv/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777);
                }
                $fullPath = '';
                foreach ($this->request->getUploadedFiles() as $file) {
                    
                    if  ($file->getExtension() !== 'csv')  {
                        $isCsv = false;
                        break;
                    }
                    $fullPath = $upload_dir .$nombre_archivo.'.'.$file->getExtension();
                    if  (file_exists($fullPath))  {
                        @unlink($fullPath);
                    }
                    $file->moveTo($fullPath);
                }

                $file = null;

                $archivo_utf_8=$this->detect_utf_8_bom($fullPath);
                $archivo_sjis_win=$this->detect_sjis_win($fullPath);

                if($archivo_utf_8 || $archivo_sjis_win) {

                    if  (($file = fopen($fullPath, 'r')) !== FALSE) {
                        $csvData = [];
                        $filterData = [];
                        while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
                            $aux = [];

                            if($archivo_utf_8 && $archivo_sjis_win === false){
                                foreach ($row as $r)  {
                                    $aux[] = $r;
                                }
                                $csvData[] = $aux;
                            } else if ($archivo_sjis_win && $archivo_utf_8 === false) {
                                foreach ($row as $r)  {
                                    $aux[] = utf8_encode($r);
                                }
                                $csvData[] = $aux;
                            }
                        }
                        fclose($file);
                        array_shift($csvData);
                        
                        $rowNum = 2;
                        $content['err'] = '';

                        if(array_key_exists(0,$csvData)){
                            if(array_key_exists(10,$csvData[0])){
                                $content['err'] =  "No  es  un  .csv  válido, por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información.";
                            } else if(array_key_exists(9,$csvData[0])) {
                                foreach  ($csvData  as  $row)  {
                                    $_1 = trim($row[0]);
                                    $_2 = trim($row[1]);
                                    $_3 = trim($row[2]);
                                    $_4 = trim($row[3]);
                                    $_5 = trim($row[4]);
                                    $_6 = trim($row[5]);
                                    $_7 = trim($row[6]);
                                    $_8 = trim($row[7]);
                                    $_9 = trim($row[8]);
                                    $_10 = trim($row[9]);

                                    $auxToFilter  =  [
                                        'linea' => 0,
                                        'nombre' => '',  
                                        'avance' => 0,  
                                        'inicio' => '',  
                                        'fin' => '',  
                                        'costo' => 0,  
                                        'fin_real' => '',  
                                        'indice' => '',
                                        'nivel' => 0,
                                        'resumen' => 0,
                                        'duracion' => 0,
                                        'duracion_restante' => 0,
                                    ];
                                    $auxToFilter['linea'] = $rowNum;
                                    $auxToFilter['nombre'] = strtoupper($_1);
                                    if(strpos($_2,'%') === false){
                                        $auxToFilter['avance'] = intval($_2);
                                    } else {
                                        $auxToFilter['avance'] = intval(str_replace('%','',$_2));
                                    }

                                    //Fecha inicio
                                    
                                    $_3= str_replace('-','/',$_3);
                                    $_4= str_replace('-','/',$_4);

                                    if($_3 === ''){
                                        $content['err'] = 'El campo FECHA COMIENZO esta vacío C:{'.$rowNum.'}';
                                    } else if($this->detect_formato_fecha($_3)){
                                        $fecha_comienzo=$this->fecha_formato($_3);
                                        if($fecha_comienzo===-1){
                                            $content['err'] = 'Revise en la fila C:{'.$rowNum.'} el formato del campo de FECHA COMIENZO => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                        } else {
                                            if($_4 === ''){
                                                $content['err'] = 'El campo FECHA FIN esta vacío D:{'.$rowNum.'}';
                                            } else if($this->detect_formato_fecha($_4)){
                                                $fecha_fin=$this->fecha_formato($_4);
                                                if($fecha_fin===-1){
                                                    $content['err'] = 'Revise en la fila D:{'.$rowNum.'} el formato del campo de FECHA FIN => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                                } else {
                                                    if($fecha_comienzo<=$fecha_fin){
                                                        $auxToFilter['inicio'] = $fecha_comienzo;
                                                        $auxToFilter['fin'] = $fecha_fin;
                                                    } else {
                                                        $content['err'] = 'La fecha FIN es menor a la fecha COMIENZO, revise la fila D:{'.$rowNum.'}';
                                                    }
                                                }
                                            } else {
                                                $content['err'] = 'Revise en la fila D:{'.$rowNum.'} el formato del campo de FECHA FIN => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                            }
                                        }
                                    } else{
                                        $content['err'] = 'Revise en la fila C:{'.$rowNum.'} el formato del campo de FECHA COMIENZO => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                    }
                                    //$auxToFilter['inicio'] = DateTime::createFromFormat('d/m/y',explode(" ",$_3)[1])->format('Y/m/d');
                                    //$auxToFilter['fin'] = DateTime::createFromFormat('d/m/y',explode(" ",$_4)[1])->format('Y/m/d');

                                    if(strpos($_5,'$') === false) {
                                        if(floatval($_5)<=1000000000) {
                                            $auxToFilter['costo'] = floatval($_5);
                                        } else{
                                            $content['err'] = 'El valor máximo para el campo costo es $1000000000.00, revise la fila E:{'.$rowNum.'}';
                                        }
                                    } else {
                                        if(floatval($_5)<=1000000000) {
                                            $auxToFilter['costo'] = floatval(str_replace('$','',str_replace(',','',$_5)));
                                        } else {
                                            $content['err'] = 'El valor máximo para el campo costo es $1000000000.00, revise la fila E:{'.$rowNum.'}';
                                        }
                                    }

                                    //Fin real
                                    if($_6 === '' || strtoupper($_6) === strtoupper('NOD')){
                                        $auxToFilter['fin_real'] = null;
                                    } else if($this->detect_formato_fecha($_6)){
                                        $fecha_fin_real=$this->fecha_formato($_6);
                                        if($fecha_fin_real===-1){
                                            $content['err'] = 'Revise en la fila F:{'.$rowNum.'} el formato del campo de FIN REAL => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                        } else {
                                            if($fecha_fin_real>=$fecha_fin){
                                                $auxToFilter['fin_real'] = $fecha_fin_real;
                                            } else {
                                                $content['err'] = 'Revise en la fila F:{'.$rowNum.'}, la fecha debe ser mayor o igual a la fecha del campo FECHA FIN';
                                            }
                                        }
                                    } else {
                                        $content['err'] = 'Revise en la fila F:{'.$rowNum.'} el formato del campo de FIN REAL => Ejemplos de fechas válidas: vie 24/05/19, 24/05/19, vie 24/05/2019, 24/05/2019, vie 2019/05/24 y 2019/05/24';
                                    }

                                    if($this->validar_edt($_7)){
                                        if($this->existe_padre($_7,$csvData,$rowNum)){
                                            if($this->indice_edt($_7,$filterData)===false){
                                                $auxToFilter['indice'] = $_7;
                                                $auxToFilter['nivel'] = $this->getNivel($_7);
                                            } else {
                                                $content['err'] = 'Revise en la fila G:{'.$rowNum.'}, el valor ya existe en otra fila';
                                            }
                                        } else {
                                            $content['err'] = 'Revise en la fila G:{'.$rowNum.'}, este EDT no tiene su índice padre de jerarquía, revise que se encuentre en orden los EDT.';
                                        }
                                    } else {
                                        $content['err'] = 'Revise en la fila G:{'.$rowNum.'}, revise el EDT';
                                    }

                                    if((strtolower($_8) === strtolower('Sí')) || (strtolower($_8) === strtolower('Si'))) {
                                        $auxToFilter['resumen'] = 0;
                                    } else if (strtolower($_8) === strtolower('No')) {
                                        $auxToFilter['resumen'] = 1;
                                    } else {
                                        $content['err'] =  'Revise en la fila H:{'.$rowNum.'} las opciones válidas del campo Resumen son: Sí/Si ó No';
                                    }
                                    $auxToFilter['duracion'] = intval(explode(" ",$_9)[0]);
                                    $auxToFilter['duracion_restante'] = intval(explode(" ",$_10)[0]);

                                    $filterData[]  =  $auxToFilter;
                                    $rowNum++;
                                }
                            } else {
                                $content['err'] =  "Número de columnas incorrecto";
                            }
                        } else {
                            $content['err'] =  "No  es  un  .csv  válido, por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información.";
                        }
                        
                        
                        if  ($content['err']  ===  '')  {
                            $tx = $this->transactions->get();
                            $content['result']  =  '';

                            if  (count($filterData)  >  0)  {
                                $acts = Actividades::find(
                                    [
                                        'proyecto_id = :proyecto_id:',
                                        'bind' => [
                                            'proyecto_id' => $request['proyecto_id']
                                        ]
                                    ]
                                );

                                foreach($acts as $acti){
                                    $acti->setTransaction($tx);
                                    $acti->padre_id = null;
                                    if($acti->update()){
                                    } else {
                                        $content['error'] = Helpers::getErrors($acti);
                                        $content['message']  =  'Error al actualizar la tabla actividades';
                                        $tx->rollback();
                                    }
                                }

                                foreach($acts as $acti){
                                    $acti->setTransaction($tx);
                                    if($acti->delete()){
                                    } else {
                                        $content['error'] = Helpers::getErrors($acti);
                                        $content['message']  =  'Error al actualizar la tabla actividades';
                                        $tx->rollback();
                                    }
                                }

                                foreach  ($filterData  as  $actividad)  {
                                    $actividades =  new Actividades();
                                    $actividades->setTransaction($tx);

                                    $actividades->proyecto_id  =  $request['proyecto_id'];

                                    $actividades->nombre  =  $actividad['nombre'];
                                    $actividades->avance  =  $actividad['avance'];
                                    $actividades->inicio =  $actividad['inicio'];
                                    $actividades->fin =  $actividad['fin'];
                                    $actividades->costo =  $actividad['costo'];
                                    $actividades->presupuesto_inicial =  $actividad['costo'];
                                    $actividades->fin_real  =  $actividad['fin_real'];
                                    $actividades->indice  =  $actividad['indice'];
                                    $actividades->nivel  =  $actividad['nivel'];
                                    $actividades->resumen =  $actividad['resumen'];
                                    $actividades->duracion =  $actividad['duracion'];
                                    $actividades->duracion_restante =  $actividad['duracion_restante'];
                    
                                    if(intval($actividad['nivel']) === 1){
                                        $actividades->padre_id = null;
                                    } else {
                                        $actividades->padre_id = $this->getPadre($actividad['indice'],$request['proyecto_id']);
                                    }
                    
                                    if  ($actividades->create())  {
                                        $content['result']  =  'success';
                                    }  else  {
                                        $content['message']  =  'No  se  pudo  guardar  registro de actividades.';
                                        $content['error'] = Helpers::getErrors($actividades);
                                        $tx->rollback();
                                        
                                    }
                                }


                                //Esto es de la tabla detalles

                                if(count($cargas)>0){
                                    foreach ($cargas as $carga_id){
                                        $cargaD = CargaDetalles::find(
                                            [
                                                'carga_id = :carga_id:',
                                                'bind' => [
                                                    'carga_id' => $carga_id->id
                                                ]
                                            ]
                                        );
                                        foreach($cargaD as $detalle){
                                            $detalle->setTransaction($tx);
                                            if($detalle->delete()){
                                            }else{
                                                $content['error'] = Helpers::getErrors($detalle);
                                                $content['message']  =  'Error en la tabla pmo_carga_detalles. Reporte la falla a ANT Technologies, disculpe la molestia. ';
                                                $tx->rollback();
                                            }
                                        }
                                    }
                                }

                                $carga = new Carga();
                                $carga->setTransaction($tx);
                                $carga->proyecto_id  =  $request['proyecto_id'];
                                $carga->archivo = $nombre_archivo;
                                $fecha_carga = date("Y").'/'.date("m").'/'.date("d");
                                $carga->fecha = date("Y/m/d", strtotime($fecha_carga));
                                

                                if($carga->create()){
                                    foreach  ($filterData  as  $a)  {
                                        $carga_detalles =  new CargaDetalles();
                                        $carga_detalles->setTransaction($tx);
                                        $carga_detalles->carga_id  =  $carga->id;
                                        $carga_detalles->linea  =  $a['linea'];
                                        $carga_detalles->nombre  =  $a['nombre'];
                                        $carga_detalles->avance  =  $a['avance'];
                                        $carga_detalles->inicio =  $a['inicio'];
                                        $carga_detalles->fin =  $a['fin'];
                                        $carga_detalles->costo =  $a['costo'];
                                        $carga_detalles->fin_real  =  $a['fin_real'];
                                        $carga_detalles->indice  =  $a['indice'];
                                        $carga_detalles->nivel  =  $a['nivel'];
                                        $carga_detalles->resumen =  $a['resumen'];
                                        $carga_detalles->duracion =  $a['duracion'];
                                        $carga_detalles->duracion_restante =  $a['duracion_restante'];

                                        if  ($carga_detalles->create())  {
                                            //////////////
                                            ////////////////
                                        }  else  {
                                            $tx->rollback();
                                            $content['message']  =  'No  se  pudo  guardar  registro.';
                                            $content['error'] = Helpers::getErrors($carga_detalles);
                                        }
                                    }
                                } else {
                                    $content['error'] = Helpers::getErrors($carga);
                                    $content['message']  =  'Error en la tabla pmo_carga. Reporte la falla a ANT Technologies, disculpe la molestia. ';
                                    $tx->rollback();
                                }
                                    //$tx->commit();

                                if ($content['result']==='success') {
                                    $this->moverArchivo($nombre_archivo);
                                    /////////////////////////////////////
                                    $project = Proyecto::findFirst($request['proyecto_id']);
                                    $project_id = $project->id;
                                    $logs = new Logs();
                                    $logs->setTransaction($tx);
                                    $validUser = Auth::getUserData($this->config);
                                    $logs->account_id = $validUser->user_id;
                                    $logs->level_id = 9;
                                    $logs->log = 'CARGÓ ACTIVIDADES AL PROJECT: ' . $project->nombre;
                                    $logs->fecha = date("Y-m-d H:i:s");
                                    if($logs->create()){
                                        $sql = "(SELECT coalesce(sum(pmo_actividades.costo),0) as presupuesto_actividad_principal from pmo_actividades where nivel = '1' and proyecto_id = $project_id)";
                                        $data = $this->db->query($sql)->fetchAll();
                                        $project->setTransaction($tx);
                                        if (count($data) > 0) {
                                            $project->presupuesto_actual = $data[0]['presupuesto_actividad_principal'];
                                        } else {
                                            $project->presupuesto_actual = 0;
                                        }
                                        if ($project->update()) {
                                            $content['result'] = 'success';
                                            $content['message']  =  'Se  han  cargado  todas  las  actividades.';
                                            $content['presupuesto'] = $project->presupuesto_actual;
                                            $tx->commit();
                                        } else {
                                            $tx->rollback();
                                            $content['message']  =  'No  se  pudo  guardar  registro.';
                                            $content['error'] = Helpers::getErrors($project);
                                        }
                                    } else {
                                        $tx->rollback();
                                        $content['message']  =  'No  se  pudo  guardar  registro.';
                                        $content['error'] = Helpers::getErrors($logs);
                                    }
                                    
                                    /////////////////////////////////////
                                    // $content['message']  =  'Se  han  cargado  todas  las  actividades.';
                                    // $tx->commit();
                                }

                            } else  {
                                $content['message']  =  'Sin  datos  a  insertar.';                                                
                            }
                        }
                        if  (!$isCsv)  {
                            $content['message']  =  'No  es  un  .csv  válido!';
                        }
                    }
                } else {
                    $content['err'] =  "No  es  un  .csv  válido, por favor guarde el archivo .csv con formato UTF-8, con el objetivo de no perder información.";
                }
            } 
        }catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    private function getPadre($edt,$proyecto_id){
        $indice = explode(".",$edt);

        $indice_padre = $this->getIndicePadre($indice);

        $padre_id = Actividades::findFirst(
            [
                'indice = :indice: AND proyecto_id = :proyecto_id:',
                'bind' => [
                    'indice' => $indice_padre,
                    'proyecto_id' => $proyecto_id
                ]
            ]
        );

        return $padre_id->id;
    }

    private function getIndicePadre($arreglo){
        $indice = '';
        $tamanio = count($arreglo)-1;
        for($i = 0;$i<$tamanio;$i++){
            if($i === ($tamanio-1)){
                $indice .= $arreglo[$i];
            } else {
                $indice .= $arreglo[$i] . '.';
            }
        }
        return $indice;
    }

    private function getNivel ($indice) {
        $nivel = explode(".",$indice);
        return count($nivel);
    }

    private function validar_edt($texto){
        if (preg_match("/^[0-9]+([.]{1}[0-9]+)*$/", $texto)===1){
            return true;
        } 
        return false;
    }

    private function indice_edt($edt_actual,$array){
        foreach($array as $elemento){
            if($edt_actual === $elemento['indice']){
                return true;
            }
        }
        return false;        
    }

    private function existe_padre($edt_actual,$array,$maximo){
        $maximo = $maximo-1;
        $array_edt = explode(".",$edt_actual);
        if($this->getNivel($edt_actual)===1) {
            return true;
        } else {
            $padre = $this->getIndicePadre($array_edt);
            for($i = 0;$i<$maximo;$i++){
                if($padre===$array[$i][6]){
                    return true;
                }
            }
        }
        return false;
    }

    private function fecha_formato($text){
        $formato = explode(' ',$text);
        $tamanio=count($formato);
        if($tamanio===2){
            $separacion_fecha=$this->separar_fecha($formato[1]);
            if($separacion_fecha[0]===2 && $separacion_fecha[2] ===2){
                return DateTime::createFromFormat('d/m/y',$formato[1])->format('Y/m/d');
            } else if ($separacion_fecha[0]===4 && $separacion_fecha[2] ===2){
                return DateTime::createFromFormat('Y/m/d',$formato[1])->format('Y/m/d');
            } else if ($separacion_fecha[0]===2 && $separacion_fecha[2] ===4){
                return DateTime::createFromFormat('d/m/Y',$formato[1])->format('Y/m/d');
            }
        } else if ($tamanio === 1){
            $separacion_fecha=$this->separar_fecha($formato[0]);
            if($separacion_fecha[0]===2 && $separacion_fecha[2] ===2){
                return DateTime::createFromFormat('d/m/y',$formato[0])->format('Y/m/d');
            } else if ($separacion_fecha[0]===4 && $separacion_fecha[2] ===2){
                return DateTime::createFromFormat('Y/m/d',$formato[0])->format('Y/m/d');
            } else if ($separacion_fecha[0]===2 && $separacion_fecha[2] ===4){
                return DateTime::createFromFormat('d/m/Y',$formato[0])->format('Y/m/d');
            }
        }
        return -1;
    }

    private function detect_formato_fecha ($texto){
        $formato = explode(' ',$texto);
        $tamanio=count($formato);
        if($tamanio===2){
            if($this->detect_fecha_simbolo($formato[1])){
                return $this->validar_valores_fecha($formato[1]);
            } else {
                return false;
            }
        } else if ($tamanio === 1){
            if($this->detect_fecha_simbolo($formato[0])){
                return $this->validar_valores_fecha($formato[0]);
            } else {
                return false;
            }
        }
    }

    private function validar_valores_fecha($texto){
        $fechaS = explode('/',$texto);
        

        $separacion_fecha=$this->separar_fecha($texto);
        if($separacion_fecha[0]===2 && $separacion_fecha[2] ===2){
            return checkdate($fechaS[1], $fechaS[0], $fechaS[2]);
        } else if ($separacion_fecha[0]===4 && $separacion_fecha[2] ===2){
            return checkdate($fechaS[1], $fechaS[2], $fechaS[0]);
        } else if ($separacion_fecha[0]===2 && $separacion_fecha[2] ===4){
            return checkdate($fechaS[1], $fechaS[0], $fechaS[2]);
        }
        return false;
    }

    private function detect_fecha_simbolo($texto){
        $cantidad = 0;
        for($i=0;$i<strlen($texto);$i++){ 
            if(substr($texto,$i,1)==='/'){
                $cantidad = $cantidad + 1;
            }
        }

        if($cantidad === 2){
            return true;
        } else {
            return false;
        }
    }

    private function separar_fecha ($texto) {
        $fechaS = explode('/',$texto);
        $tamanio = [];
        
        foreach($fechaS as $digitos){
            $tamanio[]=strlen($digitos);
        }
        return $tamanio;
    }

    public function update_actividad ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $actividad = Actividades::findFirst($request['id']);
            $data = $this->getPresupuestoActividadReal_disponible($actividad->id);
            $proyecto_id_modificar = $actividad->proyecto_id;
            $monto_actual = Proyecto::findFirst($actividad->proyecto_id)->presupuesto_actual;
            /* var_dump($data);
            var_dump($request['costo_validado']); */
            if (floatval($data->cantidad_ejercida) > floatval($request['costo_validado'])) {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'La cantidad de la actividad no debe ser menor a la ejercida'];
            } else {
                if ($actividad !== false) {
                    $actividad->setTransaction($tx);
                    $actividad->nombre = trim(strtoupper($request['nombre']));
                    $actividad->avance = floatval($request['avance']);
                    if($request['inicio'] === ""){
                        $actividad->inicio = null;
                    } else {
                        $actividad->inicio = date("Y/m/d", strtotime($request['inicio']));
                    }
                    if($request['fin'] === ""){
                        $actividad->fin = null;
                    } else {
                        $actividad->fin = date("Y/m/d", strtotime($request['fin']));
                    }
                    $costo = $actividad->costo;
                    $id = $actividad->padre_id;
                    $nivel = $actividad->nivel;
                    //
                    $id_2 = $actividad->padre_id;
                    //

                    $actividad->costo = $request['costo_validado'];
                    /*
                        Por definir si se puede editar el presupuesto_inicial
                    */
                        //print_r($request);

                    if($request['fin_real'] === ""){
                        $actividad->fin_real = null;
                    } else {
                        $actividad->fin_real = date("Y/m/d", strtotime($request['fin_real']));
                    }
                    if($request['resumen'] === 'true'){
                        $actividad->resumen = 0;
                    } else {
                        $actividad->resumen = 1;
                    }
                    $actividad->duracion = intval($request['duracion']);
                    $actividad->duracion_restante = intval($request['duracion_restante']);
                    
                    if ($actividad->update()) {
                        $this->content['result'] = 'success';
                        $accion = 'nada';
                        if ($costo < $request['costo_validado']) {
                            $diferencia = $request['costo_validado'] - $costo;
                            $accion = 'suma';
                        } 
                        if ($costo > $request['costo_validado']) {
                            $diferencia = $costo - $request['costo_validado'];
                            $accion = 'resta';
                        }
                        if ($costo === $request['costo_validado']) {
                            $accion = 'nada';
                        }
                        // print_r($accion);
                        $project_id = 0;
                        if ($accion != 'nada') {
                            for ($i=$nivel; $i>1; $i--) {
                                $actividad_padre = Actividades::findFirst($id);
                                if ($actividad_padre !== false) {
                                    $actividad_padre->setTransaction($tx);
                                    if ($accion === 'suma') {
                                        $actividad_padre->costo = $actividad_padre->costo + $diferencia;
                                    } else {
                                        $actividad_padre->costo = $actividad_padre->costo - $diferencia;
                                        $project_id = $actividad_padre->proyecto_id;
                                    }
                                    if ($actividad_padre->update()) {
                                        $this->content['result'] = 'success';
                                        $id = $actividad_padre->padre_id;
                                    } else {
                                        $this->content['error'] = Helpers::getErrors($actividad_padre);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                                        $tx->rollback();
                                    }
                                }
                            }
                            // var_dump($project_id);
                            $projectAModificar = Proyecto::findFirst($proyecto_id_modificar);
                            if ($projectAModificar !== false) {
                                $projectAModificar->setTransaction($tx);
                                if ($accion === 'suma') {
                                    $monto_actual = $projectAModificar->presupuesto_actual + $diferencia;
                                    $projectAModificar->presupuesto_actual = $monto_actual;
                                } else {
                                    $monto_actual = $projectAModificar->presupuesto_actual - $diferencia;
                                    $projectAModificar->presupuesto_actual = $monto_actual;
                                }
                                if ($projectAModificar->update()) {
                                    $this->content['result'] = 'success';
                                    // $monto_actual = $projectAModificar->presupuesto_actual;
                                    // var_dump($monto_actual);
                                    // var_dump($monto_actual + $diferencia);
                                } else {
                                    $this->content['error'] = Helpers::getErrors($projectAModificar);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el proyecto'];
                                    $tx->rollback();
                                }
                            } 
                        }
                        /* for ($i=$nivel; $i>1; $i--) {
                            $data = $this->calcular_avance($id_2);
                            $actividad_padre = Actividades::findFirst($id_2);
                            if ($actividad_padre !== false) {
                                $actividad_padre->setTransaction($tx);
                                if (intval($data[0]['sum']) > 0) {
                                    $actividad_padre->avance = round(($data[0]['sum']*100) / ($data[0]['count'] * 100),0);
                                } else {
                                    $actividad_padre->avance = 0;
                                }
                                if ($actividad_padre->update()) {
                                    $this->content['result'] = 'success';
                                    $id_2 = $actividad_padre->padre_id;
                                } else {
                                    $this->content['error'] = Helpers::getErrors($actividad_padre);
                                    // $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                                    // $tx->rollback();
                                }
                            }
                        } */
                                    if ($accion != 'nada') {
                                        $logs = new Logs();
                                        $logs->setTransaction($tx);
                                        $validUser = Auth::getUserData($this->config);
                                        $logs->account_id = $validUser->user_id;
                                        $logs->level_id = 12;
                                        $costo_inicial = number_format(floatval($costo),2,'.',',');
                                        $costo_modificado = number_format(floatval($request['costo_validado']),2,'.',',');
                                        $nombre_project = Proyecto::findFirst($actividad->proyecto_id)->nombre;
                                        $logs->log = "CAMBIÓ EL PRESUPUESTO DE LA ACTIVIDAD PERTENECIENTE AL PROJECT '" .$nombre_project."' CON ID #" . $actividad->id ." DE $" . $costo_inicial . " A $" . $costo_modificado;
                                        $logs->fecha = date("Y-m-d H:i:s");
                                        if($logs->create()){
                                          $this->content['result'] = 'success';
                                        } else {
                                          $this->content['result'] = 'success';
                                          $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad.'];
                                          $tx->commit();
                                        }
                                    }
                        if ($this->content['result'] === 'success') {
                            $this->content['result'] = 'success';
                            $this->content['monto_actual'] = $monto_actual;
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la actividad'];
                            $tx->commit();
                        }
                                    
                                    
                    } else {
                        $this->content['error'] = Helpers::getErrors($actividad);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha encontrado esta actividad para editar.'];
                }
            }
            
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update_avance ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $actividad = Actividades::findFirst($request['id']);
            if ($actividad !== false) {
                $actividad->setTransaction($tx);
                $actividad->avance = floatval($request['avance']);
                if ($actividad->update()) {
                    $id = $actividad->padre_id;
                    $nivel = $actividad->nivel;
                    for ($i=$nivel; $i>1; $i--) {
                        $data = $this->calcular_avance($id);
                        $actividad_padre = Actividades::findFirst($id);
                        if ($actividad_padre !== false) {
                            $actividad_padre->setTransaction($tx);
                            if (intval($data[0]['sum']) > 0) {
                                $actividad_padre->avance = round(($data[0]['sum']*100) / ($data[0]['count'] * 100),0);
                            } else {
                                $actividad_padre->avance = 0;
                            }
                            if ($actividad_padre->update()) {
                                $this->content['result'] = 'success';
                                $id = $actividad_padre->padre_id;
                            } else {
                                $this->content['error'] = Helpers::getErrors($actividad_padre);
                                // $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                                // $tx->rollback();
                            }
                        }
                    }
                    if ($this->content['result'] === 'success') {
                        $this->content['result'] = 'success';
                        $this->content['message'] = ['title' => '¡Éxito!', 'content' => 'Se ha actualizado la actividad'];
                        $tx->commit();
                    } else {
                        $this->content['error'] = 'error';
                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                        $tx->rollback();
                    }
                } else {
                    $this->content['error'] = Helpers::getErrors($actividad);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha encontrado esta actividad para editar.'];
            }
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    private function calcular_avance ($id_actividad) {
        $sql_hijos = "SELECT sum(avance), count(*) from pmo_actividades where padre_id = ".$id_actividad;
        $hijos = $this->db->query($sql_hijos)->fetchAll();
        return $hijos;
    }

    public function updateFinalizado () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $actividad = Actividades::findFirst($request['id']);
            if ($actividad !== false) {
                $actividad->setTransaction($tx);
                if ($actividad->finalizado == "true") {
                    $actividad->finalizado = false;
                } else {
                    $actividad->finalizado = true;
                }

                if ($actividad->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividad);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha encontrado la actividad para editar.'];
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function updateVisible () {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $actividad = Actividades::findFirst($request['id']);
            if ($actividad !== false) {
                $actividad->setTransaction($tx);
                if ($actividad->visible == "true") {
                    $actividad->visible = false;
                } else {
                    $actividad->visible = true;
                }

                if ($actividad->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividad);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar la actividad'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha encontrado la actividad para editar.'];
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function update_costo ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPut();

            $actividad = Actividades::findFirst($request['id']);
            if ($actividad !== false) {
                $actividad->setTransaction($tx);
                $actividad->costo = floatval($actividad->costo + $request['costo']);

                if ($actividad->update()) {
                    $this->content['result'] = 'success';
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha actualizado el presupuesto de la actividad'];
                    $tx->commit();
                } else {
                    $this->content['error'] = Helpers::getErrors($actividad);
                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo actualizar el presupuesto de la actividad'];
                    $tx->rollback();
                }
            } else {
                $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se ha encontrado la actividad para editar.'];
            }
            
        } catch (Throwable $e) {
            $this->content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function delete_actividad ()
    {
        try {
            $tx = $this->transactions->get();
            
            $request = $this->request->getPost();

            $padre = Actividades::findFirst(end($request['arrayActividades']));

            if ($request['arrayActividades']) {
                foreach ($request['arrayActividades'] as $elemento){
                    $actividad = Actividades::findFirst($elemento);
                    $actividad->setTransaction($tx);

                    if ($actividad->delete()) {
                        $this->content['result'] = 'success';
                    } else {
                        $this->content['error'] = Helpers::getErrors($actividad);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                        $tx->rollback();
                    }
                }
                
                if ($this->content['result'] === 'success') {
                    $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado la actividad.'];
                    if($padre) {
                        if($padre->padre_id === null){
                            $registros = Actividades::find(
                                [
                                    'proyecto_id = :proyecto_id: AND padre_id IS NULL',
                                    'bind' => [
                                        'proyecto_id' => $padre->proyecto_id
                                    ]
                                ]
                            );
                            $actividades_padre = [];

                            foreach($registros as $padre){
                                $nodo_hijos=(object)array();
                                $nodo_hijos->id = $padre->id;
                                $nodo_hijos->proyecto_id =$padre->proyecto_id;
                                $nodo_hijos->indice =$padre->indice;
                                $nodo_hijos->nivel =$padre->nivel;
                                $nodo_hijos->padre_id =$padre->id;
                                $nodo_hijos->padre_indice = $padre->indice;
                                $numero_indice = intval(explode(".",$padre->indice)[intval($padre->nivel)-1]);
                                $nodo_hijos->orden = $numero_indice;
                                array_push($actividades_padre,$nodo_hijos);
                            }

                            usort($actividades_padre, function($a, $b) {
                                return $a->orden > $b->orden ? 1 : -1;
                            });

                            $numeracion = 1;
                            foreach($actividades_padre as $registro){
                                $actividad = Actividades::findFirst($registro->id);
                                $actividad->indice = $numeracion++;
                                $actividad->update();
                            }
                            $this->encuentraHijos2($actividades_padre,$padre->proyecto_id);

                        } else {
                            $registros = Actividades::find(
                                [
                                    'proyecto_id = :proyecto_id: AND padre_id = :padre_id:',
                                    'bind' => [
                                        'proyecto_id' => $padre->proyecto_id,
                                        'padre_id' => $padre->padre_id
                                    ]
                                ]
                            );

                            $actividades_padre = [];

                            foreach($registros as $padre){
                                $nodo_hijos=(object)array();
                                $nodo_hijos->id = $padre->id;
                                $nodo_hijos->proyecto_id =$padre->proyecto_id;
                                $nodo_hijos->indice =$padre->indice;
                                $nodo_hijos->nivel =$padre->nivel;
                                $nodo_hijos->padre_id =$padre->padre_id;
                                $indice_padre = Actividades::findFirst($padre->padre_id);
                                $nodo_hijos->padre_indice = $indice_padre->indice;
                                $numero_indice = intval(explode(".",$padre->indice)[intval($padre->nivel)-1]);
                                $nodo_hijos->orden = $numero_indice;
                                array_push($actividades_padre,$nodo_hijos);
                            }

                            usort($actividades_padre, function($a, $b) {
                                return $a->orden > $b->orden ? 1 : -1;
                            });
                            $numeracion = 1;
                            foreach($actividades_padre as $registro){
                                $actividad = Actividades::findFirst($registro->id);
                                $actividad->indice = $registro->padre_indice.'.'.$numeracion++;
                                $actividad->update();
                            }
                            $this->encuentraHijos2($actividades_padre,$padre->proyecto_id);
                        }
                    }
                    $tx->commit();
                }

                // $this->autocorregir_edt(end($request['arrayActividades']));
                
                $this->content['resulta'] =$registros;
            }
        } catch (Throwable $e) {
            $this->$content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }
        
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }


    private function encuentraHijos2($actividades,$proyecto_id) {
        foreach($actividades as $actividad) {
            $children = $this->buscarHijos2($actividad->id,$proyecto_id);

            if(count($children)>0){
                $this->encuentraHijos2($children,$proyecto_id);
            }
        }
    }

    private function buscarHijos2($padre_id,$proyecto_id) {
        $actividades_hijos = Actividades::find(
            [
                'proyecto_id = :proyecto_id: AND padre_id = :padre_id:',
                'bind' => [
                    'proyecto_id' => $proyecto_id,
                    'padre_id' => $padre_id
                ]
            ]
        );

        $hijos = [];

        foreach($actividades_hijos as $hijo){
            $nodo_hijos=(object)array();
            $nodo_hijos->id = $hijo->id;
            $nodo_hijos->proyecto_id =$hijo->proyecto_id;
            $nodo_hijos->indice =$hijo->indice;
            $nodo_hijos->nivel =$hijo->nivel;
            $nodo_hijos->padre_id =$hijo->padre_id;
            $indice_padre = Actividades::findFirst($hijo->padre_id);
            $nodo_hijos->padre_indice = $indice_padre->indice;
            $numero_indice = intval(explode(".",$hijo->indice)[intval($hijo->nivel)-1]);
            $nodo_hijos->orden = $numero_indice;
            array_push($hijos,$nodo_hijos);
        }

        usort($hijos, function($a, $b) {
            return $a->orden > $b->orden ? 1 : -1;
        });

        $numeracion = 1;
        foreach($hijos as $registro){
            $actividad = Actividades::findFirst($registro->id);
            $actividad->indice = $registro->padre_indice.'.'.$numeracion++;
            $actividad->update();
        }

        return $hijos;

    }

    public function uploadArchivo()
    {
        try {
            $content = $this->content;
            $request = $this->request->getPost();
            $tx = $this->transactions->get();


            if ($this->request->hasFiles()) {
                
                //$upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/expedientes/licitaciones/';
                $upload_dir = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/actividades/';
                if (!is_dir($upload_dir))  {
                    mkdir($upload_dir, 0777,true);
                    chmod($upload_dir, 0777);
                }

                foreach ($this->request->getUploadedFiles() as $file) {
                    $sizeA=($file->getSize()/1000000);
                    if($sizeA>100||$sizeA===0){
                        $content['err'] = 'Archivo demasiado grande';
                    }else{
                        if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'||strtolower($file->getExtension())==='png'||strtolower($file->getExtension())==='pdf' || strtolower($file->getExtension())==='xml' || strtolower($file->getExtension())==='docx'){
                            
                            $img = $request['nombre'];
                            $nombre = $request['nombre'];

                            if(intval($request['formato_requisito_id']) === 0){

                                $documento = new SysDocuments();
                                $documento->setTransaction($tx);
                                $validUser = Auth::getUserData($this->config);
                                $documento->account_id = $validUser->account_id;
                                $documento->doc_type = $file->getExtension();
                                $documento->name=$img;

                                if ($documento->create()) {
                                    $id = $documento->id;
                                    $nombre_con_id = $id .'.'. $file->getExtension();
                                    // aqui empieza lo de guardar el documento con el numero de id
                                    foreach (glob($upload_dir.$nombre_con_id.'.*') as $nombre_fichero) {
                                    unlink($nombre_fichero);
                                    }
                                    $file->moveTo($upload_dir . $nombre_con_id);
                                    if (file_exists($upload_dir . $nombre_con_id)) {
                                        chmod($upload_dir . $nombre_con_id, 0777);
                                    }
                                    if(strtolower($file->getExtension())==='jpg'||strtolower($file->getExtension())==='jpeg'){
                                        $path=$upload_dir . $nombre_con_id;
                                        $this->orientation($path);
                                    }

                                    $documentos_actividades = new DocumentosActividades();
                                    $documentos_actividades->setTransaction($tx);
                                    $documentos_actividades->documento_id = $id;
                                    $documentos_actividades->actividad_id = intval($request['actividad_id']);
                                        if ($documentos_actividades->create()) {
                                            $content['result'] = 'success';
                                            $content['message'] = ['title' => 'Éxito', 'content' => 'Se ha guardado el archivo.'];
                                            $tx->commit();
                                        } else {
                                        $this->content['error'] = Helpers::getErrors($documentos_actividades);
                                        $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo agregar el archivo al registro de la actividad.'];
                                        $tx->rollback();
                                        }
                                } else {
                                    $this->content['error'] = Helpers::getErrors($documento);
                                    $this->content['message'] = ['title' => '¡Error!', 'content' => 'No se pudo guardar el archivo'];
                                    $tx->rollback();
                                }

                            }
                        } else {
                            $this->content['message'] = ['title' => '¡Error!', 'content' => 'Sólo archivos con extensión .jpg, .jpeg, .png, .xml y .pdf'];
                        }
                    }
                }
            } else {
                $content['message'] = 'No hay archivos.';
            }
        } catch (Throwable $e) {
            $content['errors'] = get_class($e) . ": {$e->getMessage()} ({$e->getCode()})" . PHP_EOL;
        }

        $this->response->setJsonContent($content);
        $this->response->send();
    }

    private function orientation($ruta) {
        try {
            $exif = @exif_read_data($ruta);
            $image = imagecreatefromjpeg($ruta);
            if(isset($exif) && array_key_exists('Orientation',$exif)) {
                switch($exif['Orientation']){
                    case 8:
                        $image = imagerotate($image,90,0);
                        break;
                    case 3:
                        $image = imagerotate($image,180,0);
                        break;
                    case 6:
                        $image = imagerotate($image,-90,0);
                        break;
                }
            }
            imagejpeg($image, $ruta);
        } catch (Exception $e){
        }
    }

    public function getFilesByActividad ($id_actividad) {
        $sql = "SELECT pmo_actividades_documents.id, pmo_actividades_documents.documento_id, pmo_actividades_documents.actividad_id,
                sys_documents.name
                FROM pmo_actividades_documents
                inner join sys_documents on sys_documents.id = pmo_actividades_documents.documento_id
                where pmo_actividades_documents.actividad_id = $id_actividad";
        $files = $this->db->query($sql)->fetchAll();
        $this->content['files'] = $files;
        $this->content['result'] = 'success';
        $this->response->setJsonContent($this->content);
        $this->response->send();
    }

    public function getFile($id)
    {    
        $documento = SysDocuments::findFirst($id);
        $path = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/actividades/' .$documento->id.'.'.$documento->doc_type;
        $filetype = mime_content_type($path);   
        $filesize = filesize($path);       
        $response = new \Phalcon\Http\Response();
        $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
        $response->setHeader("Content-Length", $filesize);
        $response->setContentType($filetype);
        $response->setFileToSend($path, $documento->name);
        $response->send();
        return $response;
    }

    public function deleteFormatoActividad ()
    {
        try {
            $tx = $this->transactions->get();
            $request = $this->request->getPost();
            if ($request['id']) {
                    $documento_actividad = DocumentosActividades::findFirst($request['id']);
                    $documento_actividad->setTransaction($tx);

                    if ($documento_actividad->delete()) {
                        $this->content['result'] = 'success';

                        if($documento_actividad->documento_id !== null) {
                            $id = $documento_actividad->documento_id;
                            $documentos = SysDocuments::findFirst(intval($id));
                            $documentos->setTransaction($tx);
                            $nombre_fichero = $_SERVER["DOCUMENT_ROOT"] . '/public/assets/actividades/'.$documentos->id.'.'.$documentos->doc_type;
                            unlink($nombre_fichero);
                            if ($documentos->delete()) {
                                $this->content['result'] = 'success';
                                $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el archivo.'];
                                $tx->commit();
                            } else {
                                $this->content['error'] = Helpers::getErrors($documentos);
                                $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
                                $tx->rollback();
                            }
                        } else {
                            $this->content['result'] = 'success';
                            $this->content['message'] = ['title' => '¡Exito!', 'content' => 'Se ha eliminado el archivo.'];
                            $tx->commit();
                        }
                    } else {
                        $this->content['error'] = Helpers::getErrors($documento_actividad);
                        $this->content['message'] = ['title' => '¡Error!', 'content' => $this->content['error'][1]];
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