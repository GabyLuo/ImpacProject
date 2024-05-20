CREATE SEQUENCE public.com_aliados_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.com_aliados
(
    id bigint NOT NULL DEFAULT nextval('com_aliados_id_seq'::regclass),
    apellido_paterno t_nombre,
    apellido_materno t_nombre,
    nombre t_nombre,
    rfc t_rfc,
    area t_name,
    estado_id bigint,
    municipio_id bigint,
    descripcion t_nombre,
    PRIMARY KEY (id)
);

CREATE SEQUENCE public.com_comisiones_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.com_comisiones
(
    id bigint NOT NULL DEFAULT nextval('com_comisiones_id_seq'::regclass),
    proyecto_id bigint,
    aliado_id bigint,
    tipo t_midname,
    porcentaje numeric(5, 2),
    monto t_amount,
    metodo_pago t_midname,
    PRIMARY KEY (id)
);

ALTER TABLE public.com_comisiones
    ADD COLUMN contrato_id bigint;

ALTER TABLE public.com_comisiones
    ADD COLUMN iva t_midname;

ALTER TABLE public.com_comisiones
    ADD COLUMN aplicado t_midname;

ALTER TABLE public.com_comisiones
    ADD COLUMN tiempo_pago t_midname;

ALTER TABLE public.com_comisiones
    ADD COLUMN observaciones t_description;

ALTER TABLE public.pro_ordenes
    ADD COLUMN tipo t_shortname DEFAULT 'PRODUCCIÓN';

ALTER TABLE public.pro_ordenes DROP COLUMN tipo;

ALTER TABLE public.pro_ordenes
    ADD COLUMN tipo t_midname DEFAULT 'PRODUCCIÓN';