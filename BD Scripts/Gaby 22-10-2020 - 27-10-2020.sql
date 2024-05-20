DROP FUNCTION public.get_existencias(bigint, bigint, text, text);
DROP FUNCTION public.getkardex(bigint, bigint, text, text);
DROP FUNCTION public.getproductos_by_almacen(bigint, bigint);
DROP FUNCTION public.getsaldos(bigint, bigint, text, text);
DROP VIEW public.vista_productos;
DROP VIEW public.vista_saldos;

ALTER TABLE public.inv_bom
    ALTER COLUMN cantidad TYPE numeric(15, 3);
ALTER TABLE public.inv_movimientos_detalles
    ALTER COLUMN cantidad TYPE numeric(15, 3);
ALTER TABLE public.pro_ordenes_detalles
    ALTER COLUMN cantidad TYPE numeric(15, 3);
ALTER TABLE public.inv_movimientos
    ADD COLUMN remision_id bigint;

ALTER TABLE public.inv_movimientos
    ADD COLUMN orden_id bigint;

-- View: public.vista_productos

-- DROP VIEW public.vista_productos;

CREATE OR REPLACE VIEW public.vista_productos
 AS
 SELECT inv_productos.id,
    inv_tipos_articulos.codigo AS codigo_categoria,
    inv_lineas.codigo AS codigo_linea,
    inv_productos.codigo AS codigo_producto,
    inv_productos.nombre,
    inv_tipos_articulos.nombre AS categoria,
    inv_lineas.nombre AS linea,
    inv_tipos_presentaciones.nombre AS presentacion,
    0.00 AS existencia
   FROM inv_productos
     JOIN inv_tipos_articulos ON inv_tipos_articulos.id = inv_productos.tipo_id
     JOIN inv_lineas ON inv_lineas.id = inv_productos.linea_id
     JOIN inv_tipos_presentaciones ON inv_tipos_presentaciones.id = inv_productos.presentacion_id
  ORDER BY inv_productos.nombre;

ALTER TABLE public.vista_productos
    OWNER TO postgres;

-------------------------------------------


-- View: public.vista_saldos

-- DROP VIEW public.vista_saldos;

CREATE OR REPLACE VIEW public.vista_saldos
 AS
 SELECT inv_movimientos.id,
    inv_movimientos.tipo_id,
    inv_movimientos.folio,
    inv_movimientos.almacen_id,
    inv_movimientos.empresa_id,
    inv_movimientos.status,
    inv_almacenes.nombre AS almacen,
    inv_tipos_movimientos.nombre AS movimiento,
    inv_movimientos_detalles.cantidad,
    inv_movimientos_detalles.costo,
    inv_movimientos_detalles.producto_id,
    inv_productos.nombre AS producto,
    inv_movimientos_detalles.created,
    inv_movimientos_detalles.cantidad AS existencia,
    inv_movimientos.orden_id,
    inv_movimientos.remision_id
   FROM inv_movimientos
     JOIN inv_almacenes ON inv_almacenes.id = inv_movimientos.almacen_id
     JOIN inv_tipos_movimientos ON inv_tipos_movimientos.id = inv_movimientos.tipo_id
     JOIN inv_movimientos_detalles ON inv_movimientos_detalles.movimiento_id = inv_movimientos.id
     JOIN inv_productos ON inv_productos.id = inv_movimientos_detalles.producto_id
  ORDER BY inv_almacenes.nombre, inv_productos.nombre, inv_movimientos.id, inv_tipos_movimientos.nombre;

ALTER TABLE public.vista_saldos
    OWNER TO postgres;

----------------------------------------

-- FUNCTION: public.get_existencias(bigint, bigint, text, text)

-- DROP FUNCTION public.get_existencias(bigint, bigint, text, text);

CREATE OR REPLACE FUNCTION public.get_existencias(
  _almacen bigint,
  _producto bigint,
  fecha_inicio text,
  fecha_fin text)
    RETURNS SETOF vista_productos 
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
    ROWS 1000
AS $BODY$DECLARE

  fila_detalle RECORD;

  fila_productos RECORD;

  id_producto numeric;

  id_almacen numeric;

  saldo numeric;

 BEGIN

 saldo = 0.00;

 id_producto = 0;

 id_almacen = 0;

 FOR fila_productos IN (select * from getproductos_by_almacen(_almacen, _producto)) LOOP

   saldo = 0.00;

   FOR fila_detalle IN (select * from getkardex(_almacen, fila_productos.id, fecha_inicio, fecha_fin)) LOOP

    IF (id_producto != fila_detalle.producto_id OR id_almacen != fila_detalle.almacen_id) THEN

      saldo = 0.00;

    END IF;

    IF fila_detalle.movimiento = 'ENTRADA' OR fila_detalle.movimiento = 'TRASPASO (ENTRADA)' THEN
      saldo = fila_detalle.existencia + saldo;
    END IF;
    IF fila_detalle.movimiento = 'INVENTARIO INICIAL' THEN
      saldo = fila_detalle.existencia;
    END IF;
    IF fila_detalle.movimiento = 'SALIDA' OR fila_detalle.movimiento = 'TRASPASO (SALIDA)' THEN
      saldo = (fila_detalle.existencia * -1) + saldo;
    END IF;

    fila_detalle.existencia = saldo;

    id_producto = fila_detalle.producto_id;

    id_almacen = fila_detalle.almacen_id;

    -- RETURN NEXT fila_detalle;

   END LOOP;

   fila_productos.existencia = saldo;

   RETURN NEXT fila_productos;

 END LOOP;

 RETURN;

 END

$BODY$;

ALTER FUNCTION public.get_existencias(bigint, bigint, text, text)
    OWNER TO postgres;

-----------------------------------------


-- FUNCTION: public.getkardex(bigint, bigint, text, text)

-- DROP FUNCTION public.getkardex(bigint, bigint, text, text);

CREATE OR REPLACE FUNCTION public.getkardex(
  _almacen bigint,
  _producto bigint,
  fecha_inicio text,
  fecha_fin text)
    RETURNS SETOF vista_saldos 
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
    ROWS 1000
AS $BODY$
DECLARE

  status text := 'EJECUTADO';

  cadena_select text := 'SELECT inv_movimientos.id, inv_movimientos.tipo_id, inv_movimientos.folio, inv_movimientos.almacen_id, inv_movimientos.empresa_id, 

 inv_movimientos.status, inv_almacenes.nombre as almacen, inv_tipos_movimientos.nombre as movimiento, inv_movimientos_detalles.cantidad, 

 inv_movimientos_detalles.costo, inv_movimientos_detalles.producto_id, inv_productos.nombre as producto, inv_movimientos_detalles.created,

 inv_movimientos_detalles.cantidad as existencia, inv_movimientos.orden_id, inv_movimientos.remision_id

 FROM inv_movimientos

 inner join inv_almacenes on inv_almacenes.id = inv_movimientos.almacen_id

 inner join inv_tipos_movimientos on inv_tipos_movimientos.id = inv_movimientos.tipo_id

 inner join inv_movimientos_detalles on (inv_movimientos_detalles.movimiento_id = inv_movimientos.id or inv_movimientos_detalles.movimiento_id = inv_movimientos.movimiento_id)

 inner join inv_productos on inv_productos.id = inv_movimientos_detalles.producto_id';

 cadena_select_final text := ' order by almacen, producto, inv_movimientos.id,inv_tipos_movimientos.nombre';

  cadena_condiciones text := '';

 BEGIN

 -- cadena_condiciones := '';

 IF _almacen is not null THEN

  cadena_condiciones := cadena_condiciones || ' and almacen_id = ' || _almacen;

 END IF;

 IF _producto is not null THEN

  cadena_condiciones := cadena_condiciones || ' and producto_id = ' || _producto;

 END IF;

 IF fecha_inicio is not null AND fecha_inicio != '' THEN

  cadena_condiciones := format(cadena_condiciones || ' and inv_movimientos_detalles.created >= ''%s''',fecha_inicio);

 END IF;

 IF fecha_fin is not null AND fecha_fin != '' THEN

  cadena_condiciones := format(cadena_condiciones || ' and inv_movimientos_detalles.created <= ''%s''',fecha_fin);

 END IF;

 cadena_condiciones := format(cadena_condiciones || ' and inv_movimientos.status = ''%s''', status);

 RETURN query EXECUTE cadena_select || cadena_condiciones || cadena_select_final;

 END

$BODY$;

ALTER FUNCTION public.getkardex(bigint, bigint, text, text)
    OWNER TO postgres;

--------------------------------------------------------------------------------

-- FUNCTION: public.getproductos_by_almacen(bigint, bigint)

-- DROP FUNCTION public.getproductos_by_almacen(bigint, bigint);

CREATE OR REPLACE FUNCTION public.getproductos_by_almacen(
  _almacen bigint,
  _producto bigint)
    RETURNS SETOF vista_productos 
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
    ROWS 1000
AS $BODY$
DECLARE

  status text := 'EJECUTADO';

  cadena_select text := 'select distinct inv_productos.id, inv_tipos_articulos.codigo as codigo_categoria, 

  inv_lineas.codigo as codigo_linea,inv_productos.codigo as codigo_producto, inv_productos.nombre, 

  inv_tipos_articulos.nombre AS categoria, inv_lineas.nombre AS linea, 

  inv_tipos_presentaciones.nombre AS presentacion, 0.00 as existencia

from inv_productos 

inner JOIN inv_tipos_articulos ON inv_tipos_articulos.id = inv_productos.tipo_id

inner JOIN inv_lineas ON inv_lineas.id = inv_productos.linea_id

inner JOIN inv_tipos_presentaciones ON inv_tipos_presentaciones.id = inv_productos.presentacion_id';

 cadena_select_final text := ' order by inv_productos.nombre';

 cadena_condiciones text := '';

 BEGIN

 -- cadena_condiciones := '';

 IF _almacen is not null OR _producto is not null THEN

  cadena_condiciones := cadena_condiciones || ' inner join inv_movimientos_detalles on inv_productos.id = inv_movimientos_detalles.producto_id

inner join inv_movimientos on (inv_movimientos.id = inv_movimientos_detalles.movimiento_id or inv_movimientos.movimiento_id = inv_movimientos_detalles.movimiento_id)';

  cadena_condiciones := format(cadena_condiciones || ' and inv_movimientos.status = ''%s''', status);

 END IF;

 IF _almacen is not null THEN

  cadena_condiciones := cadena_condiciones || ' and almacen_id = ' || _almacen;

 END IF;

 IF _producto is not null THEN

  cadena_condiciones := cadena_condiciones || ' and producto_id = ' || _producto;

 END IF;

 RETURN query EXECUTE cadena_select || cadena_condiciones || cadena_select_final;

 END

$BODY$;

ALTER FUNCTION public.getproductos_by_almacen(bigint, bigint)
    OWNER TO postgres;

---------------------------------------------------------------

-- FUNCTION: public.getsaldos(bigint, bigint, text, text)

-- DROP FUNCTION public.getsaldos(bigint, bigint, text, text);

CREATE OR REPLACE FUNCTION public.getsaldos(
  _almacen bigint,
  _producto bigint,
  fecha_inicio text,
  fecha_fin text)
    RETURNS SETOF vista_saldos 
    LANGUAGE 'plpgsql'

    COST 100
    VOLATILE 
    ROWS 1000
AS $BODY$DECLARE

  fila_detalle RECORD;

  id_producto numeric;

  id_almacen numeric;

  saldo numeric;

 BEGIN

 saldo = 0.00;

 id_producto = 0;

 id_almacen = 0;

 FOR fila_detalle IN (select * from getkardex(_almacen, _producto, fecha_inicio, fecha_fin)) LOOP

  IF (id_producto != fila_detalle.producto_id OR id_almacen != fila_detalle.almacen_id) THEN

		saldo = 0.00;
    
  END IF;

  IF fila_detalle.movimiento = 'ENTRADA' OR fila_detalle.movimiento = 'TRASPASO (ENTRADA)' THEN
    saldo = fila_detalle.existencia + saldo;
  END IF;
  IF fila_detalle.movimiento = 'INVENTARIO INICIAL' THEN
    saldo = fila_detalle.existencia;
  END IF;
  IF fila_detalle.movimiento = 'SALIDA' OR fila_detalle.movimiento = 'TRASPASO (SALIDA)' THEN
    saldo = (fila_detalle.existencia * -1) + saldo;
  END IF;

  fila_detalle.existencia = saldo;

  id_producto = fila_detalle.producto_id;

  id_almacen = fila_detalle.almacen_id;

  RETURN NEXT fila_detalle;

 END LOOP;

 RETURN;

 END

$BODY$;

ALTER FUNCTION public.getsaldos(bigint, bigint, text, text)
    OWNER TO postgres;
