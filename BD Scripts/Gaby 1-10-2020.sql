ALTER TABLE public.inv_movimientos
    ADD COLUMN movimiento_id bigint;

ALTER TABLE public.inv_almacenes
    ADD COLUMN tipo t_midname;

CREATE SEQUENCE public.inv_bom_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.inv_bom
(
    id bigint NOT NULL DEFAULT nextval('inv_bom_id_seq'::regclass),
    producto_id bigint,
    material_id bigint,
    cantidad numeric(15),
    PRIMARY KEY (id)
);

ALTER TABLE public.inv_productos
    ADD COLUMN numero_parte t_name;

-- ALTER TABLE public.inv_bom ADD COLUMN almacen_id bigint;
-- 2da ronda de cambios

CREATE SEQUENCE public.pro_ordenes_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.pro_ordenes
(
    id bigint NOT NULL DEFAULT nextval('pro_ordenes_id_seq'::regclass),
    folio t_midname,
    fecha_produccion date,
    cliente_id bigint,
    status t_midname,
    almacen_id_origen bigint,
    almacen_id_destino bigint,
    empresa_id_origen bigint,
    empresa_id_destino bigint,
    PRIMARY KEY (id)
);