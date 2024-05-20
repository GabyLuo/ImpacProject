-- 20/08/2019 Mendoza Vargas Gabriela
--Creación de tabla pmo_proveedores, secuencia para id y construcción de llave foránea para pmo_gastos
-- Table: public.pmo_proveedores

-- DROP TABLE public.pmo_proveedores;

CREATE TABLE public.pmo_proveedores
(
    id bigint NOT NULL,
    nombre_comercial t_name COLLATE pg_catalog."default" NOT NULL,
    razon_social t_name COLLATE pg_catalog."default" NOT NULL,
    rfc t_rfc COLLATE pg_catalog."default" NOT NULL,
    curp t_curp COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT pmo_proveedores_pkey PRIMARY KEY (id),
    CONSTRAINT pmo_proveedores_curp_key UNIQUE (curp)
,
    CONSTRAINT pmo_proveedores_nombre_comercial_key UNIQUE (nombre_comercial)
,
    CONSTRAINT pmo_proveedores_rfc_key UNIQUE (rfc)

)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.pmo_proveedores
    OWNER to postgres;


ALTER TABLE public.pmo_gastos
    ADD COLUMN proveedor_id bigint;

ALTER TABLE public.pmo_gastos
    ADD FOREIGN KEY (proveedor_id)
    REFERENCES public.pmo_proveedores (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;
	
	
	CREATE SEQUENCE public.pmo_proveedores_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    CACHE 1;

ALTER SEQUENCE public.pmo_proveedores_id_seq
    OWNER TO postgres;


ALTER TABLE public.pmo_proveedores
    ALTER COLUMN id SET DEFAULT nextval('pmo_proveedores_id_seq'::regclass);


/*
	Cambios
    Micaela
	20-08-2019  4:59 p.m.

*/

alter table vnt_contrato add column num_contrato bigint;
ALTER TABLE vnt_contrato ADD CONSTRAINT vnt_contrato_num_contrato_key UNIQUE (num_contrato);


/*
	Cambios
    Micaela
	21-08-2019  3:12 p.m.

*/

ALTER TABLE pmo_proveedores
ADD COLUMN created timestamp without time zone DEFAULT now();
ALTER TABLE pmo_proveedores
ADD COLUMN created_by bigint;
ALTER TABLE pmo_proveedores
ADD COLUMN modified timestamp without time zone DEFAULT now();
ALTER TABLE pmo_proveedores
ADD COLUMN modified_by bigint;

CREATE SEQUENCE public.exp_requisitos_id_seq;
create table exp_requisitos (
	id bigint not null default nextval('exp_requisitos_id_seq'::regclass),
	nombre t_midname not null,
	tipo t_midname not null,
	tramite t_name not null,
	created timestamp without time zone DEFAULT now(),
	created_by bigint,
	modified timestamp without time zone DEFAULT now(),
	modified_by bigint,
	primary key (id),
	unique(nombre)
);


/*
	Cambios
    Micaela
	21-08-2019  4:55 p.m.

*/

ALTER TABLE exp_requisitos ALTER COLUMN nombre SET DATA TYPE t_name;


/*
	Cambios micaela
	21-08-2019  5:21 p.m.

*/

CREATE SEQUENCE public.vnt_padron_requisitos_id_seq;
create table vnt_padron_requisitos (
	id bigint not null default nextval('vnt_padron_requisitos_id_seq'::regclass),
	padron_id bigint not null,
	requisito_id bigint,
	documento_id bigint,
	created timestamp without time zone DEFAULT now(),
	created_by bigint,
	modified timestamp without time zone DEFAULT now(),
	modified_by bigint,
	primary key (id),
	foreign key(padron_id) references vnt_proveedor(id),
	foreign key(requisito_id) references exp_requisitos(id),
	foreign key(documento_id) references sys_documents(id)
);