-- 26/08/2019 17:08

ALTER TABLE public.pmo_gastos
    ADD COLUMN categoria character varying(8);

    update pmo_gastos set categoria = 'MODERADO', modified_by = null, fecha_ejercido = null, status = 'SOLICITADO';


-- 28/08/2019 17_03

CREATE SEQUENCE public.num_solicitud_gasto_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

ALTER SEQUENCE public.num_solicitud_gasto_seq
    OWNER TO postgres;

ALTER TABLE public.pmo_gastos
    ADD COLUMN num_solicitud bigint DEFAULT nextval('num_solicitud_gasto_seq'::regclass);

    ALTER TABLE public.sys_logs
    ADD COLUMN log t_description;


-- 29/08/2019 13:18

ALTER TABLE pmo_gastos DROP COLUMN num_solicitud;

-- 30/08/2019 20:25
ALTER TABLE lic_licitacion
    ADD COLUMN kickoff timestamp without time zone

    CREATE TABLE lic_requerimientos
(
    id bigint NOT NULL,
    requerimiento_id bigint NOT NULL,
    licitacion_id bigint NOT NULL,
    tipo t_midname NOT NULL,
    documento_id bigint NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (requerimiento_id)
        REFERENCES exp_requisitos (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    FOREIGN KEY (licitacion_id)
        REFERENCES lic_licitacion (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    FOREIGN KEY (documento_id)
        REFERENCES sys_documents (id) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
);

ALTER TABLE lic_requerimientos
    OWNER to postgres;


    ALTER TABLE lic_licitacion
    ADD COLUMN fecha_termino date;


----------------------------------------------------------

-- 3/09/2019

ALTER TABLE lic_requerimientos
    ADD COLUMN created timestamp without time zone DEFAULT now();

ALTER TABLE lic_requerimientos
    ADD COLUMN created_by bigint;

ALTER TABLE lic_requerimientos
    ADD COLUMN modified timestamp without time zone DEFAULT now();

ALTER TABLE lic_requerimientos
    ADD COLUMN modified_by bigint;


CREATE SEQUENCE lic_requerimientos_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;

ALTER SEQUENCE lic_requerimientos_id_seq
    OWNER TO postgres;

ALTER TABLE lic_requerimientos
    ALTER COLUMN id SET DEFAULT nextval('lic_requerimientos_id_seq'::regclass);

ALTER TABLE lic_requerimientos
    ADD COLUMN descripcion t_description;

-- 04/09/2019

ALTER TABLE lic_licitacion
    RENAME titulo TO procedimiento;

    ALTER TABLE lic_licitacion
    RENAME fecha_firma TO fecha_inicio;

    ALTER TABLE lic_licitacion
    ADD COLUMN status t_midname;

ALTER TABLE lic_licitacion
    ADD COLUMN entidad_id bigint;

ALTER TABLE lic_licitacion
    ADD COLUMN descripcion t_description;

ALTER TABLE lic_licitacion
    ADD COLUMN contrato t_midname;

ALTER TABLE lic_licitacion
    ADD COLUMN monto t_amount;
ALTER TABLE lic_licitacion
    ADD CONSTRAINT lic_licitacion_estado_id_fkey FOREIGN KEY (entidad_id)
    REFERENCES vnt_estado (id) MATCH SIMPLE
    ON UPDATE NO ACTION
    ON DELETE NO ACTION;

    ALTER TABLE lic_licitacion
    ALTER COLUMN monto SET DEFAULT 0;

    ALTER TABLE lic_licitacion
    ADD UNIQUE (folio);

    ALTER TABLE lic_requerimientos
    ADD COLUMN nombre_referencia character varying(50);

    -- 12/09/2019 

    ALTER TABLE public.pmo_gastos
    ALTER COLUMN status DROP NOT NULL;