ALTER TABLE public.lic_licitacion
    ADD COLUMN responsable text;

ALTER TABLE public.lic_licitacion
    ADD COLUMN responsable_gdp text;

CREATE SEQUENCE public.vnt_sucursales_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

CREATE TABLE public.vnt_sucursales
(
    id bigint NOT NULL DEFAULT nextval('vnt_sucursales_id_seq'::regclass),
    empresa_id bigint NOT NULL,
    nombre text,
    encargado text,
    telefono t_midname,
    fax t_midname,
    tipo t_name NOT NULL,
    calle t_name,
    num_ext t_midname,
    num_int t_midname,
    colonia t_name,
    poblacion t_name,
    cp t_postal,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    tipo_oficina t_midname NOT NULL,
    tipo_propiedad t_midname NOT NULL,
    estado_id bigint,
    municipio_id bigint,
    CONSTRAINT vnt_sucursales_pkey PRIMARY KEY (id),
    CONSTRAINT vnt_sucursales_empresa_id_fkey FOREIGN KEY (empresa_id)
        REFERENCES public.vnt_empresa (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT vnt_sucursales_estado_id_fkey FOREIGN KEY (estado_id)
        REFERENCES public.vnt_estado (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT vnt_sucursales_municipio_id_fkey FOREIGN KEY (municipio_id)
        REFERENCES public.vnt_municipio (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
);

ALTER TABLE public.cmp_compras
    ADD COLUMN empresa_id bigint;

ALTER TABLE public.cmp_compras
    ADD COLUMN condiciones_pago text;

ALTER TABLE public.cmp_compras
    ADD COLUMN condiciones_entrega text;

ALTER TABLE public.cmp_compras
    ADD COLUMN transporte text;

ALTER TABLE public.cmp_compras
    ADD COLUMN entrega t_shortname;

ALTER TABLE public.cmp_compras
    ADD COLUMN direccion_id bigint;

ALTER TABLE public.cmp_compras
    ADD COLUMN sucursal_id bigint;

ALTER TABLE public.cmp_compras
    ADD COLUMN direccion_entrega text;

update cmp_compras set direccion_id = null, sucursal_id = null;

ALTER TABLE public.cmp_compras
    ADD COLUMN observaciones text;

ALTER TABLE public.cmp_compras
    ADD COLUMN observaciones text;