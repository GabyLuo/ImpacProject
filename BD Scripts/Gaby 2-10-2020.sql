CREATE SEQUENCE public.pro_ordenes_detalles_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.pro_ordenes_detalles
(
    id bigint NOT NULL DEFAULT nextval('pro_ordenes_detalles_id_seq'::regclass),
    orden_id bigint,
    producto_id bigint,
    cantidad numeric(15),
    PRIMARY KEY (id)
)